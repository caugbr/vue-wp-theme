<template>
  <div id="app">
    <site-header/>

    <div class="site-stage">
        <router-view/>
    </div>

    <site-footer/>

    <loading v-bind="loadingLayer" />
  </div>
</template>

<script>
import SiteHeader from './components/SiteHeader.vue';
import SiteFooter from './components/SiteFooter.vue';
import Loading from "@/components/Loading";

export default {
    name: 'App',
    components: { SiteHeader, SiteFooter, Loading },
    computed: {
        loadingLayer() {
            return this.$store.state.loadingLayer;
        }
    },
    watch: {
        '$store.state.language'(lng) {
            this.setLanguage(lng);
        }
    },
    beforeMount() {
        if (this.$store.state.language) {
            this.setLanguage(this.$store.state.language);
        }
    }
}
</script>

<style lang="scss">
    @import "./assets/scss/style.scss";
</style>
