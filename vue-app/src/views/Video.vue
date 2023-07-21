<template>
    <not-found v-if="is404" :path="path" />
    <div class="post video" v-else>
        <h1 class="post-title">{{ post.title }}</h1>
        <taxonomy-links 
            :taxonomyLinks="taxonomyLinks" 
            :taxonomies="{ video_category: t('Video category') }"
        />
        <div class="post-thumbnail" v-html="thumbnail"></div>
        <article class="post-content" v-html="post.content"></article>
    </div>
</template>

<script>
import TaxonomyLinks from '../components/TaxonomyLinks.vue';
import postMixin from '../mixins/post.js';

export default {
    name: 'Video',
    components: { TaxonomyLinks },
    mixins: [ postMixin ],
    data() {
        return {
            videoCategoryLinks: [],
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