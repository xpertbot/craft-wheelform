<template>
    <div id="formapp">
        <div class="btn-container">
            <button v-on:click.prevent="addField" style="margin-bottom: 15px" class="btn submit">Add  Field</button>
            <button v-on:click.prevent="handleEditMode" class="btn primary pull-right">{{isEditMode ? "Drag" : "Edit"}} Fields</button>
        </div>
        <div id="field-container">
              <Field
               v-for="(field, index) in fields"
               :key="field.uniqueId"
               :index="index"
               :order="index + 1"
               :default-field="field"
               :is-edit-mode="isEditMode"
               @delete-field="fields.splice(index, 1)"
              />
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Field from './Field.vue';
import { find } from 'lodash';

export default {
    components:{
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
                index_view: false,
                active: false,
                required: false,
                uniqueId: this.generateKeyId()
            });
        },
        handleEditMode()
        {
            this.isEditMode = !this.isEditMode;
        },
        generateKeyId()
        {
            return '_' + Math.random().toString(36).substr(2, 9);
        }
    }
}
</script>

<style>

</style>

