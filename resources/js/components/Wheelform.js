import React, {Component} from 'react';
import {
  SortableContainer,
  SortableElement,
  SortableHandle,
  arrayMove,
} from 'react-sortable-hoc';
import axios from 'axios';

const DragHandle = SortableHandle(() => <span>::</span>);

const SortableItem = SortableElement((field) => {
  return (
    <li>
      <DragHandle />
      {field.name}
    </li>
  );
});

const SortableList = SortableContainer((fields) => {
  return (
    <ul>\
      {console.log(fields)}
      {fields.map((value, index) => {
        <SortableItem key={'item-'+index} index={index} value={value} />
      })}
    </ul>
  );
});

class Wheelform extends Component
{
  constructor() {
    super();

    this.state = {
      fields: []
    };

    this.onSortEnd = (oldIndex, newIndex) => {
      const fields = this.state.fields;

      this.setState({
        fields: arrayMove(fields, oldIndex, newIndex),
      });
    };
  };

  componentDidMount() {
    const cpUrl = window.Craft.baseCpUrl;
    const form_id = window.Wheelform.form_id;

    axios.get(cpUrl, {
      params: {
        action: 'wheelform/form/get-fields',
        form_id: form_id
      }
    })
      .then((res) => {
        this.setState({ 'fields': res.data });
      })
  }

  render(){
    return (
      <SortableList fields={this.state.fields} onSortEnd={this.onSortEnd} useDragHandle={true} />
    )
  }
}

export default Wheelform;
