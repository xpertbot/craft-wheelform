import React from 'react';
import update from 'immutability-helper';
import { DragDropContext } from 'react-dnd';
import HTML5Backend from 'react-dnd-html5-backend';
import axios from 'axios';
import Field from "./Field";

const style = {
  width: 450,
}

class Wheelform extends React.Component{

  constructor(props)
  {
    super(props);

    this.moveField = this.moveField.bind(this);

    this.state = {
      fields: [],
    }
  }

  moveField(dragIndex, hoverIndex)
  {
    const {fields} = this.state;
    const dragField = fields[dragIndex];

    this.setState(
      update(this.state, {
          fields: {
            $splice: [[dragIndex, 1], [hoverIndex, 0, dragField]],
          }
      })
    );
  }

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
        this.setState((prevState, props) => {
          return { 'fields': res.data }
        });
      });
  }

  render()
  {
    const fields = this.state.fields;

    return (
      <div style={style}>
        {fields.map((field, i) => {
          return (
            <Field
              key={field.name}
              index={i}
              name={field.name}
              id={field.id}
              type={field.type}
              indexView={field.indexView}
              active={field.active}
              required={field.required}
              moveField={this.moveField}
            />
          )
        })}
      </div>
    );
  }
}

export default DragDropContext(HTML5Backend)(Wheelform);
