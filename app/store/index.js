import Vue from "vue";
import Vuex from 'vuex';

import configurator from './modules/configurator';
import filters from './modules/filters';
import users from './modules/users';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {},
    getters: {},
    mutations: {},
    actions: {},
    modules: {
        configurator,
        filters,
        users,
    }
});