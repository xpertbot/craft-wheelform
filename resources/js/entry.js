import Vue from 'vue';
import { t } from './filters/filters';
import Container from './components/FormConfig/Container.vue';
import Field from './components/FormConfig/types/Field.vue';
import Html from './components/FormConfig/types/Html.vue';

Vue.filter('t', t)
Vue.component('Field', Field);
Vue.component('Html', Html);

if(document.getElementById('wheelform-app'))
{
    new Vue({
        el: '#wheelform-app',
        components: {
            Container
        },
    })
}
