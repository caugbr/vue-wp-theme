<template>
    <not-found v-if="is404" :path="path" />
    <div class="single post video" v-else>
        <h1 class="post-title">{{ post.title }}</h1>
        <taxonomy-links 
            :taxonomyLinks="taxonomyLinks" 
            :taxonomies="{ video_category: t('Video category') }"
        />
        <thumbnail :post="post" />
        <article class="post-content" v-html="post.content"></article>
    </div>
</template>

<script>
import Thumbnail from '../components/Thumbnail.vue';
import TaxonomyLinks from '../components/TaxonomyLinks.vue';
import postMixin from '../mixins/post.js';

export default {
    name: 'Video',
    components: { Thumbnail, TaxonomyLinks },
    route_params: 'slug',
    mixins: [ postMixin ],
    data() {
        return {
            taxonomyLinks: {
                video_category: []
            }
        }
    },
    beforeMount() {
        const slug = this.$route.params.slug;
        this.getPost(slug, 'video', ['video_category']).then(() => {
            this.setTermLinks('video_category');
        });
    }
}
</script>

<style lang="scss">
.video {
    
}
</style>