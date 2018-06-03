<template>
    <div class="wheelform-field" :style="getFieldStyle">
        <div v-show="!isEditMode" class="field-details">
            <div class="handle"><i class="fa fa-bars"></i></div>
            <div class="row">
                <div class="col">
                    <span class="field-label">
                        {{ field.name }}
                    </span>
                </div>
                <div class="col text-right">
                    <span :style="'color:'+getStatusColor(field.required)">Required</span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <strong>Label:</strong> {{ getFieldLabel }}
                </div>
                <div class="col text-right">
                    <span :style="'color:'+getStatusColor(field.index_view)">Index</span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <strong>Type:</strong> {{ field.type | capitalize }}
                </div>
            </div>
        </div>
        <div v-show="isEditMode" class="input-container">
            <div class="row">
                <div class="col">
                    <label class="required">Name:</label>
                    <input type="text" v-model="field.name" @change="validateName" :name="getFieldName('name')" />
                    <p v-show="! field.isValidName.status" style="color: #da5a47">{{ field.isValidName.msg }}</p>
                </div>
                <div class="col">
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
            </div>
            <div class="row">
                <div class="col">
                    &nbsp;
                </div>
                <div class="col">
                    <Lightswitch
                        :name="'required'"
                        :label="'Required'"
                        :status="field.required"
                        :handle-status-change="handleStatusChange"
                        />

                </div>
            </div>
            <div class="row">
                <div class="col">&nbsp;</div>
                <div class="col">
                    <Lightswitch
                        :name="'index_view'"
                        :label="'Index View'"
                        :status="field.index_view"
                        :handle-status-change="handleStatusChange"
                        />
                </div>
            </div>
            <div class="row">
                <div class="col text-right">
                    <a href="" @click.prevent="validateDeleteField" class="form-field-rm">Delete</a>
                </div>
            </div>
        </div>
        <input type="hidden" :name="getFieldName('id')" :value="field.id ? field.id : '0'">
        <input type="hidden" :name="getFieldName('order')" :value="order">
        <input type="hidden" :name="getFieldName('required')" v-model="field.required">
        <input type="hidden" :name="getFieldName('index_view')" v-model="field.index_view">
        <input type="hidden" :name="getFieldName('active')" v-model="field.active">
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
                'email',
                'number',
                'checkbox',
                'radio',
                'hidden',
                'select',
                'file',
            ],
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
            let label = this.field.name.toString();
            label = label.replace(/_/g, ' ');
            label = label.replace(/-/g, ' ');
            label = label.charAt(0).toUpperCase() + label.slice(1);
            return label;
        }
    },
    methods: {
        handleStatusChange(key, value)
        {
            this.field[key] = value;
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
        }
    }
}
</script>

<style>
.field-details{
    position: relative;
    padding-left: 25px;
}
.handle{
    position: absolute;
    top: 50%;
    left: 0;
    z-index: 10;
    cursor: move;
    max-width: 18px;
    max-height: 20px;
    margin-top: -10px; /* half of height; */
}
.row{
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
}
.col{
    flex-basis: 0;
    flex-grow: 1;
    max-width: 100%;
}
.input-container .col{
    margin:0px 0px 10px;
}
.text-right{
    text-align: right;
}
#formapp label{
    display: block;
}
#formapp .field-label{
    font-size: 18px;
}
</style>
