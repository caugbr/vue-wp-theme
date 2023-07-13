<template>
    <div class="site-header">
        <div class="langs">
            <langs-menu />
        </div>
        <h1>Header</h1>
    </div>
</template>

<script>
import LangsMenu from './LangsMenu.vue';

export default {
    components: { LangsMenu },
    name: 'SiteHeader'
}
</script>

<style lang="scss">
.site-header {
    
}
</style>