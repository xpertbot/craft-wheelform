<template>
    <div class="wheelform-field" :style="getFieldStyle">
        <div class="field-details">
            <div class="wheelform-field-handle"><i class="fa fa-bars"></i></div>
            <a @click.prevent="isActive = ! isActive">{{ getFieldLabel }} <small class="pull-right">{{type}}</small></a>
        </div>
        <div class="field-content" v-show="isActive">
            <div>
                <label class="required">{{'Name'|t('wheelform')}}:</label>
                <input type="text" :value="name" @input="updateFieldProperty('name', $event.target.value)" />
            </div>
            <div>
                <label>{{'Content'|t('app')}}:</label>
                <textarea name="" id="" cols="30" rows="10"></textarea>
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
        updateFieldOptionProperty(property, value) {
            this.$emit('update-field-option',this.index, property, value);
        },
    }
}
</script>
