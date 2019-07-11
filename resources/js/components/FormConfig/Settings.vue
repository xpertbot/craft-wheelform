<template>
    <div class="wheelform-container form-settings">
        <div class="field first" id="name-field">
            <div class="heading">
                <label class="required" for="name">{{'Form Name'|t('wheelform')}}</label>
                <div class="instructions"><p>{{'Name of this Form in the CP.'|t('wheelform')}}</p>
                </div>
            </div>
            <div class="input ltr">
                <input class="text fullwidth" type="text" id="name" v-model="form.name" autofocus="" autocomplete="off">
            </div>
        </div>
        <div class="field" id="to_email-field">
            <div class="heading">
                <label id="to_email-label" class="required" for="to_email">{{'To Email'|t('wheelform')}}</label>
                <div class="instructions"><p>{{'The email address(es) that the contact form will send to. Separate multiple email addresses with commas.'|t('wheelform')}}</p>
                </div>
            </div>
            <div class="input ltr">
                <input class="text fullwidth" type="text" id="to_email" v-model="form.to_email" autocomplete="off">
            </div>
        </div>

        <div  class="field">
            <Lightswitch
                :name="'active'"
                :label="'Active'|t('wheelform')"
                :status="form.active"
                @handle-status-change="handleStatusChange"
                />
        </div>
        <div  class="field">
            <Lightswitch
                :name="'save_entry'"
                :label="'Save Entries'|t('wheelform')"
                :instructions="'Save Entries to database'|t('wheelform')"
                :status="form.save_entry"
                @handle-status-change="handleStatusChange"
                />
        </div>
        <div  class="field">
            <Lightswitch
                :name="'send_email'"
                :label="'Send Email'|t('wheelform')"
                :status="form.send_email"
                @handle-status-change="handleStatusChange"
                />
        </div>

        <Collapsable>
            <div class="field" id="options[email_subject]-field">
                <div class="heading">
                    <label id="options[email_subject]-label" for="options[email_subject]">{{'Email Subject'|t('wheelform')}}</label>
                    <div class="instructions"><p>{{'Subject for the Admin email'|t('wheelform')}}</p>
                    </div>
                </div>
                <div class="input ltr">
                    <input class="text fullwidth" type="text" id="options[email_subject]" v-model="form.options.email_subject" value="" autocomplete="off">
                </div>
            </div>

            <div  class="field">
                <Lightswitch
                    :name="'recaptcha'"
                    :label="'Recaptcha'"
                    :status="form.recaptcha"
                    @handle-status-change="handleStatusChange"
                    />
            </div>

            <div class="field" id="options[honeypot]-field">
                <div class="heading">
                    <label id="options[honeypot]-label" for="options[honeypot]">{{'Honeypot'|t('wheelform')}}</label>
                    <div class="instructions"><p>{{'Name of hidden field that helps prevent bot spam. Leave empty to disable.'|t('wheelform')}}</p>
                    </div>
                </div>
                <div class="input ltr">
                    <input class="text fullwidth" type="text" id="options[honeypot]" v-model="form.options.honeypot" value="" autocomplete="off">
                </div>
            </div>
            <div class="field">
                <span v-show="! form.send_email && form.options.user_notification" style="color: #da5a47">{{'Send Email needs to be active for this feature to work'|t('wheelform')}}</span>
                <Lightswitch
                    :name="'user_notification'"
                    :label="'User Notification'|t('wheelform')"
                    :status="form.options.user_notification"
                    @handle-status-change="handleOptionsChange"
                    />
            </div>
            <div class="field" v-if="form.options.user_notification">
                <div class="heading">
                    <label id="options[user_notification_message]-label" for="options[user_notification_message]">{{'User Notification Message'|t('wheelform')}}</label>
                    <div class="instructions"><p>{{'Message that will be displayed on the body of the user Notification Email'|t('wheelform')}}</p>
                    </div>
                </div>
                <div class="input ltr">
                    <textarea  v-model="form.options.user_notification_message" class="text fullwidth"></textarea>
                </div>
            </div>
        </Collapsable>
    </div>
</template>
<script>
import Lightswitch from './Lightswitch.vue';
import Collapsable from '../Partials/Collapsable.vue';

export default {
    components: {
        Lightswitch,
        Collapsable,
    },
    props: [
        "form",
    ],
    methods: {
        handleStatusChange(key, value)
        {
            this.$emit('handle-form-setting', key, value);
        },
        handleOptionsChange(option, value)
        {
            this.$emit('handle-form-option-change', option, value);
        }
    }
}
</script>
