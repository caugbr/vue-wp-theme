<template>
    <div class="video-archive">
        <h1>{{ t('Videos') }}</h1>
        <list postType="video" />
    </div>
</template>

<script>
import List from '../components/List.vue';

export default {
    name: 'VideoArchive',
    components: { List }
}
</script>

<style lang="scss">
.video-archive {
    
}
</style>