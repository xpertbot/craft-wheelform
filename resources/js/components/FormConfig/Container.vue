<template>
    <div class="row">
        <div class="col-sm-6">
            <Settings
                :form="form"
                v-on:handle-form-setting="handleSettingsInput"
                v-on:handle-save-settings="handleSaveSettings"
            />
        </div>
        <div class="col-sm">
            <div class="btn-container">
                <button v-on:click.prevent="addField" style="margin-bottom: 15px" class="btn submit">Add  Field</button>
                <button v-show="form.fields.length > 0"
                    v-on:click.prevent="handleEditMode" class="btn primary pull-right">
                    {{isEditMode ? "Drag" : "Edit"}} Fields
                </button>
            </div>
            <draggable v-model="form.fields" :options="{handle: '.wheelform-field-handle'}" @end="onDragEnd"
                id="field-container">
                <Field
                    v-for="(field, index) in form.fields"
                    :key="field.uniqueId"
                    :index="index"
                    :default-field="field"
                    :is-edit-mode="isEditMode"
                    @delete-field="form.fields.splice(index, 1)"
                    :validate-name-callback="validateFieldName"
                    :update-field-property-callback="updateFieldProperty"
                />
            </draggable>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import draggable from 'vuedraggable';
import toastr from 'toastr';
import Field from './Field.vue';
import Settings from './Settings.vue';

export default {
    components:{
        draggable,
        Field,
        Settings,
    },
    data() {
        return {
            isEditMode: false,
            nextFieldIndex: 0,
            form: {
                name: "",
                to_email: "",
                active: 1,
                save_entry: 1,
                recaptcha: 0,
                send_email: 1,
                fields: [],
                options: {
                    honeypot: "",
                }
            },
        }
    },
    mounted()
    {
        const cpUrl = window.Craft.baseCpUrl;
        const form_id = window.Wheelform.form_id;

        if (form_id) {
            axios.get(cpUrl, {
                params: {
                    action: 'wheelform/form/get-settings',
                    form_id: form_id
                }
            })
            .then((res) => {
                if(res.data) {
                    const form = JSON.parse(res.data);
                    if(form) {
                        form.options = form.options ? JSON.parse(form.options) : {};
                        //parse fields
                        for (let index = 0; index < form.fields.length; index++) {
                            let options = form.fields[index].options ? JSON.parse(form.fields[index].options) : {};

                            form.fields[index].isValidName = {
                                status: true,
                                msg: ''
                            };
                            form.fields[index].uniqueId = this.generateKeyId();
                            form.fields[index].options = this.mergeFieldOptions(options);
                        }
                        this.nextFieldIndex = form.fields.length;
                        this.form = form;
                    }
                }

            });
        }
    },
    methods: {
        addField()
        {
            this.nextFieldIndex++;

            this.form.fields.push({
                name: "field_" + this.nextFieldIndex,
                order: this.nextFieldIndex,
                type: "text",
                index_view: 0,
                active: 1,
                required: 0,
                uniqueId: this.generateKeyId(),
                isValidName: {
                    status: true,
                    msg: ''
                },
                options: this.mergeFieldOptions({}),
            });
        },
        onDragEnd()
        {
            let fields = this.form.fields.map(function(item, index) {
                return item.order = index + 1;
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
            let result = this.form.fields.filter((field) => {
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
        updateFieldProperty(index, property, value)
        {
            this.form.fields[index][property] = value;
        },
        mergeFieldOptions(options)
        {
            const booleanProperties = [
                'validate',
                'selectEmpty'
            ];

            const defaultOptions = {
                validate: 0,
                label: '',
                items: [],
                containerClass: '',
                fieldClass: '',
                selectEmpty: 0,
                placeholder: '',
            };

            for(let property in defaultOptions) {
                if(! options.hasOwnProperty(property)) {
                    //Default Option values
                    options[property] = defaultOptions[property];
                } else {
                    //check if booleanProperty
                    if(options[property] in booleanProperties) {
                        options[property] = parseInt(options[property]);
                    }
                }
            }
            return options;
        },
        handleSettingsInput(key, value) {
            this.form[key] = value;
        },
        handleSaveSettings()
        {
            const cpUrl = window.Craft.baseCpUrl;

            let headers = {
                'Content-Type': 'application/json',
                'X-CSRF-Token': Craft.csrfTokenValue,
            };

            axios.post(cpUrl + "/wheelform/form/save", this.form, {headers: headers})
                .then((res) => {
                    const success = res.data.success;
                    if(success) {
                        toastr.success('Success', res.data.message);
                    } else {
                        let msg = "";
                        const errors = res.data.errors;
                        for(let prop in errors) {
                            msg += errors[prop] + "<br/>";
                        }
                        toastr.error("Error", msg);
                    }
                }).catch((error) => {
                    console.log(error);
                });
        }
    }
}
</script>
