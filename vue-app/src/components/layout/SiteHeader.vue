<template>
    <div class="site-header">
        <h1>{{ t('Vue WP theme') }}</h1>
        <wp-menu 
            v-if="info.settings.header_menu" 
            :menu="info.settings.header_menu" 
        />
        <langs-menu />
    </div>
</template>

<script>
import LangsMenu from '../LangsMenu.vue';
import WpMenu from '../WpMenu.vue';

export default {
    name: 'SiteHeader',
    components: { LangsMenu, WpMenu }
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