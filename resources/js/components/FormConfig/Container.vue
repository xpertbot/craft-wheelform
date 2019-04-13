<template>
<div class="settings-container">
    <div id="sidebar" class="sidebar" style="display:block;">
        <nav>
            <ul>
                <li>
                    <a @click.prevent="handleViewChange('form')" :class="activeTabClasses('form')">
                        <span class="label">{{ 'Form Settings' | t('wheelform')}}</span>
                    </a>
                </li>
                <li>
                    <a @click.prevent="handleViewChange('field')" :class="activeTabClasses('field')">
                        <span class="label">{{ 'Field Settings' | t('wheelform')}}</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <div id="content-container">
        <div id="content">
            <i class="fas fa-spinner fa-spin" v-show="currentView == 'loading'"></i>

            <Settings
                v-show="currentView == 'form'"
                :form="form"
                @handle-form-setting="handleSettingsInput"
                @handle-form-option-change="handleFormOptionChange"
            />

            <div v-show="currentView == 'field'" class="row">
                <div class="col-sm">
                    <h3>{{ 'Form Fields' |t('wheelform')}}</h3>
                    <draggable
                        :list="form.fields"
                        :handle="'.wheelform-field-handle'"
                        :group="'field-types'"
                        @end="onDragEnd"
                        class="field-container mb-20">
                        <Field
                            v-for="(field, index) in form.fields"
                            :key="index"
                            :index="index"
                            :name="field.name"
                            :required="field.required"
                            :index_view="field.index_view"
                            :options="field.options"
                            :config="field.config"
                            :type="field.type"
                            @delete-field="form.fields.splice(index, 1)"
                            @validate-name="validateFieldName"
                            @update-field-property="updateFieldProperty"
                            @update-field-option="handleFieldOptionChange"
                        />
                    </draggable>
                </div>
                <div class="col-sm-4">
                    <h3>{{ 'Field Types' |t('wheelform')}}</h3>
                    <draggable
                        :list="fieldTypes"
                        :group="{ name: 'field-types', pull: 'clone', put: false }"
                        :sort="false"
                        :clone="clone"
                        class="field-container"
                    >
                    <div class="field-type"
                        v-for="(fieldType, index) in fieldTypes"
                        :key="index"
                    >
                        {{ fieldType.name }}
                    </div>
                    </draggable>
                </div>
            </div>
            <div class="field action-buttons">
                <button @click.prevent="handleSaveSettings" class="btn submit">{{'Save'|t('wheelform')}}</button>
                <a :href="getBackUrl" class="btn primary">{{'Back'|t('wheelform')}}</a>
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
            nextFieldIndex: 0,
            fieldTypes: [],
            currentView: 'loading',
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
        this.fieldTypes = window.Wheelform.fieldTypes;

        if (! form_id) {
            this.currentView = 'form';
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
                    if(form) {
                        form.options = Object.assign(this.getDefaultFormOptions(), JSON.parse(form.options));
                        //parse fields
                        for (let index = 0; index < form.fields.length; index++) {
                            let options = form.fields[index].options ? JSON.parse(form.fields[index].options) : {};

                            // only get options that belong to that fieldType
                            const fieldType = this.fieldTypes.find((fieldType) => {
                                return (form.fields[index].type == fieldType.type);
                            });

                            form.fields[index].class = fieldType.class;
                            form.fields[index].config = fieldType.config;
                            form.fields[index].options = Object.assign(this.getOptionsFromConfig(fieldType.config), options);
                        }
                        this.nextFieldIndex = form.fields.length;
                        this.form = form;
                    }
                }
                this.currentView = 'form';
            });
        }
    },
    computed: {
        getBackUrl() {
            const  cpUrl = window.Craft.baseCpUrl;
            return cpUrl + "/wheelform";
        }
    },
    methods: {
        clone(fieldType) {
            let field = Object.assign({}, fieldType);
            field.options = this.getOptionsFromConfig(fieldType.config);
            return field;
        },
        onDragEnd()
        {
            let fields = this.form.fields.map(function(item, index) {
                return item.order = index + 1;
            });
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
        activeTabClasses(view) {
            return {
                sel: (view == this.currentView),
            }
        },
        getOptionsFromConfig(config) {
            let options = {};
            if(Array.isArray(config)) {
                config.map((config) => {
                    options[config.name] = config.value;
                });
            } else {
                options[config.name] = config.value;
            }

            return options;
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

        // Handles
        handleSettingsInput(key, value) {
            this.form[key] = value;
        },
        handleFormOptionChange(option, value)
        {
            this.form.options[option] = value;
        },
        handleFieldOptionChange(index, option, value)
        {
            this.form.fields[index].options[option] = value;
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
        },
        handleViewChange(view) {
            this.currentView = view;
        }
    }
}
</script>
