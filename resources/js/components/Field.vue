<template>
    <div className="wheelform-field">
        <h4>
          <input type="text" defaultValue={this.props.name} name={"fields[" + this.props.index + "][name]"}/>
        </h4>
    </div>
</template>

<script>
export default {
    props: [
        {
            type: Object,
            required: true
        }
    ]
}
</script>

<style>

</style>
