import React from 'react';
import Text from './fields/Text';

class Fields extends React.Component{

    constructor(props) {
        super(props);
    }

    render() {
        return (
        <div>
            {
            this.props.fields.map((field) => {
                switch(field.options.type){
                    default:
                        return (
                            <Text
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
