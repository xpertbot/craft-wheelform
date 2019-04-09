<template>
    <div>
        <div class="heading">{{ label }}</div>
        <div v-if="instructions" class="instructions">
            {{instructions}}
        </div>
        <a href="" @click.prevent="$emit('handle-status-change', name, ! status)">
            <i :style="getStatusColor" :class="getStatusClass"></i>
        </a>
    </div>
</template>

<script>
export default {
    props:[
        'label',
        'status',
        'instructions',
        'name'
    ],
    computed: {
        getStatusColor()
        {
            return "color:"+ (this.status ? "#00b007" : "grey");
        },
        getStatusClass()
        {
            return "fa fa-toggle-" + (this.status ? 'on' : 'off')
        }
    }
}
</script>
