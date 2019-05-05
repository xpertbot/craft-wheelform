import Vue from 'vue';
import { t } from './filters/filters';
import Container from './components/FormConfig/Container.vue';
import Field from './components/FormConfig/types/Field.vue';
import Html from './components/FormConfig/types/Html.vue';
import Editor from './components/Partials/Editor.vue';

// require styles
import 'codemirror/lib/codemirror.css';
import 'codemirror/mode/xml/xml.js';

Vue.filter('t', t)
Vue.component('Field', Field);
Vue.component('Html', Html);
Vue.component('Editor', Editor);

if(document.getElementById('wheelform-app')) {
    new Vue({
        el: '#wheelform-app',
        components: {
            Container
        },
    })
}
