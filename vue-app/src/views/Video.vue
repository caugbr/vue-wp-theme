<template>
    <not-found v-if="is404" :path="path" />
    <div class="post video" v-else>
        <h1 class="post-title">{{ post.title }}</h1>
        <div class="tax-links">
            <div v-if="taxonomyLinks.video_category.length" class="video-category">
                <span 
                    class="tax-link" 
                    v-for="lnk, index in taxonomyLinks.video_category" 
                    :key="index" 
                    v-html="lnk"
                ></span>
            </div>
        </div>
        <div class="post-thumbnail" v-html="thumbnail"></div>
        <article class="post-content" v-html="post.content"></article>
    </div>
</template>

<script>
import postMixin from '../mixins/post.js';

export default {
    name: 'Video',
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
        this.getPost(slug, 'video').then(() => {
            this.setTermLinks('video_category');
            // if (this.post.video_category && this.post.video_category.length) {
            //     const vcTerms = this.post.video_category;
            //     vcTerms.forEach(vcTerm => {
            //         this.getTerm('video_category', vcTerm).then(term => {
            //             this.videoCategoryLinks.push(this.termLink(term.data));
            //         });
            //     });
            // }
        });
    }
}
</script>

<style lang="scss">
.video {
    
}
</style>