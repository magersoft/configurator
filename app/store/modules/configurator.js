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
        state.configurationProducts = state.configurationProducts.map(item => {
            if (item.id === payload.category_id) {
                return payload;
            } else if (item.category_id === payload.category_id) {
                return payload;
            } else {
                return item;
            }
        });
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
    },

    PLUS_TOTAL_PRICE: (state, payload) => {
        state.currentConfiguration.total_price += +payload;
    },
    MINUS_TOTAL_PRICE: (state, payload) => {
        state.currentConfiguration.total_price -= +payload;
    }
};
const actions = {
    async CREATE_CONFIGURATION(context, payload) {
        let { data } = await axios.post('/api/create-configuration').then(response => response);
        await context.commit('SET_CURRENT_CONFIGURATION', data.configuration);
        await context.commit('SET_PRODUCTS', data.result);
    },
    async SAVE_PRODUCT(context, payload) {
        await context.commit('PLUS_TOTAL_PRICE', payload.prices.citilink.regular_price);
        let { data } = await axios.post('/api/add-product', {
                id: this.getters.CURRENT_CONFIGURATION.id,
                product_id: payload.id,
                total_price: this.getters.CURRENT_CONFIGURATION.total_price
        });
        // todo: обработать ошибки с сервера в сторе
        await context.commit('ADD_PRODUCT', payload);
        await context.commit('UPDATE_CURRENT_CONFIGURATION', 0);
    },
    async REMOVE_PRODUCT(context, payload) {
        await context.commit('MINUS_TOTAL_PRICE', payload.prices.citilink.regular_price);
        let { data } = await axios.delete('/api/remove-product', {
            params: {
                id: this.getters.CURRENT_CONFIGURATION.id,
                product_id: payload.id,
                total_price: this.getters.CURRENT_CONFIGURATION.total_price
            }
        });
        await context.commit('SET_PRODUCTS', data.result);
        await context.commit('UPDATE_CURRENT_CONFIGURATION', 0);
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

        const update = this.getters.CONFIGURATIONS.find(item => item.id === payload.id);

        if (update) {
            await context.commit('UPDATE_CONFIGURATION', payload)
        } else {
            await context.commit('ADD_CONFIGURATION', payload);
        }
        await context.commit('UPDATE_CURRENT_CONFIGURATION', 1);
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