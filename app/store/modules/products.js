const state = {
    products: []
};
const getters = {
    PRODUCTS: state => {
        return state.products;
    }
};
const mutations = {
    SET_PRODUCT: (state, payload) => {
        state.products = payload;
    },

    ADD_PRODUCT: (state, payload) => {
        state.products.push(payload);
    },

    DELETE_PRODUCT: (state, payload) => {
        state.products.splice(state.products.indexOf(payload), 1);
    }
};
const actions = {
    async GET_PRODUCTS(context, payload) {
        let { data } = await axios.get('/api/vuex').then(response => response);
        context.commit('SET_PRODUCT', data.products);
    },

    async SAVE_PRODUCT(context, payload) {
        let { data } = await axios.post('/api/vuex', { id: payload.id });
        context.commit('ADD_PRODUCT', payload);
    },

    async REMOVE_PRODUCT(context, payload) {
        let { data } = await axios.delete('/api/vuex', { params: { id: payload.id } });
        context.commit('DELETE_PRODUCT', payload);
    }
};

export default {
    state,
    getters,
    mutations,
    actions
}