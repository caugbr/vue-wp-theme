import WpAPI from '../assets/js/api.js';

export default {
    computed: {
        api() {
            return new WpAPI(this.info.wpApiSettings);
        },
        info() {
            return window.vueWpThemeInfo;
        }
    },
    methods: {
        loading(obj) {
            this.$store.dispatch('waiting', obj);
        }
    }
};