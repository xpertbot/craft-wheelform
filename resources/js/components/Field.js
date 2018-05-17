import React from 'react';

class Field extends React.Component{

  constructor(props) {
    super(props);
    this.state = {
      name: '',
      required: 0,
      type: '',
      index_view: 0,
      active: 0,
      order: 0,
    };
  }

  static getDerivedStateFromProps(nextProps, prevState)
  {
    console.log(nextProps);
    return nextProps.field;
  }

  render() {
    return (
      <div className="wheelform-field">
        <h4>{this.state.name}</h4>
        <div className="meta subheading"><span>{this.state.type}</span></div>
        <div className="meta">Required: <span>{this.state.require ? 'true' : 'false'}</span></div>
        <div className="meta">Active: <span>{this.state.active ? 'true' : 'false'}</span></div>
        <div className="meta">Index View: <span>{this.state.index_view ? 'true' : 'false'}</span></div>
      </div>
    )
  }
}

export default Field;
