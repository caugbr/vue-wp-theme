<template>
    <div class="site-header">
        <langs-menu />
        <h1>{{ t('Header') }}</h1>
    </div>
</template>

<script>
import LangsMenu from './LangsMenu.vue';

export default {
    name: 'SiteHeader',
    components: { LangsMenu }
}
</script>

<style lang="scss">
.site-header {
    
}
</style>