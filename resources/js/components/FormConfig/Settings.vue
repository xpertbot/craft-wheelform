<template>
    <div class="form-settings">
        <div class="field first" id="name-field">
            <div class="heading">
                <label class="required" for="name">Form Name</label>
                <div class="instructions"><p>Name of this Form in the CP.</p>
                </div>
            </div>
            <div class="input ltr">
                <input class="text fullwidth" type="text" id="name" v-model="form.name" autofocus="" autocomplete="off">
            </div>
        </div>
        <div class="field" id="to_email-field">
            <div class="heading">
                <label id="to_email-label" class="required" for="to_email">To Email</label>
                <div class="instructions"><p>The email address(es) that the contact form will send to. Separate multiple email addresses with commas.</p>
                </div>
            </div>
            <div class="input ltr">
                <input class="text fullwidth" type="text" id="to_email" v-model="form.to_email" autocomplete="off">
            </div>
        </div>
        <div class="field" id="options[honeypot]-field">
            <div class="heading">
                <label id="options[honeypot]-label" for="options[honeypot]">Honeypot</label>
                <div class="instructions"><p>Name of hidden field that helps prevent bot spam. Leave empty to disable.</p>
                </div>
            </div>
            <div class="input ltr">
                <input class="text fullwidth" type="text" id="options[honeypot]" v-model="form.options.honeypot" value="" autocomplete="off">
            </div>
        </div>

        <div  class="field">
            <Lightswitch
                :name="'active'"
                :label="'Active'"
                :status="form.active"
                :handle-status-change="handleStatusChange"
                />
        </div>
        <div  class="field">
            <Lightswitch
                :name="'save_entry'"
                :label="'Save Entries'"
                :instructions="'Save Entries to database'"
                :status="form.save_entry"
                :handle-status-change="handleStatusChange"
                />
        </div>
        <div  class="field">
            <Lightswitch
                :name="'send_email'"
                :label="'Send Email'"
                :status="form.send_email"
                :handle-status-change="handleStatusChange"
                />
        </div>
        <div  class="field">
            <Lightswitch
                :name="'recaptcha'"
                :label="'Recaptcha'"
                :status="form.recaptcha"
                :handle-status-change="handleStatusChange"
                />
        </div>
        <div class="field action-buttons">
            <button v-on:click.prevent="$emit('handle-save-settings')" class="btn submit">Save</button>
            <a :href="getBackUrl" class="btn primary">Back</a>
        </div>
    </div>
</template>
<script>
import Lightswitch from './Lightswitch.vue';

export default {
    components: {
        Lightswitch,
    },
    props: [
        "form",
    ],
    computed: {
        getBackUrl()
        {
            const  cpUrl = window.Craft.baseCpUrl;
            return cpUrl + "/wheelform";
        }
    },
    methods: {
        handleStatusChange(key, value)
        {
            this.$emit('handle-form-setting', key, value);
        }
    }
}
</script>
