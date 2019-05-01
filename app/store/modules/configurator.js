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
    SET_PRODUCT: (state, payload) => {
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

    SET_CONFIGURATION: (state, payload) => {
        state.configurations = payload;
    },
    ADD_CONFIGURATION: (state, payload) => {
        state.configurations.push(payload);
    },
    DELETE_CONFIGURATION: (state, payload) => {
        state.configurations.splice(state.configurations.indexOf(payload), 1);
    }
};
const actions = {
    async CREATE_CONFIGURATION(context, payload) {
        let { data } = await axios.get('/api/create-configuration').then(response => response);
        context.commit('SET_CURRENT_CONFIGURATION', data.configuration);
        context.commit('SET_PRODUCT', data.result);
    },
    async SAVE_PRODUCT(context, payload) {
        let { data } = await axios.post('/api/create-configuration', { id: payload.id });
        context.commit('ADD_PRODUCT', payload);
    },
    async REMOVE_PRODUCT(context, payload) {
        let { data } = await axios.delete('/api/create-configuration', { params: { id: payload.id } });
        await context.commit('DELETE_PRODUCT', data.result);
    },

    async GET_CONFIGURATIONS(context, payload) {
        let { data } = await axios.get('/api/get-configurations').then(response => response);
        context.commit('SET_CONFIGURATION', data.configurations);
    },
    async SAVE_CONFIGURATION(context, payload) {
        let { data } = await axios.post('/api/save-configuration', { id: payload.id, name: payload.name });
        context.commit('ADD_CONFIGURATION', payload)
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