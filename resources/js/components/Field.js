import React from 'react';
import { findDOMNode } from 'react-dom';
import { DragSource, DropTarget } from 'react-dnd';
import { flow } from 'lodash';

const style = {
  border: '1px dashed blue',
  padding: '0.5rem 1rem',
  marginBottom: '.5rem',
  backgroundColor: 'white',
}

const handleStyle = {
  color: "#994032",
  marginRight: "10px",
  cursor: 'move',
  position: 'absolute',
  left: '5px',
  top: '5px',
  zIndex: 10,
}

const ellipsisSource = {
  beginDrag(props)
  {
    return {
      id: props.id,
      index: props.index,
    }
  }
}

const fieldTarget = {
  hover(props, monitor, component)
  {
    const dragIndex = monitor.getItem().index;
    const hoverIndex = props.index;

    //Don't replace items with themselves
    if(dragIndex == hoverIndex) {
      return;
    }

    //Determine rectangle on screen
    const hoverBoundingRect = findDOMNode(component).getBoundingClientRect();

    //get vertical middle
    const hoverMiddleY = (hoverBoundingRect.bottom - hoverBoundingRect.top) / 2;

    // Determine mouse position
    const clientOffset = monitor.getClientOffset();

    //get pixles on top
    const hoverClientY = clientOffset.y - hoverBoundingRect.top;

    // Only perform the move when the mouse has crossed half of the items height
		// When dragging downwards, only move when the cursor is below 50%
    // When dragging upwards, only mo ve when the cursor is above 50%

    //Draggin Downwards
    if(dragIndex < hoverIndex && hoverClientY < hoverMiddleY) {
      return;
    }

    //Draggin Upwards
    if(dragIndex > hoverIndex && hoverClientY > hoverMiddleY) {
      return;
    }

    // Time to actually perform the action
    props.moveField(dragIndex, hoverIndex);

    // Note: We're mutating the monitor item here!
    // Generally it's better to avoid mutations,
    // but it's good here for the sake of performance
    // to avoid expensive index searches.
    monitor.getItem().index = hoverIndex;
  }
}

class Field extends React.Component {

  constructor(props) {
    super(props);
    this.state = {
      isEditMode: false,
    }

    this.onEdit = this.onEdit.bind(this);
    this.handleChange = this.handleChange.bind(this);
  }

  onEdit(e)
  {
    e.preventDefault();

    this.setState((prevState, props) => {
      return {
        isEditMode: !prevState.isEditMode,
      }
    });
  }

  handleChange(e, name, status)
  {
    e.preventDefault();

    console.log(name);
    console.log(status);
    this.setState((prevState, props) => {
      return {
        [name]: ! status
      }
    })
  }

  createLightswitch(name, label, status)
  {
    return (
      <div>
        <div className="heading">{label}</div>
        <a href="" onClick={(e) => this.handleChange(e, name, !status)}>
          <i style={{color: "#00b007"}} className={"fa fa-toggle-" + (status ? 'on' : 'off')}></i>
        </a>
        <input type="hidden" name={name} value="1" />
      </div>
    );
  }

  render() {

    const opacity = this.props.isDragging ? 0 : 1;

    return (
      this.props.connectDropTarget(
        <div className="wheelform-field" style={{'opacity': opacity, position: 'relative'}}>
          {! this.state.isEditMode &&
            this.props.connectDragSource(<i className="fa fa-ellipsis-v" style={handleStyle}></i>)
          }
          <h4>
            {this.props.name}
            <a href="" onClick={this.onEdit}><i className="fa fa-edit"></i></a>
          </h4>
          <div className="meta subheading"><span>{this.props.type}</span></div>
          <div style={{display: this.state.isEditMode ? 'block' : 'none', paddingTop: '20px' }}>
            {this.createLightswitch('required', 'Required', this.props.required)}
            {this.createLightswitch('index_view', 'Index View', this.props.index_view)}
          </div>
        </div>
      )
    )
  }
}

export default flow(
  DropTarget('field', fieldTarget, (connect) => ({
    connectDropTarget: connect.dropTarget(),
  })),
  DragSource('ellipsis', ellipsisSource, (connect, monitor) => ({
    connectDragSource: connect.dragSource(),
    isDragging: monitor.isDragging()
  }))
)(Field);
