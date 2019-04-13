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
        <div v-for="(config, i) in config"
            :key="i"
        >
            <Lightswitch
                v-if="config.type == 'boolean'"
                :name="config.name"
                :label="config.label|t('wheelform')"
                :status="options[config.name]"
                @handle-status-change="handleStatusOptionChange"
                />
            <div v-else-if="config.type == 'text'">
                <label :for="'field' + index + '-' + config.name">{{config.label|t('wheelform')}}</label>
                <input type="text" :id="'field' + index + '-' + config.name" :value="options[config.name]" @input="updateFieldOptionProperty(config.name, $event.target.value)">
            </div>
            <div v-else-if="config.type == 'list'">
                <div>
                    <label :for="'field' + index + '-' + config.name">{{config.label | t('wheelform')}}</label>
                    <input class="new-option"
                        :id="'field' + index + '-' + config.name"
                        autocomplete="off"
                        :placeholder="config.label|t('wheelform')"
                        :value="newOption"
                        @input="newOption = $event.target.value"
                    >
                    <a href="" @click.prevent="addOption" class="form-field-add">{{'Add'|t('wheelform')}}</a>
                </div>
                <div>
                    <ul class="list-wrapper">
                        <li
                            v-for="(item, key) in options[config.name]"
                            :key="key"
                        >
                            {{ item }}
                            <a href="" @click.prevent="deleteFieldOptionItem($event.target.value)" class="form-field-rm">X</a>
                        </li>
                    </ul>
                </div>
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
        "config",
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
