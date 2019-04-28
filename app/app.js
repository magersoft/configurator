import Vue from 'vue';
import Vuetify from 'vuetify';
import App from './layout.vue';
import router from './routes.js';
import store from './store';

import infiniteScroll from 'vue-infinite-scroll';
import Vuelidate from 'vuelidate';

require('./bootstrap');

Vue.use(Vuetify);
Vue.use(infiniteScroll);
Vue.use(Vuelidate);

const app = new Vue({
    store,
    router,
    render: h => h (App)
}).$mount('#app');