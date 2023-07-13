<template>
    <div :class="`list-posts type-${postType}`">
        <ul>
            <li v-for="post, index in posts" :key="index">
                <router-link :to="`${info.basePath}/${urlName}/${post.slug}`">
                    <div 
                        v-if="thumbnails" 
                        class="thumbnail" 
                        v-html="getThumbnailImg(post)"
                    ></div>
                    <div class="post-title">{{ post.title }}</div>
                </router-link>
            </li>
        </ul>
    </div>
</template>

<script>
import postMixin from '../mixins/post.js';

export default {
    name: 'List',
    mixins: [postMixin],
    props: {
        perPage: {
            type: Number,
            default: 10
        },
        postType: {
            type: String,
            default: 'post'
        },
        thumbnails: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            posts: []
        };
    },
    computed: {
        urlName() {
            if (this.postType == 'post' || this.postType == 'page') {
                return this.postType + 's';
            }
            return this.postType;
        }
    },
    beforeMount() {
        const params = { _fields: ['slug', 'title'], per_page: this.perPage };
        if (this.thumbnails) {
            this.api.embed = true;
        }
        this.api.getList(this.urlName, params).then(posts => {
            this.posts = this.normalizeStringsArray(posts.data);
        });
    }
}
</script>

<style lang="scss">
.List-posts {
    
}
</style>