<template>
<div class="wheelform-field" :style="getFieldStyle">
    <div class="field-details">
        <div class="wheelform-field-handle"><i class="fa fa-bars"></i></div>
        <a @click.prevent="isActive = ! isActive">{{ getFieldLabel }}</a>
    </div>
    <div class="field-content" v-show="isActive">
        <div>
            <label class="required">{{'Name'|t('wheelform')}}:</label>
            <input type="text" :value="name" @input="updateFieldProperty('name', $event.target.value)" />
            <p v-show="! isValidName.status" style="color: #da5a47">{{ isValidName.msg }}</p>
        </div>
        <div>
            <label>{{'Label'|t('wheelform')}}:</label>
            <input type="text" :value="options.label" @input="updateFieldOptionProperty('label', $event.target.value)" />
        </div>
        <div>
            <Lightswitch
                :name="'required'"
                :label="'Required'|t('wheelform')"
                :status="required"
                @handle-status-change="handleStatusChange"
                />
        </div>
        <div>
            <Lightswitch
                :name="'index_view'"
                :label="'Index View'|t('wheelform')"
                :status="index_view"
                @handle-status-change="handleStatusChange"
                />
        </div>
        <div>
            <Lightswitch
                :name="'is_user_notification_field'"
                :label="'User Notification Field'|t('wheelform')"
                :status="options.is_user_notification_field"
                @handle-status-change="handleStatusOptionChange"
                />
        </div>
        <div>
            <Lightswitch
                :name="'is_reply_to'"
                :label="'Reply-to Email'|t('wheelform')"
                :status="options.is_reply_to"
                @handle-status-change="handleStatusOptionChange"
                />
        </div>
        <div>
            <label :for="'container-class-' + index">{{'Container class'|t('wheelform')}}</label>
            <input type="text" :id="'container-class-' + index" :value="options.containerClass" @input="updateFieldOptionProperty('containerClass', $event.target.value)">
        </div>
        <div>
            <label :for="'field-class-' + index">{{'Field class'|t('wheelform')}}</label>
            <input type="text" :id="'field-class-' + index" :value="options.fieldClass" @input="updateFieldOptionProperty('fieldClass', $event.target.value)">
        </div>
        <div>
            <label :for="'field-placeholder-' + index">{{'Field placeholder'|t('wheelform')}}</label>
            <input type="text" :id="'field-placeholder-' + index" :value="options.placeholder" @input="updateFieldOptionProperty('placeholder', $event.target.value)">
        </div>
        <div>
            <div v-if="type == 'select'">
                <Lightswitch
                    :name="'options_select_empty'"
                    :label="'Default Empty?'|t('wheelform')"
                    :status="options.selectEmpty"
                    @handle-status-change="handleStatusOptionChange"
                />
            </div>
            <div>
                <strong>{{'Options'|t('wheelform')}}</strong>
                <Lightswitch
                    :name="'validate'"
                    :label="'Validate'|t('wheelform')"
                    :status="options.validate"
                    @handle-status-change="handleStatusOptionChange"
                    />
            </div>
            <div>
                <input class="new-option"
                    autocomplete="off"
                    :placeholder="'Item to validate'|t('wheelform')"
                    :value="newOption"
                    @input="newOption = $event.target.value"
                >
                <a href="" @click.prevent="addOption" class="form-field-add">{{'Add'|t('wheelform')}}</a>
            </div>
            <div>
                <ul class="list-wrapper">
                    <li
                        v-for="(item, key) in options.items"
                        :key="key"
                    >
                        {{ item }}
                        <a href="" @click.prevent="deleteFieldOptionItem($event.target.value)" class="form-field-rm">X</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col"></div>
            <div class="col">
                <div class="text-right mt-10">
                    <a href="" @click.prevent="validateDeleteField" class="form-field-rm">{{'Delete'|t('wheelform')}}</a>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import Lightswitch from './Lightswitch.vue';
import { debounce } from 'lodash';

export default {
    props: [
        "index",
        "name",
        "required",
        "type",
        "index_view",
        "options",
    ],
    data(){
        return {
            isValidName: {
                status: true,
                msg: ''
            },
            newOption: '',
            isActive: false,
        }
    },
    components: {
        Lightswitch
    },
    filters: {
        capitalize(value)
        {
            if (!value) return '';
            value = value.toString();

            return value.charAt(0).toUpperCase() + value.slice(1);
        }
    },
    computed:{
        getFieldLabel()
        {
            if(this.options.label)
            {
                return this.options.label;
            }
            let label = this.name.toString();
            label = label.replace(/_/g, ' ');
            label = label.replace(/-/g, ' ');
            label = label.charAt(0).toUpperCase() + label.slice(1);
            return label;
        },
        validateName()
        {
            this.isValidName = this.validateNameCallback(this.name);
            if(this.isValidName.status)
            {
                this.$emit('update-field-property', this.index, 'name', this.name)
            }
        },
    },
    methods: {
        handleStatusChange(key, boolValue)
        {
            const value = (boolValue ? 1 : 0);
            this.$emit('update-field-property', this.index, key, value);
        },
        handleStatusOptionChange(key, boolValue)
        {
            const value = (boolValue ? 1 : 0);
            this.$emit('update-field-option', this.index, key, value);
        },
        getFieldStyle()
        {
            return "position: relative, cursor: move";
        },
        validateDeleteField()
        {
            const result = window.confirm(Craft.t('wheelform', "Are you sure you want to delete Field")+ ": " + this.name);
            if(result)
            {
                this.$emit('delete-field');
            }
        },
        updateFieldProperty(property, value)
        {
            this.$emit('update-field-property', this.index, property, value);
        },
        updateFieldOptionProperty(property, value) {
            this.$emit('update-field-option',this.index, property, value);
        },
        addOption() {
            var value = this.newOption && this.newOption.trim();
            if (!value) {
                return
            }
            let items = this.options.items;
            items.push(value);
            this.$emit('update-field-option', this.index, 'items', items);
            this.newOption = '';
        },
        deleteFieldOptionItem(value)
        {
            let items = this.options.items;
            items.splice(items.indexOf(value), 1);
            this.$emit('update-field-option', this.index, 'items', items);
        }
    }
}
</script>
