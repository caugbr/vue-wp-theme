import Vue from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';
import common from './mixins/common.js';

import I18n, { i18nMixin } from "./I18n"
Vue.use(I18n);
Vue.mixin(i18nMixin);

Vue.mixin(common);
Vue.config.productionTip = false;

new Vue({
    router,
    store,
    render: h => h(App)
}).$mount('#app');
