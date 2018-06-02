<template>
    <div class="wheelform-field" style="getFieldStyle">
        <h4 v-show="!isEditMode">
            {{ name }}
        </h4>
        <input type="text" :value="name" v-show="isEditMode" />
        <div class="meta subheading" v-show="!isEditMode">
            <span>{{ type | capitalize }}</span>
        </div>
        <div v-show="isEditMode">
            <div className="meta subheading">
            <select v-model="type" name="getFieldName">
                <option
                    v-for="(fieldType, index) in fieldTypes"
                    :key="index"
                    :value="fieldType"
                    >
                    {{ fieldType | capitalize }}
                </option>
            </select>
            </div>
            <div style="padding-top: 20px">
                <Lightswitch
                    :name="'required'"
                    :label="'Required'"
                    :status="required"
                    />

                <Lightswitch
                    :name="'index_view'"
                    :label="'Index View'"
                    :status="index_view"
                    />
            </div>
        </div>
    </div>
</template>

<script>
import Lightswitch from './Lightswitch.vue';

export default {
    props: [
        "index",
        "defaultName",
        "type",
        "required",
        "index_view",
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
            name: this.defaultName
        }
    },
    components: {
        Lightswitch
    },
    computed: {
        getFieldType()
        {
           return "fields["+ this.index+"][name]";
        },
        getFieldName()
        {
            "fields[" + this.index + "][type]"
        },
        getFieldStyle()
        {
            return "position: relative, cursor: move";
        }
    },
    filters: {
        capitalize(value)
        {
            if (!value) return '';
             value = value.toString();
            return value.charAt(0).toUpperCase() + value.slice(1);
        }
    }
}
</script>

<style>

</style>
