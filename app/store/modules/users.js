const state = {
    isLoggedIn: false,
    user: false
};
const getters = {
    LOGGED: state => state.isLoggedIn,
    USER: state => state.user,
};
const mutations =  {
    IS_LOGGED: (state, payload) => {
        state.isLoggedIn = payload;
    },
    SET_USER: (state, payload) => {
        state.user = payload;
    }
};
const actions = {
    async GET_LOGGED(context, payload) {
      let { data } = await axios.get('/api/logged').then(response => response);
      context.commit('IS_LOGGED', data.logged);
      context.commit('SET_USER', data.user);
    }
};

export default {
    state,
    getters,
    mutations,
    actions
}