import Vue from 'vue';
import Vuetify from 'vuetify';
import router from './routes.js';
import store from './store';
import components from './components'

import infiniteScroll from 'vue-infinite-scroll';
import Vuelidate from 'vuelidate';
import 'vuetify/dist/vuetify.min.css';

require('./bootstrap');

Vue.use(Vuetify);
Vue.use(infiniteScroll);
Vue.use(Vuelidate);

document.onreadystatechange = function () {
    if (document.readyState === 'interactive') {
        // JS modules
    }
};

const app = new Vue({
    el: '#app',
    store,
    router,
    components,
    data: () => ({
        dark: false,
        primaryDrawer: {
            model: null,
            type: 'default (no property)',
            clipped: true,
            floating: false,
            mini: false
        },
    }),
    methods: {}
});