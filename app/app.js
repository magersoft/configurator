import router from './routes.js';
import infiniteScroll from 'vue-infinite-scroll';

document.onreadystatechange = function () {
    if (document.readyState === 'interactive') {
        // JS modules
    }
};

require('./bootstrap');

const Vue = require('vue');
Vue.use(infiniteScroll);


const app = new Vue({
    el: '#app',
    router,
    data: {

    },
    directives: {
      infiniteScroll
    },
    methods: {
        isActiveMenu(path) {
            return window.location.pathname == path;
        },
    }
});