<template>
    <div id="formapp">
        <div class="btn-container">
            <button v-on:click.prevent="addField" style="margin-bottom: 15px" class="btn submit">Add  Field</button>
            <button v-on:click.prevent="handleEditMode" class="btn primary pull-right">{{isEditMode ? "Drag" : "Edit"}} Fields</button>
        </div>
        <div id="field-container">
              <Field
                v-for="(field, index) in fields"
                v-bind:key="index"
                v-bind:index="index"
                v-bind:field="field"
              />
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Field from './Field.vue';

export default {
    components:{
        Field
    },
    data() {
        return {
            isEditMode: false,
            fields: []
        }
    },
    mounted()
    {
        console.log(this.fields);
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
                this.fields = res.data;
            });
        }
        console.log(this.fields);
    },
    methods: {
        addField() {
            let fieldIndex = (this.fields.length + 1);

            this.fields.push({
                name: "field_" + fieldIndex,
                type: "text",
                index_view: false,
                active: false,
                required: false,
            });
        },
        handleEditMode() {
            this.isEditMode = !this.isEditMode;
        }
    }
}
</script>

<style>

</style>

