import React from 'react';
import Text from './fields/Text';
import axios from 'axios';

class Fields extends React.Component{

  constructor(props) {
    super(props);
    this.state = {
      fields: [],
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
      <div>
      {
        this.state.fields.map((field) => {
          switch(field.type){
            default:
              return (
                <Text
                  key={field.name}
                  value={field.name}
                  required={field.required}
                  indexView={field.index_view}
                />
              )
            break;
          }

        })
      }
      </div>
      )
  }
}

export default Fields;
