import Vue from 'vue';
import router from './routes.js';
import {store} from './store/index';
import infiniteScroll from 'vue-infinite-scroll';

require('./bootstrap');

Vue.use(infiniteScroll);

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