import WpAPI from '../assets/js/api.js';

export default {
    computed: {
        lang() {
            return this.$store.state.lang;
        },
        api() {
            return new WpAPI(this.info.wpApiSettings);
        },
        info() {
            return window.apiInfo;
        }
    },
    methods: {
        loading(obj) {
            this.$store.dispatch('waiting', obj);
        }
    }
};