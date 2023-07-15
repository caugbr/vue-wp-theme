<template>
    <div class="page-archive">
        <h1>{{ t('Pages') }}</h1>
        <list postType="page" />
    </div>
</template>

<script>
import List from '../components/List.vue';

export default {
    name: 'PageArchive',
    components: { List }
}
</script>

<style lang="scss">
.page-archive {
    
}
</style>