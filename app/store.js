import Vuex from 'vuex';

export default () => {
    return new Vuex.Store({
        state: {},
        mutations: {},
        actions: {
            // This action is called on each HTTP request
            // and will not be called in futures navigations
            async onHttpRequest({ commit }, { url }) {},
        },
    });
};