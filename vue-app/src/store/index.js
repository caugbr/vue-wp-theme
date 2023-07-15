import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        redrawKey: 0,
        language: 'pt-br',
        loadingLayer: {
            bgColor: '#000000',
            bgOpacity: '0.6',
            textColor: '#FFFFFF',
            message: '',
            spinner: '<em class="fa fa-spinner"></em>',
            rotateSpinner: 1,
            visible: false
        }
    },
    mutations: {
        SET_LOADING_LAYER(state, payload) {
            state.loadingLayer = { ...state.loadingLayer, ...payload };
        },
        SET_LANGUAGE(state, payload) {
            state.language = payload;
        },
        SET_REDRAW_KEY(state) {
            state.redrawKey++;
        }
    },
    actions: {
        waiting({ commit }, payload) {
            if (typeof payload === 'boolean') {
                payload = { visible: payload };
            }
            commit('SET_LOADING_LAYER', payload);
        },
        setLanguage({ commit }, lang) {
            commit('SET_LANGUAGE', lang);
        },
        setRedraw({ commit }) {
            commit('SET_REDRAW_KEY');
        }
    }
});
