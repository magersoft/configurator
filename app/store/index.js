import Vue from "vue";
import Vuex from 'vuex';

import products from './modules/products';
import filters from './modules/filters';
import users from './modules/users';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {},
    getters: {},
    mutations: {},
    actions: {},
    modules: {
        products,
        filters,
        users,
    }
});