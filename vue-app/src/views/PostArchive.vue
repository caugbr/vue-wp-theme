<template>
    <div class="post-archive">
        <h1>{{ t('Posts') }}</h1>
        <list postType="post" />
    </div>
</template>

<script>
import List from '../components/List.vue';

export default {
    name: 'PostArchive',
    components: { List }
}
</script>

<style lang="scss">
.post-archive {
    
}
</style>