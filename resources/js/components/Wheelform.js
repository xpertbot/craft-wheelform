import React from 'react';
import Field from './Field';
import axios from 'axios';

class Wheelform extends React.Component
{

  constructor()
  {
    super();

    this.state = {
      fields: []
    };
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
        this.setState({ 'fields': res.data });
      })
  }

  render() {
    return (
      <div id="field-container">
        {this.state.fields.map((field, index) => {
            return (
            <Field
              key={field.name}
              field={field}
            />
            )
          })}
      </div>
    );
  }
}

export default Wheelform;
