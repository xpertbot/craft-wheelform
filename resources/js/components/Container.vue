<template>
    <div id="formapp">
        <div class="btn-container">
            <button v-on:click.prevent="addField" style="margin-bottom: 15px" class="btn submit">Add  Field</button>
            <button v-show="fields.length > 0" v-on:click.prevent="handleEditMode" class="btn primary pull-right">{{isEditMode ? "Drag" : "Edit"}} Fields</button>
        </div>
        <draggable v-model="fields" :options="{handle: '.wheelform-field-handle'}" id="field-container">
              <Field
               v-for="(field, index) in fields"
               :key="field.uniqueId"
               :index="index"
               :order="index + 1"
               :default-field="field"
               :is-edit-mode="isEditMode"
               @delete-field="fields.splice(index, 1)"
               :validate-name-callback="validateFieldName"
              />
        </draggable>
    </div>
</template>

<script>
import axios from 'axios';
import draggable from 'vuedraggable';
import Field from './Field.vue';

export default {
    components:{
        draggable,
        Field
    },
    data() {
        return {
            isEditMode: false,
            fields: [],
            nextFieldIndex: 0,
        }
    },
    mounted()
    {
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
                if(res.data.length > 0)
                {
                    for (let index = 0; index < res.data.length; index++) {
                        let field = res.data[index];
                        field.uniqueId = this.generateKeyId();
                        field.isValidName= {
                            status: true,
                            msg: ''
                        };
                        if(field.options.length == 0)
                        {
                            field.options = {
                                validate: false,
                                items: [],
                            }
                        }
                        this.fields.push(field);
                    }
                    this.nextFieldIndex = this.fields.length;
                }
            });
        }
    },
    methods: {
        addField()
        {
            this.nextFieldIndex++

            this.fields.push({
                name: "field_" + this.nextFieldIndex,
                type: "text",
                index_view: 0,
                active: 1,
                required: 0,
                uniqueId: this.generateKeyId(),
                isValidName: {
                    status: true,
                    msg: ''
                },
                options: {
                    items: [],
                    validate: false,
                }
            });
        },
        handleEditMode()
        {
            this.isEditMode = !this.isEditMode;
        },
        generateKeyId()
        {
            return '_' + Math.random().toString(36).substr(2, 9);
        },
        validateFieldName(userInput)
        {
            let result = this.fields.filter((field) => {
                return field.name === userInput;
            });

            if(result.length > 0)
            {
                return {
                    status: false,
                    msg: 'Name is not unique'
                }
            }

            if(userInput.indexOf(' ') >= 0){
                return {
                    status: false,
                    msg: 'Name contains whitespaces'
                }
            }

            return {
                status: true,
                msg: ''
            }
        },
    }
}
</script>

<style>

</style>

