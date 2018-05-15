import React from 'react';
import Fields from './Fields';

class Wheelform extends React.Component{

    constructor(props)
    {
        super(props);
        this.state = {
            fields: [
                {
                    options: {
                        'type': 'text',
                    }
                },
                {
                    options: {
                        'type': 'text',
                    }
                }
            ]
        }
    }

    render(){
        return (
        <div>
            <Fields
                fields={this.state.fields}
            />
        </div>
        );
    }
}

export default Wheelform;
