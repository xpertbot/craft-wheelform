<template>
    <div>
        <div class="heading">{{ label }}</div>
        <div v-if="instructions" class="instructions">
            {{instructions}}
        </div>
        <a href="" @click.prevent="handleStatusChange(name, ! status)">
            <i :style="getStatusColor" :class="getStatusClass"></i>
        </a>
    </div>
</template>

<script>
export default {
    props:[
        'label',
        'handleStatusChange',
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
