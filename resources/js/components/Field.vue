<template>
    <div class="wheelform-field" :style="getFieldStyle">
        <div v-show="!isEditMode">
            <div class="row">
                <div class="col">
                    <h4>
                        {{ field.name }}
                    </h4>
                </div>
                <div class="col text-right">
                    <span :style="'color:'+getStatusColor(field.required)">Required</span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <span>{{ field.type | capitalize }}</span>
                </div>
                <div class="col text-right">
                    <span :style="'color:'+getStatusColor(field.index_view)">Index</span>
                </div>
            </div>
        </div>
        <div v-show="isEditMode" class="input-container">
            <div class="row">
                <div class="col">
                    <input type="text" v-model="field.name" />
                </div>
            </div>
            <div class="row">
                <div class="col">
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
                    <Lightswitch
                        :name="'required'"
                        :label="'Required'"
                        :status="field.required"
                        :handle-status-change="handleStatusChange"
                        />

                </div>
            </div>
            <div class="row">
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
    </div>
</template>

<script>
import Lightswitch from './Lightswitch.vue';

export default {
    props: [
        "index",
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
            field: this.defaultField
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
</style>
