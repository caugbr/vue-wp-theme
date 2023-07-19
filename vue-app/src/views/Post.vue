<template>
    <not-found v-if="is404" :path="path" />
    <div class="post" v-else>
        <h1 class="post-title">{{ post.title }}</h1>
        <taxonomy-links :taxonomyLinks="taxonomyLinks" />
        <div class="post-thumbnail" v-html="thumbnail"></div>
        <article class="post-content" v-html="post.content"></article>
    </div>
</template>

<script>
import TaxonomyLinks from '../components/TaxonomyLinks.vue';
import postMixin from '../mixins/post.js';

export default {
    name: 'Post',
    components: { TaxonomyLinks },
    mixins: [ postMixin ],
    beforeMount() {
        const slug = this.$route.params.slug;
        this.getPost(slug, 'posts').then(() => {
            this.setTermLinks('categories');
            this.setTermLinks('tags');
        });
    }
}
</script>

<style lang="scss">
.page {
    
}
</style>