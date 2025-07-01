import axios from 'axios';

const state = () => ({
    token:      localStorage.getItem('token'),
    department: localStorage.getItem('department'),
    user:       JSON.parse(localStorage.getItem('user') || 'null'),
});

const getters = {
    isLogged:   s => !!s.token,
    department: s => s.department,
    user:       s => s.user,
};

const mutations = {
    SET_AUTH(state, payload) {
        Object.assign(state, payload);
        localStorage.setItem('token', payload.token);
        localStorage.setItem('department', payload.department);
        localStorage.setItem('user', JSON.stringify(payload.user));
        axios.defaults.headers.common.Authorization = `Bearer ${payload.token}`;
    },
    CLEAR_AUTH(state) {
        state.token = state.department = state.user = null;
        ['token', 'department', 'user'].forEach(k => localStorage.removeItem(k));
        delete axios.defaults.headers.common.Authorization;
    },
};

const actions = {
    async login({ commit }, { id, password }) {
        const { data } = await axios.post('/api/login', { id, password });
        commit('SET_AUTH', data);
        return data;               // let the caller redirect
    },
    logout({ commit }) {
        commit('CLEAR_AUTH');
    },
};

export default { state, getters, mutations, actions };
