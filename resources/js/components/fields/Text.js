import React from 'react';

class Text extends React.Component{

  constructor(props){
    super(props);
  }

  render(){
    return (
      <div>
        <input type="text" name="" value={this.props.value} />
      </div>
      )
  }
}

export default Text;
