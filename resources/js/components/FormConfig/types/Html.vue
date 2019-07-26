<template>
    <div class="wheelform-field" :style="getFieldStyle">
        <div class="field-details">
            <div class="wheelform-field-handle"><i class="fa fa-bars"></i></div>
            <a @click.prevent="isActive = ! isActive">{{ getFieldLabel }} <small class="pull-right">{{type}}</small></a>
        </div>
        <div class="field-content" v-show="isActive">
            <div class="row">
                <div class="col">
                    <label class="required">{{'Name'|t('wheelform')}}:</label>
                    <input type="text" :value="name" @input="updateFieldProperty('name', $event.target.value)" />
                    <p v-show="getErrorFor('name') !== null" style="color: #da5a47">{{ getErrorFor('name') }}</p>
                </div>
                <div class="col text-right">
                    <a @click.prevent="validateDeleteField" class="form-field-rm">{{'Delete'|t('wheelform')}}</a>
                </div>
            </div>
            <div>
                <label>{{'Content'|t('app')}}:</label>
                <Editor
                    :value="options.content"
                    @input="handleEditorUpdate"
                    :options="{
                        lineNumbers: true,
                        mode: 'text/html',
                    }"
                ></Editor>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        "index",
        "name",
        "required",
        "type",
        "index_view",
        "options",
        "config",
         "errors",
    ],
    components: {

    },
    data() {
        return {
            isActive: false,
        }
    },
    computed: {
        getFieldLabel()
        {
            let label = this.name.toString();
            label = label.replace(/_/g, ' ');
            label = label.replace(/-/g, ' ');
            label = label.charAt(0).toUpperCase() + label.slice(1);
            return label;
        }
    },
    methods: {
        getFieldStyle()
        {
            return "position: relative, cursor: move";
        },
        updateFieldProperty(property, value) {
            this.$emit('update-field-property', this.index, property, value);
        },
        handleEditorUpdate(value) {
            this.$emit('update-field-option',this.index, 'content', value);
        },
        getErrorFor(property) {
            if(this.errors[property]) {
                return this.errors[property];
            }
            return null;
        },
        validateDeleteField()
        {
            const result = window.confirm(Craft.t('wheelform', "Are you sure you want to delete Field")+ ": " + this.name);
            if(result)
            {
                this.$emit('delete-field');
            }
        },
    }
}
</script>
