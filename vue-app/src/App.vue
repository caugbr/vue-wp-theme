<template>
  <div id="app">
    <site-header/>

    <div class="content">
        <sidebar />
        <div class="site-stage" :key="redrawKey">
            <router-view/>
        </div>
    </div>

    <site-footer/>

    <loading v-bind="loadingLayer" />
  </div>
</template>

<script>
import SiteHeader from './components/layout/SiteHeader.vue';
import SiteFooter from './components/layout/SiteFooter.vue';
import Sidebar from './components/layout/Sidebar.vue';
import Loading from "@/components/Loading";

export default {
    name: 'App',
    components: {
        SiteHeader,
        Sidebar,
        SiteFooter,
        Loading
    },
    computed: {
        loadingLayer() {
            return this.$store.state.loadingLayer;
        }
    },
    watch: {
        '$store.state.language'(lng) {
            this.setLanguage(lng);
        },
        '$route'(to, from){
            // If we try to show different contents using
            // the same component, the visible content
            // does not change. We must force a redraw.
            if (from.name === to.name) {
                this.redraw();
            }
        }
    },
    beforeMount() {
        if (this.info.language && this.info.settings.use_wp_lang == '1') {
            const lang = this.normalizeLangCode(this.info.language);
            this.$store.dispatch('setLanguage', lang);
        }
    }
}
</script>

<style lang="scss">
    @import "./assets/scss/style.scss";
</style>
