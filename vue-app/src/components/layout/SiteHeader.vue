<template>
    <div class="site-header">
        <div class="site-name">
            <h1 class="site-title">{{ t('Vue WP theme') }}</h1>
        </div>
        <wp-menu 
            v-if="info.settings.header_menu" 
            :menu="info.settings.header_menu" 
        />
    </div>
</template>

<script>
import WpMenu from '../WpMenu.vue';

export default {
    name: 'SiteHeader',
    components: { WpMenu }
}
</script>

<style lang="scss">
.site-header {
    text-align: left;
    display: flex;
    flex-direction: row;
    background-color: #efefef;
    border-bottom: 1px solid #ccc;
    padding: 0.5rem 45px;

    h1 {
        margin: 0;
        padding: 0;
        flex-grow: 1;
        flex-shrink: 3;
    }

    .wp-menu {
        text-align: right;
        flex-grow: 2;
        flex-shrink: 2;
    }

    .langs-menu {
        width: 180px;
        flex-grow: 0;
        flex-shrink: 2;
        text-align: right;
    }
}
</style>