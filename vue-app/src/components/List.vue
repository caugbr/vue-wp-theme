<template>
    <div :class="`list-posts type-${postType} list`">
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
        taxonomy: {
            type: String,
            default: ''
        },
        term: {
            type: String,
            default: ''
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
            if (/^(post|page|video)$/.test(this.postType)) {
                return this.postType + 's';
            }
            return this.postType;
        }
    },
    async beforeMount() {
        this.loading(true);
        const params = { _fields: ['slug', 'title'], per_page: this.perPage };
        if (this.thumbnails) {
            this.api.embed = true;
        }
        if (this.taxonomy && this.term) {
            const { postType, taxonomy, term } = this;
            const posts = await this.apiCall('listByTaxonomy', postType, taxonomy, term, params);
            this.posts = this.normalizeStringsArray(posts.data);
            // this.api.listByTaxonomy(postType, taxonomy, term, params).then(posts => {
            //     this.posts = this.normalizeStringsArray(posts.data);
            //     this.loading(false);
            // });
        } else {
            const posts = await this.apiCall('listByPostType', this.urlName, params);
            this.posts = this.normalizeStringsArray(posts.data);
            // this.api.listByPostType(this.urlName, params).then(posts => {
            //     this.posts = this.normalizeStringsArray(posts.data);
            //     this.loading(false);
            // });
        }
    }
}
</script>

<style lang="scss">
// .list-posts {
//     display: inline-block;
//     width: auto;
    
//     ul {
//         display: inline-block;
//         padding: 0;
//         margin: 0;
//         list-style-type: none;
//         list-style: none;
        
//         li {
//             display: inline-block;
//             padding: 0;
//             margin: 0;
//             list-style-type: none;
//             list-style: none;
//         }
//     }
// }
</style>