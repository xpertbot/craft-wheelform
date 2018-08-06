import Vue from 'vue';
import Container from './components/Container.vue';

if(document.getElementById('formapp'))
{
    new Vue({
        el: '#formapp',
        components: {
            Container
        },
    })
}
