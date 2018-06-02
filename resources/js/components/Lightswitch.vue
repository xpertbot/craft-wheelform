<template>
    <div>
        <div class="heading">{{ label }}</div>
        <a href="">
            <i :style="getStatusColor" :class="getStatusClass"></i>
        </a>
        <input type="hidden" name="name" value="1" />
    </div>
</template>

<script>
export default {
    props:[
        'label',
        'handleEvent',
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
