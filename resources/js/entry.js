import Vue from 'vue';
import { t } from './filters/filters';
import Container from './components/FormConfig/Container.vue';

Vue.filter('t', t)

if(document.getElementById('wheelform-app'))
{
    new Vue({
        el: '#wheelform-app',
        components: {
            Container
        },
    })
}
