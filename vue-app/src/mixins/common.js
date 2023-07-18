import WpApi from '../assets/js/wp-api.js';

export default {
    computed: {
        api() {
            return new WpApi(this.info.wpApiSettings);
        },
        info() {
            return window.vueWpThemeInfo;
        },
        redrawKey() {
            return this.$store.state.redrawKey;
        }
    },
    methods: {
        getApi(settings) {
            const cfg = { ...this.info.wpApiSettings, ...settings };
            return new WpApi(cfg);
        },
        async apiCall(method, ...params) {
            this.loading(true);
            const ret = await this.api[method](...params);
            this.loading(false);
            return ret;
        },
        loading(obj) {
            this.$store.dispatch('waiting', obj);
        },
        redraw() {
            this.$store.dispatch('setRedraw');
        },
        toggleMenu() {
            document.body.classList.toggle('menu-open');
        }
    }
};