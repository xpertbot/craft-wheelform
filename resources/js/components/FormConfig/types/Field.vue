<template>
<div class="wheelform-field" :style="getFieldStyle">
    <div class="field-details">
        <div class="wheelform-field-handle"><i class="fa fa-bars"></i></div>
        <a @click.prevent="updateFieldProperty('isActive', ! isActive)">{{ getFieldLabel }} <small class="pull-right">{{type}}</small></a>
    </div>
    <div class="field-content" v-show="isActive">
        <div class="row">
            <div class="col">
                <div>
                    <label class="required">{{'Name'|t('wheelform')}}:</label>
                    <input type="text" :value="name" @input="updateFieldProperty('name', $event.target.value)" class="text" />
                    <p v-show="getErrorFor('name') !== null" style="color: #da5a47">{{ getErrorFor('name') }}</p>
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
                <FieldOptions
                    v-for="(config, i) in leftSideConfigurations"
                    :config="config"
                    :options="options"
                    :index="index"
                    :key="i"
                >
                </FieldOptions>
            </div>
            <div class="col">
                <FieldOptions
                    v-for="(config, i) in rightSideConfigurations"
                    :config="config"
                    :options="options"
                    :index="index"
                    :key="i"
                >
                </FieldOptions>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <FieldOptions
                    v-for="(config, i) in bothSideConfigurations"
                    :config="config"
                    :options="options"
                    :index="index"
                    :key="i"
                >
                </FieldOptions>
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
import Lightswitch from '../Lightswitch.vue';
import FieldOptions from '../partials/FieldOptions.vue';
import draggable from 'vuedraggable';
import { get } from 'lodash';

export default {
    props: [
        "isActive",
        "index",
        "name",
        "required",
        "type",
        "index_view",
        "options",
        "configuration",
        "errors",
    ],
    data(){
        return {
        }
    },
    components: {
        Lightswitch,
        draggable,
        FieldOptions,
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
        leftSideConfigurations()
        {
            return this.configuration.filter((el) => {
                let display = (el.display_side && el.display_side == 'left');
                if (el.condition) {
                    const conditional_value = get(this, el.condition);
                    if (! conditional_value) {
                        display = false;
                    }
                }
                return display;
            });
        },
        bothSideConfigurations()
        {
            return this.configuration.filter((el) => {
                let display = (el.display_side && el.display_side == 'both');
                if (el.condition) {
                    const conditional_value = get(this, el.condition);
                    if (! conditional_value) {
                        display = false;
                    }
                }
                return display;
            });
        },
        rightSideConfigurations()
        {
            return this.configuration.filter((el) => {
                // Right side is default, is property is missing move it to right side
                return (! el.display_side || el.display_side == 'right')
            });
        }
    },
    methods: {
        handleStatusChange(key, boolValue)
        {
            const value = (boolValue ? 1 : 0);
            this.$emit('update-field-property', this.index, key, value);
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
        getErrorFor(property) {
            if(this.errors[property]) {
                return this.errors[property];
            }
            return null;
        },
    }
}
</script>
