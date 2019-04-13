import Vue from 'vue';
import router from './routes.js';
import {store} from './store';

import infiniteScroll from 'vue-infinite-scroll';
import Vuelidate from 'vuelidate';

require('./bootstrap');

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
    data: {

    },
    methods: {
        isActiveMenu(path) {
            return window.location.pathname == path;
        },
    }
});