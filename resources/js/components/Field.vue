<template>
    <div class="wheelform-field" :style="getFieldStyle">
        <div v-show="!isEditMode" class="field-details">
            <div class="wheelform-field-handle"><i class="fa fa-bars"></i></div>
            <div class="row">
                <div class="col">
                    <div>
                        <span class="field-label">
                            {{ field.name }}
                        </span>
                    </div>
                    <div>
                        <strong>Label:</strong> {{ getFieldLabel }}
                    </div>
                    <div>
                        <strong>Type:</strong> {{ field.type | capitalize }}
                    </div>
                    <div v-if="isMultiOption">
                        <div>
                            <strong>Options</strong>
                            <span :style="'color:'+getStatusColor(field.options.validate)">Validate</span>
                        </div>
                        <ul>
                            <li v-if="field.type == 'select' && field.options.selectEmpty">
                                --
                            </li>
                            <li
                                v-for="(item, index) in field.options.items"
                                v-bind:key="index"
                            >
                                {{ item }}
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col">
                    <div class="text-right">
                        <span :style="'color:'+getStatusColor(field.required)">Required</span>
                    </div>
                    <div class="text-right">
                        <span :style="'color:'+getStatusColor(field.index_view)">Index</span>
                    </div>
                    <div class="text-right" v-if="field.options.containerClass">
                        <span>{{ field.options.containerClass }}</span>
                    </div>
                    <div class="text-right" v-if="field.options.fieldClass">
                        <span>{{ field.options.fieldClass }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div v-show="isEditMode">
            <div class="row">
                <div class="col">
                    <div>
                        <label class="required">Name:</label>
                        <input type="text" v-model="field.name" @change="validateName" :name="getFieldName('name')" />
                        <p v-show="! field.isValidName.status" style="color: #da5a47">{{ field.isValidName.msg }}</p>
                    </div>
                    <div>
                        <label>Label:</label>
                        <input type="text" v-model="field.options.label" :name="'fields['+index+'][options][label]'" />
                    </div>
                    <div>
                        <label>Type:</label>
                        <select v-model="field.type" :name="getFieldName('type')">
                            <option
                                v-for="(fieldType, index) in fieldTypes"
                                :key="index"
                                :value="fieldType"
                                >
                                {{ fieldType | capitalize }}
                            </option>
                        </select>
                    </div>
                    <div v-if="isMultiOption">
                        <div v-if="field.type == 'select'">
                            <Lightswitch
                                :name="'options_select_empty'"
                                :label="'Default Empty?'"
                                :status="field.options.selectEmpty"
                                :handle-status-change="handleOptionSelectEmpty"
                            />
                        </div>
                        <div>
                            <strong>Options</strong>
                            <Lightswitch
                                :name="'options_validate'"
                                :label="'Validate'"
                                :status="field.options.validate"
                                :handle-status-change="handleOptionValidate"
                                />
                        </div>
                        <div>
                            <input class="new-option"
                                autocomplete="off"
                                placeholder="Item to validate"
                                v-model="newOption">
                            <a href="" @click.prevent="addOption" class="form-field-add">Add</a>
                        </div>
                        <div>
                            <ul>
                                <li
                                    v-for="(item, key) in field.options.items"
                                    v-bind:key="key"
                                >
                                    {{ item }}
                                    <a href="" @click.prevent="removeOption(item)" class="form-field-rm">X</a>
                                    <input type="hidden" :name="'fields['+index+'][options][items]['+key+']'" :value="item">
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div>
                        <Lightswitch
                            :name="'required'"
                            :label="'Required'"
                            :status="field.required"
                            :handle-status-change="handleStatusChange"
                            />
                    </div>
                    <div>
                        <Lightswitch
                            :name="'index_view'"
                            :label="'Index View'"
                            :status="field.index_view"
                            :handle-status-change="handleStatusChange"
                            />
                    </div>
                    <div>
                        <label :for="'field-class-' + field.id">Container class</label>
                        <input type="text" :id="'field-class-' + field.id" v-model="field.options.containerClass" :name="'fields['+index+'][options][containerClass]'">
                    </div>
                    <div>
                        <label :for="'field-class-' + field.id">Field class</label>
                        <input type="text" :id="'field-class-' + field.id" v-model="field.options.fieldClass" :name="'fields['+index+'][options][fieldClass]'">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col"></div>
                <div class="col">
                    <div class="text-right mt-10">
                        <a href="" @click.prevent="validateDeleteField" class="form-field-rm">Delete</a>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" :name="getFieldName('id')" :value="field.id ? field.id : '0'">
        <input type="hidden" :name="getFieldName('order')" :value="order">
        <input type="hidden" :name="getFieldName('required')" v-model="field.required">
        <input type="hidden" :name="getFieldName('index_view')" v-model="field.index_view">
        <input type="hidden" :name="getFieldName('active')" v-model="field.active">
        <input type="hidden" :name="'fields['+index+'][options][validate]'" v-model="field.options.validate">
        <input type="hidden" :name="'fields['+index+'][options][selectEmpty]'" v-model="field.options.selectEmpty">
    </div>
</template>

<script>
import Lightswitch from './Lightswitch.vue';
import { debounce } from 'lodash';

export default {
    props: [
        "order",
        "index",
        "defaultField",
        "isEditMode",
        "validateNameCallback"
    ],
    data(){
        return {
            fieldTypes: [
                'text',
                'textarea',
                'email',
                'number',
                'checkbox',
                'radio',
                'hidden',
                'select',
                'file',
            ],
            newOption: '',
            field: Object.assign({}, this.defaultField),
        }
    },
    components: {
        Lightswitch
    },
    filters: {
        capitalize(value)
        {
            if (!value) return '';
            value = value.toString();

            return value.charAt(0).toUpperCase() + value.slice(1);
        }
    },
    computed:{
        getFieldLabel()
        {
            if(this.field.options.label)
            {
                return this.field.options.label;
            }
            let label = this.field.name.toString();
            label = label.replace(/_/g, ' ');
            label = label.replace(/-/g, ' ');
            label = label.charAt(0).toUpperCase() + label.slice(1);
            return label;
        },
        isMultiOption()
        {
            const multiOption = [
                "checkbox",
                "radio",
                "select"
            ];

            let isMultiOption = multiOption.indexOf(this.field.type);

            return (isMultiOption >= 0);
        }
    },
    methods: {
        handleStatusChange(key, value)
        {
            this.field[key] = (value ? 1 : 0);
        },
        getFieldName(key)
        {
            return "fields[" + this.index + "]["+ key +"]"
        },
        getFieldStyle()
        {
            return "position: relative, cursor: move";
        },
        getStatusColor(status)
        {
            return (status ? "#00b007" : "grey");
        },
        validateDeleteField()
        {
            const result = window.confirm("Are you sure you want to delete Field: "+this.field.name);
            if(result)
            {
                this.$emit('delete-field');
            }
        },
        validateName()
        {
            this.field.isValidName = this.validateNameCallback(this.field.name);
        },
        addOption: function () {
            var value = this.newOption && this.newOption.trim()
            if (!value) {
                return
            }
            this.field.options.items.push(value)
            this.newOption = ''
        },
        removeOption: function (option) {
            this.field.options.items.splice(this.field.options.items.indexOf(option), 1)
        },
        handleOptionValidate(name, value)
        {
            this.field.options.validate = (value ? 1 : 0);
        },
        handleOptionSelectEmpty(name, value)
        {
            this.field.options.selectEmpty = (value ? 1 : 0);
        },
    }
}
</script>

<style>
    ul{
        list-style: none;
        padding: 10px 0px 0px 5px;
    }
</style>
