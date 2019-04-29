import Vue from 'vue';
import Vuetify from 'vuetify';
import App from './layout.vue';
import router from './routes.js';
import store from './store';

require('./plugins');
require('./bootstrap');

Vue.use(Vuetify);

const app = new Vue({
    store,
    router,
    render: h => h (App)
}).$mount('#app');