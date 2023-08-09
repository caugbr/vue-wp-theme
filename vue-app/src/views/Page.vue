<template>
    <not-found v-if="is404" :path="path" />
    <div class="single post page" v-else>
        <h1 class="post-title">{{ post.title }}</h1>
        <thumbnail :post="post" />
        <article class="post-content" v-html="post.content"></article>
    </div>
</template>

<script>
import Thumbnail from '../components/Thumbnail.vue';
import postMixin from '../mixins/post.js';

export default {
    name: 'Page',
    components: { Thumbnail },
    mixins: [ postMixin ],
    route_params: 'slug',
    beforeMount() {
        const slug = this.$route.params.slug;
        this.getPost(slug, 'pages');
    }
}
</script>