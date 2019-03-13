import Vue from 'vue';
import { t } from './filters/filters';
import Container from './components/FormConfig/Container.vue';

Vue.filter('t', t)

if(document.getElementById('formapp'))
{
    new Vue({
        el: '#formapp',
        components: {
            Container
        },
    })
}
