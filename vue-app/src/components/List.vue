<template>
    <div :class="`list-posts type-${postType} list`">
        <ul>
            <li v-for="post, index in posts" :key="index">
                <router-link 
                    :to="`${info.basePath}/${typeName(post)}/${post.slug}`" 
                    :class="`${postType}-link`"
                >
                    <div 
                        v-if="thumbnails" 
                        class="thumbnail" 
                        v-html="thumbHTML[post.slug]"
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
            posts: [],
            thumbHTML: {}
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
        },
        normalizeTaxName(name) {
            if ('category' == name) {
                return 'categories';
            }
            if ('tag' == name || 'post_tag' == name) {
                return 'tags';
            }
            return name;
        },
        getThumbHTML(post) {
            this.getThumbnailHTML(post).then(html => {
                const add = {};
                add[post.slug] = html;
                this.thumbHTML = { ...this.thumbHTML, ...add };
            });
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
        } else {
            this.loading(true);
            const _fields = ['slug', 'title'];
            const params = { _fields, per_page: this.perPage };
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
        this.posts.forEach(async p => await this.getThumbHTML(p));
    }
}
</script>