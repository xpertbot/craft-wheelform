<template>
    <div class="wheelform-field" :style="getFieldStyle">
        <div v-show="!isEditMode">
            <div class="row">
                <div class="col">
                    <span class="field-label">
                        {{ field.label ? field.label : getFieldLabel }}
                    </span>
                </div>
                <div class="col text-right">
                    <span :style="'color:'+getStatusColor(field.required)">Required</span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <strong>Name:</strong> {{ field.name }}
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
                    <label>Label:</label>
                    <input type="text" v-model="field.label" :name="getFieldName('label')" />
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
                    <label class="required">Name:</label>
                    <input type="text" v-model="field.name" :name="getFieldName('name')" />
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
        </div>
        <input type="hidden" :name="getFieldName('id')" :value="field.id ? field.id : ''">
        <input type="hidden" :name="getFieldName('order')" :value="order+1">
        <input type="hidden" :name="getFieldName('required')" v-model="field.required">
        <input type="hidden" :name="getFieldName('index_view')" v-model="field.index_view">
        <input type="hidden" :name="getFieldName('active')" v-model="field.active">
    </div>
</template>

<script>
import Lightswitch from './Lightswitch.vue';

export default {
    props: [
        "defaultOrder",
        "defaultField",
        "isEditMode",
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
            field: this.defaultField,
            order: this.defaultOrder
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
            return "fields[" + this.order + "]["+ key +"]"
        },
        getFieldStyle()
        {
            return "position: relative, cursor: move";
        },
        getStatusColor(status)
        {
            return (status ? "#00b007" : "grey");
        }
    }
}
</script>

<style>
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
