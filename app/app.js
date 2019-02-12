import router from './routes.js';

document.onreadystatechange = function () {
    if (document.readyState === 'interactive') {
        // JS modules
    }
};

require('./bootstrap');

const Vue = require('vue');

const app = new Vue({
    el: '#app',
    router,
    data: {

    },
    methods: {
        isActiveMenu(path) {
            return window.location.pathname == path;
        },
    }
});