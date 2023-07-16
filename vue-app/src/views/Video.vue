<template>
    <not-found v-if="is404" :path="path" />
    <div class="post video" v-else>
        <h1 class="post-title">{{ post.title }}</h1>
        <div class="post-thumbnail" v-html="thumbnail"></div>
        <article class="post-content" v-html="post.content"></article>
    </div>
</template>

<script>
import postMixin from '../mixins/post.js';

export default {
    name: 'Video',
    mixins: [ postMixin ],
    beforeMount() {
        const slug = this.$route.params.slug;
        this.getPost(slug, 'video');
    }
}
</script>

<style lang="scss">
.video {
    
}
</style>