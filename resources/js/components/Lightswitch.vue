<template>
    <div>
        <div class="heading">{{ label }}</div>
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

<style>
    .fa{
        font-size: 20px;
    }
</style>
