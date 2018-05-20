import React from 'react';
// import PropTypes from 'prop-types';
import { findDOMNode } from 'react-dom';
import { DragSource, DropTarget } from 'react-dnd';
import { flow } from 'lodash';

const style = {
  border: '1px dashed blue',
  padding: '0.5rem 1rem',
  marginBottom: '.5rem',
  backgroundColor: 'white',
  cursor: 'move',
}

const fieldSource = {
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

  //   this._propTypes = {
  //     connectDragSource: PropTypes.func.isRequired,
  //     connectDropTarget: PropTypes.func.isRequired,
  //     index: PropTypes.number.isRequired,
  //     isDragging: PropTypes.bool.isRequired,
  //     id: PropTypes.any.isRequired,
  //     name: PropTypes.string.isRequired,
  //     moveField: PropTypes.func.isRequired,
  //   }
  }

  // static get propTypes() {
  //   return this._propTypes;
  // }

  render() {

    return this.props.connectDragSource(
      this.props.connectDropTarget(
        <div className="wheelform-field">
          <h4>{this.props.name}</h4>
          <div className="meta subheading"><span>{this.props.type}</span></div>
          <div className="meta">Required: <span>{this.props.require ? 'true' : 'false'}</span></div>
          <div className="meta">Active: <span>{this.props.active ? 'true' : 'false'}</span></div>
          <div className="meta">Index View: <span>{this.props.index_view ? 'true' : 'false'}</span></div>
        </div>
      )
    )
  }
}

export default flow(
  DropTarget('field', fieldTarget, (connect) => ({
    connectDropTarget: connect.dropTarget(),
  })),
  DragSource('field', fieldSource, (connect, monitor) => ({
    connectDragSource: connect.dragSource(),
    isDragging: monitor.isDragging()
  }))
)(Field);
