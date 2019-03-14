<template>
    <div class="wheelform-collapsable">
        <a href="" @click.prevent="isActive = ! isActive"><i :class="getIconClass"></i> {{'More Options'|t('wheelform')}}</a>
        <div class="collapsable-content" v-show="isActive">
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            isActive: false,
        }
    },
    computed: {
        getIconClass() {
            return "fa fa-angle-double-" + (this.isActive ? "down" : 'right');
        }
    }
}
</script>

