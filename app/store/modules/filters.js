const state = {
    amd: false,
    intel: false
};
const getters = {
    AMD: state => state.amd,
    INTEL: state => state.intel,
};
const mutations = {
    SET_AMD: (state, payload) => {
        state.amd = payload;
    },
    SET_INTEL: (state, payload) => {
        state.intel = payload;
    }
};

export default {
    state,
    getters,
    mutations
}