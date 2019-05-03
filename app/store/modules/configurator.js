const state = {
    configurations: [],
    configurationProducts: [],
    currentConfiguration: null
};
const getters = {
    PRODUCTS: state => state.configurationProducts,
    CONFIGURATIONS: state => state.configurations,
    CURRENT_CONFIGURATION: state => state.currentConfiguration
};
const mutations = {
    SET_PRODUCTS: (state, payload) => {
        state.configurationProducts = payload;
    },
    ADD_PRODUCT: (state, payload) => {
        state.configurationProducts = state.configurationProducts.map(item => item.id === payload.category_id ? payload : item);
    },
    DELETE_PRODUCT: (state, payload) => {
        state.configurationProducts.splice(state.configurationProducts.indexOf(payload), 1);
    },

    SET_CURRENT_CONFIGURATION: (state, payload) => {
        state.currentConfiguration = payload;
    },
    UPDATE_CURRENT_CONFIGURATION: (state, payload) => {
      state.currentConfiguration.status = payload;
    },
    SET_CONFIGURATIONS: (state, payload) => {
        state.configurations = payload;
    },
    ADD_CONFIGURATION: (state, payload) => {
        state.configurations.push(payload);
    },
    UPDATE_CONFIGURATION: (state, payload) => {
        state.configurations = state.configurations.map(item => item.id === payload.id ? payload : item);
    },
    DELETE_CONFIGURATION: (state, payload) => {
        state.configurations.splice(state.configurations.indexOf(payload), 1);
    }
};
const actions = {
    async CREATE_CONFIGURATION(context, payload) {
        let { data } = await axios.post('/api/create-configuration').then(response => response);
        context.commit('SET_CURRENT_CONFIGURATION', data.configuration);
        context.commit('SET_PRODUCTS', data.result);
    },
    async SAVE_PRODUCT(context, payload) {
        let { data } = await axios.post('/api/add-product', { id: this.getters.CURRENT_CONFIGURATION.id, product_id: payload.id });
        context.commit('ADD_PRODUCT', payload);
        context.commit('UPDATE_CURRENT_CONFIGURATION', 0);
    },
    async REMOVE_PRODUCT(context, payload) {
        let { data } = await axios.delete('/api/remove-product', { params: { id: this.getters.CURRENT_CONFIGURATION.id, product_id: payload.id } });
        await context.commit('SET_PRODUCTS', data.result);
        context.commit('UPDATE_CURRENT_CONFIGURATION', 0);
    },

    async GET_CONFIGURATIONS(context, payload) {
        let { data } = await axios.get('/api/get-configurations').then(response => response);
        context.commit('SET_CURRENT_CONFIGURATION', data.current_configuration);
        context.commit('SET_CONFIGURATIONS', data.configurations);
    },
    async GET_CONFIGURATION(context, payload) {
        let { data } = await axios.get('/api/get-configuration', { params: { id: payload } }).then(response => response);
        context.commit('SET_CURRENT_CONFIGURATION', data.configuration);
        context.commit('SET_PRODUCTS', data.result);
    },
    async SAVE_CONFIGURATION(context, payload) {
        let { data } = await axios.post('/api/save-configuration', { id: payload.id, name: payload.name });
        context.commit('ADD_CONFIGURATION', payload);
        context.commit('UPDATE_CURRENT_CONFIGURATION', 1);
    },
    async UPDATE_CONFIGURATION(context, payload) {
        let { data } = await axios.post('/api/save-configuration', { id: payload.id, name: payload.name });
        context.commit('UPDATE_CONFIGURATION', payload)
    },
    async REMOVE_CONFIGURATION(context, payload) {
        let { data } = await axios.delete('/api/delete-configuration', { params: { id: payload } });
        context.commit('DELETE_CONFIGURATION', payload);
    }
};

export default {
    state,
    getters,
    mutations,
    actions
}