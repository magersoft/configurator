import Vue from "vue";
import Vuex from 'vuex';

Vue.use(Vuex);

export const store = new Vuex.Store({
    state: {
        products: []
    },
    getters: {
        PRODUCTS: state => {
            return state.products;
        }
    },
    mutations: {
        SET_PRODUCT: (state, payload) => {
            state.products = payload;
        },

        ADD_PRODUCT: (state, payload) => {
            state.products.push(payload);
        }
    },
    actions: {
        GET_PRODUCT: async (context, payload) => {
            let { data } = await axios.get('/api/products');
            context.commit('SET_PRODUCT', data);
        },

        async SAVE_PRODUCT(context, payload) {
            let { data } = await axios.post('/api/vuex');
            context.commit('ADD_PRODUCT', payload);
        }
    }
});