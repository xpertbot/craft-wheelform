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
        <div id="content" class="content-pane">
            <i class="fas fa-spinner fa-spin" v-show="currentView == 'loading'"></i>

            <Settings
                v-show="currentView == 'form'"
                :form="form"
                @handle-form-setting="handleSettingsInput"
                @handle-form-option-change="handleFormOptionChange"
            />

            <div v-show="currentView == 'field'" class="wheelform-container">
                <div class="row">
                    <div class="col-sm">
                        <h3>{{ 'Form Fields' |t('wheelform')}}</h3>
                        <div class="row">
                            <div class="col">
                                <a @click.prevent="handleCollapseExpandFields" class="btn primary pull-right" style="margin-bottom:10px;">{{ areExpanded ? "Collapse" : "Expand" | t('wheelform') }}</a>
                            </div>
                        </div>
                        <draggable
                            :list="form.fields"
                            :handle="'.wheelform-field-handle'"
                            :group="'field-types'"
                            @end="onDragEnd"
                            @add="onDragAdd"
                            class="field-container mb-20">

                            <component
                                v-for="(field, index) in form.fields"
                                :key="index"
                                :index="index"
                                :name="field.name"
                                :required="field.required"
                                :index_view="field.index_view"
                                :options="field.options"
                                :configuration="field.config"
                                :type="field.type"
                                :errors="field.errors"
                                :isActive="field.isActive"
                                @delete-field="form.fields.splice(index, 1)"
                                @update-field-property="updateFieldProperty"
                                @update-field-option="handleFieldOptionChange"
                                :is="field.fieldComponent">
                            </component>

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
            </div>
            <div class="field action-buttons wheelform-container">
                <div class="row">
                    <div class="col">
                        <button @click.prevent="handleSaveSettings" class="btn submit">{{'Save'|t('wheelform')}}</button>
                        <a :href="getBackUrl" class="btn primary">{{'Back'|t('wheelform')}}</a>
                    </div>
                    <div class="col">
                        <a @click.prevent="handleDelete" class="form-field-rm pull-right">{{ 'Delete' |t('wheelform')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import axios from 'axios';
import draggable from 'vuedraggable';
import toastr from 'toastr';
import Settings from './Settings.vue';

export default {
    components:{
        draggable,
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
                    const form = JSON.parse(res.data);
                    if(form) {
                        form.options = Object.assign(this.getDefaultFormOptions(), form.options);
                        //parse fields
                        form.fields = form.fields ? form.fields : [];
                        for (let index = 0; index < form.fields.length; index++) {
                            // only get options that belong to that fieldType
                            const fieldType = this.fieldTypes.find((fieldType) => {
                                return (form.fields[index].type == fieldType.type);
                            });

                            if(! fieldType) {
                                continue;
                            }

                            let fieldOptions = this.getOptionsFromConfig(fieldType.config);
                            const options = form.fields[index].options ? form.fields[index].options : {};

                            //set Options from database
                            Object.keys(options).forEach((key) => {
                                if(fieldOptions.hasOwnProperty(key)) {
                                    fieldOptions[key] = options[key];
                                }
                            });

                            form.fields[index].isActive = 0;
                            form.fields[index].errors = {};
                            form.fields[index].config = fieldType.config;
                            form.fields[index].options = fieldOptions;
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
        },
        areExpanded()
        {
            const activeField = this.form.fields.find((field) => {
                return (field.isActive);
            });

            return (activeField ? true : false);
        }
    },
    methods: {
        clone(fieldType) {
            let field = this.deepClone(fieldType);
            field.isActive = 0;
            field.errors = {};
            field.options = this.getOptionsFromConfig(fieldType.config);
            return field;
        },
        deepClone(src, exclude = []) {
            let target = {};
            for(let prop in src) {
                if(exclude.indexOf(prop) > -1) {
                    continue;
                }
                // if the value is a nested object, recursively copy all it's properties
                if (this.isObject(src[prop])) {
                    target[prop] = this.deepClone(src[prop]);
                } else {
                    target[prop] = src[prop];
                }
            }
            return target;
        },
        isObject(obj) {
            var type = typeof obj;
            return type === 'function' || type === 'object' && !!obj;
        },
        onDragEnd()
        {
            return this.getSortList();
        },
        onDragAdd()
        {
            return this.getSortList();
        },
        validateFieldName(userInput)
        {
            let result = this.form.fields.filter((field) => {
                return field.name === userInput;
            });

            if(result.length > 0)
            {
                return Craft.t('wheelform', 'Name is not unique')
            }

            if(userInput.indexOf(' ') >= 0){
                return Craft.t('wheelform', 'Name contains whitespaces')
            }

            return null;
        },
        updateFieldProperty(index, property, value)
        {
            if(property == 'name') {
                this.form.fields[index].errors.name = this.validateFieldName(value);
            }
            this.form.fields[index][property] = value;
        },
        activeTabClasses(view) {
            return {
                sel: (view == this.currentView),
            }
        },
        getOptionsFromConfig(config = []) {
            let options = {};
            for(let i = 0; i < config.length; i++) {
                if(Array.isArray(config[i].value)) {
                    options[config[i].name] = config[i].value.slice(0);
                } else {
                    options[config[i].name] =  config[i].value;
                }
            }

            return options;
        },
        getSortList()
        {
            let fields = this.form.fields.map(function(item, index) {
                return item.order = index + 1;
            });
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
        handleDelete()
        {
            const result = window.confirm(Craft.t('wheelform', "Are you sure you want to delete Form")+ ": " + this.form.name);
            if(result)
            {
                const cpUrl = window.Craft.baseCpUrl;

                let headers = {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': Craft.csrfTokenValue,
                };

                axios.post(cpUrl + "/wheelform/form/delete", this.form, {headers: headers})
                    .then((res) => {
                        const success = res.data.success;
                        if(success) {
                            window.location.replace(cpUrl + '/wheelform');
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
        },
        handleViewChange(view) {
            this.currentView = view;
        },
        handleCollapseExpandFields()
        {
            const isActive = ! this.areExpanded;

            this.form.fields.map((field) => {
                field.isActive = isActive;
            });
        }
    }
}
</script>
