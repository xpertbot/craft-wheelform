import React from 'react'
import update from 'immutability-helper'
import { DropTarget, DragDropContext } from 'react-dnd'
import HTML5Backend from 'react-dnd-html5-backend'
import axios from 'axios'
import Field from "./Field"

class Container extends React.Component {

  constructor(props) {
    super(props);

    this.state = {
      isEditMode: false,
      fields: [],
    }

    //Map Event functions
    this.moveField = this.moveField.bind(this);
    this.addField = this.addField.bind(this);
    this.handleEditMode = this.handleEditMode.bind(this);
  }

  moveField(dragIndex, hoverIndex) {
    const { fields } = this.state;
    const dragField = fields[dragIndex];

    this.setState(
      update(this.state, {
        fields: {
          $splice: [[dragIndex, 1], [hoverIndex, 0, dragField]],
        }
      })
    );
  }

  addField(e) {
    e.preventDefault();

    this.setState((prevState, props) => {
      let fieldIndex = (prevState.fields.length + 1);

      return prevState.fields.push({
        name: "field_" + fieldIndex,
        type: "text",
        index_view: false,
        active: false,
        required: false,
      })
    });
  }

  handleEditMode(e) {
    e.preventDefault();

    this.setState((prevState, props) => {
      return {
        isEditMode: !prevState.isEditMode,
      }
    });
  }

  componentDidMount() {
    const cpUrl = window.Craft.baseCpUrl;
    const form_id = window.Wheelform.form_id;

    if (form_id) {
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
  }

  render() {
    const fields = this.state.fields;

    return (
      <div>
        <div className="btn-container">
          <button onClick={this.addField} style={{ "marginBottom": 15 }} className="btn submit">Add  Field</button>
          <button onClick={this.handleEditMode} className="btn primary pull-right">{this.state.isEditMode ? "Drag" : "Edit"} Fields</button>
        </div>
        <div id="field-container">
          {fields.map((field, i) => {
            return (
              <Field
                key={field.name + "_" + i}
                index={i}
                field={field}
                moveField={this.moveField}
                isEditMode={this.state.isEditMode}
              />
            )
          })}
        </div>
      </div>
    );
  }
}

export default DragDropContext(HTML5Backend)(Container);
