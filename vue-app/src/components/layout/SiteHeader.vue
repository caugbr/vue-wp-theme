<template>
    <div class="site-header">
        <langs-menu />
        <h1>{{ t('Header') }}</h1>
        <wp-menu menu="my-menu" />
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
    
}
</style>