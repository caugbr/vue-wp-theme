<template>
    <not-found v-if="is404" :path="path" />
    <div class="single post" v-else>
        <div class="container">
            <div class="row">
                <div class="col-4">
                    <thumbnail :post="post" />
                </div>
                <div class="col-8">
                    <h1 class="post-title">{{ post.title }}</h1>
                    <taxonomy-links :taxonomyLinks="taxonomyLinks" />
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <article class="post-content" v-html="post.content"></article>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Thumbnail from '../components/Thumbnail.vue';
import TaxonomyLinks from '../components/TaxonomyLinks.vue';
import postMixin from '../mixins/post.js';

export default {
    name: 'Post',
    components: { Thumbnail, TaxonomyLinks },
    mixins: [ postMixin ],
    route_params: 'slug',
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
.post {
    text-align: left;
}
</style>