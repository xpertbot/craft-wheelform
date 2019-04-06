<template>
<div>
    <div v-if="loading">
        <i class="fas fa-spinner fa-spin"></i>
    </div>
    <div v-else>
        <div class="row">
            <div class="col-sm-6">
                <Settings
                    :form="form"
                    v-on:handle-form-setting="handleSettingsInput"
                    v-on:handle-form-option-change="handleFormOptionChange"
                    v-on:handle-save-settings="handleSaveSettings"
                />
            </div>
            <div class="col-sm">
                <div class="btn-container">
                    <button v-on:click.prevent="addField" style="margin-bottom: 15px" class="btn submit">{{ 'Add Field' | t('wheelform')}}</button>
                    <button v-show="form.fields.length > 0"
                        v-on:click.prevent="handleEditMode" class="btn primary pull-right">
                        {{ (isEditMode ? "Drag" : "Edit") + ' Fields' | t('wheelform')}}
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
                        :send-notification="form.options.user_notification"
                        v-on:handle-user-notification-field="handleUserNotificationField"
                        v-on:handle-reply-to-field="handleReplyToField"
                        :fieldTypes="fieldTypes"
                    />
                </draggable>
            </div>
        </div>
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
            loading: true,
            isEditMode: false,
            nextFieldIndex: 0,
            fieldTypes: [],
            form: {
                id: null,
                name: "",
                to_email: "",
                active: 1,
                save_entry: 1,
                recaptcha: 0,
                send_email: 1,
                fields: [],
                options: this.getDefaultFormOptions(),
            },
        }
    },
    mounted()
    {
        const cpUrl = window.Craft.baseCpUrl;
        const form_id = window.Wheelform.form_id;

        if (! form_id) {
            this.loading = false;
        } else {
            axios.get(cpUrl, {
                params: {
                    action: 'wheelform/form/get-settings',
                    form_id: form_id
                }
            })
            .then((res) => {
                if(res.data) {
                    const data = JSON.parse(res.data);
                    const form = data.form;
                    this.fieldTypes = data.fieldTypes;
                    if(form) {
                        form.options = Object.assign(this.getDefaultFormOptions(), JSON.parse(form.options));
                        //parse fields
                        for (let index = 0; index < form.fields.length; index++) {
                            let options = form.fields[index].options ? JSON.parse(form.fields[index].options) : {};

                            form.fields[index].isValidName = {
                                status: true,
                                msg: ''
                            };
                            form.fields[index].uniqueId = this.generateKeyId();
                            form.fields[index].options = Object.assign(this.getDefaultFieldOptions(), options);
                        }
                        this.nextFieldIndex = form.fields.length;
                        this.form = form;
                    }
                }
                this.loading = false;
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
                options: this.getDefaultFieldOptions(),
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
                    msg: Craft.t('wheelform', 'Name is not unique')
                }
            }

            if(userInput.indexOf(' ') >= 0){
                return {
                    status: false,
                    msg: Craft.t('wheelform', 'Name contains whitespaces')
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

        //Getters
        getDefaultFormOptions()
        {
            return {
                honeypot: "",
                email_subject: "",
                user_notification: 0,
            };
        },
        getDefaultFieldOptions()
        {
            return {
                validate: 0,
                label: '',
                items: [],
                containerClass: '',
                fieldClass: '',
                selectEmpty: 0,
                placeholder: '',
                is_reply_to: false,
                is_user_notification_field: false,
            };
        },

        // Handles
        handleSettingsInput(key, value) {
            this.form[key] = value;
        },
        handleFormOptionChange(option, value)
        {
            this.form.options[option] = value;
        },
        handleUserNotificationField(toIndex, value)
        {
            this.form.fields.map((field, i) => {
                if(i == toIndex) {
                    field.options.is_user_notification_field = value;
                } else if(field.type == 'email') {
                    field.options.is_user_notification_field = false;
                }
            });
        },
        handleReplyToField(toIndex, value)
        {
            this.form.fields.map((field, i) => {
                if(i == toIndex) {
                    field.options.is_reply_to = value;
                } else if(field.type == 'email') {
                    field.options.is_reply_to = false;
                }
            });
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
                        this.form.id = res.data.form_id;
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
