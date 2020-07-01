<template>
    <div>
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
            <div v-if="config.hasOwnProperty('description')" style="text-decoration: italic; font-size: 12px;"> {{ config.description|t('wheelform') }}</div>
        </div>
        <div v-else-if="config.type == 'list'">
            <div>
                <label :for="'field' + index + '-' + config.name">{{config.label | t('wheelform')}}</label>
                <input class="new-option"
                    type="text"
                    :id="'field' + index + '-' + config.name"
                    autocomplete="off"
                    :placeholder="config.label|t('wheelform')"
                    v-model="newOption"
                >
                <a href="" @click.prevent="addOption" class="form-field-add">{{'Add'|t('wheelform')}}</a>
            </div>
            <div>
                <draggable
                    :list="options[config.name]"
                    class="list-wrapper"
                >
                    <div
                        v-for="(item, key) in options[config.name]"
                        :key="key"
                        class="list-wrapper-item"
                    >
                        {{ item }}
                        <a href="" @click.prevent="deleteFieldOptionItem(key)" class="form-field-rm">X</a>
                    </div>
                </draggable>
            </div>
        </div>
    </div>
</template>

<script>
import Lightswitch from '../Lightswitch.vue';
import draggable from 'vuedraggable';

export default {
    components: {
        draggable,
        Lightswitch,
    },
    props: [
        'index',
        'config',
        'options', //Already saved values from Database
    ],
    data() {
        return {
            newOption: '',
        }
    },
    methods: {
        handleStatusOptionChange(key, boolValue)
        {
            const value = (boolValue ? 1 : 0);
            this.$parent.$emit('update-field-option', this.index, key, value);
        },
        updateFieldOptionProperty(property, value) {
            this.$parent.$emit('update-field-option', this.index, property, value);
        },
        addOption() {
            var value = this.newOption.trim();
            if (!value) {
                return
            }
            this.newOption = '';

            let items = this.options.items;
            items.push(value);
            this.$parent.$emit('update-field-option', this.index, 'items', items);
        },
        deleteFieldOptionItem(index)
        {
            let items = this.options.items;
            items.splice(index, 1);
            this.$parent.$emit('update-field-option', this.index, 'items', items);
        },
    }
}
</script>