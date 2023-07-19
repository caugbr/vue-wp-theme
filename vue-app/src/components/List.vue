<template>
    <div :class="`list-posts type-${postType} list`">
        <ul>
            <li v-for="post, index in posts" :key="index">
                <router-link 
                    :to="`${info.basePath}/${typeName(post)}/${post.slug}`" 
                    :class="postType"
                >
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
            default: ''
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
        },
        postList: {
            type: Array,
            default() {
                return [];
            }
        }
    },
    data() {
        return {
            posts: []
        };
    },
    methods: {
        typeName(post) {
            if (post.type) {
                return this.normalizeName(post.type);
            }
            return this.urlName;
        },
        normalizeName(name) {
            if (/^(post|page)$/.test(name)) {
                return name + 's';
            }
            return name;
        }
    },
    computed: {
        urlName() {
            return this.normalizeName(this.postType);
        },
        items() {
            if (this.postList.length) {
                return this.postList;
            }
            return this.posts;
        }
    },
    async mounted() {
        if (this.postList.length) {
            this.posts = this.postList;
            return;
        }
        this.loading(true);
        const params = { _fields: ['slug', 'title'], per_page: this.perPage };
        if (this.thumbnails) {
            this.api.embed = true;
        }
        if (this.taxonomy && this.term) {
            const { postType, taxonomy, term } = this;
            const posts = await this.apiCall('listByTaxonomy', postType, taxonomy, term, params);
            this.posts = this.normalizeStringsArray(posts.data);
        } else {
            const posts = await this.apiCall('listByPostType', this.urlName, params);
            this.posts = this.normalizeStringsArray(posts.data);
        }
    }
}
</script>