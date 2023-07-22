<template>
    <not-found v-if="is404" :path="path" />
    <div class="single post page" v-else>
        <h1 class="post-title">{{ post.title }}</h1>
        <div class="post-thumbnail" v-html="thumbnail"></div>
        <article class="post-content" v-html="post.content"></article>
    </div>
</template>

<script>
import postMixin from '../mixins/post.js';

export default {
    name: 'Page',
    mixins: [ postMixin ],
    beforeMount() {
        const slug = this.$route.params.slug;
        this.getPost(slug, 'pages');
    }
}
</script>

<style lang="scss">
.page {
    
}
</style>