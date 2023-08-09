<template>
    <div :class="`post-thumbnail size-${size}`" v-if="thumbnailHtml" v-html="thumbnailHtml"></div>
</template>

<script>
export default {
    name: 'Thumbnail',
    props: {
        post: {
            type: Object,
            required: true
        },
        size: {
            type: String,
            default: 'post-thumbnail'
        }
    },
    data() {
        return {
            thumbnailHtml: ''
        };
    },
    mounted() {
        this.setThumbnail();
    },
    methods: {
        async getMediaInfo(id) {
            const res = await this.api._get(`/media/${id}`);
            return {
                id: res.data.id,
                post_id: res.data.post,
                ...res.data.media_details
            };
        },
        getThumbnailId() {
            if (this.post && this.post._embedded) {
                if (this.post._embedded['wp:featuredmedia']) {
                    if (undefined !== this.post._embedded['wp:featuredmedia'][0]) {
                        return this.post._embedded['wp:featuredmedia'][0].id ?? 0;
                    }
                }
            }
            return 0;
        },
        async getThumbnailHTML(fallbackFull = true) {
            if (!this.post.id) {
                return '';
            }
            const mid = this.getThumbnailId();
            if (mid) {
                const media = await this.getMediaInfo(mid);
                let url = media.sizes[this.size]?.source_url;
                if (fallbackFull && !media.sizes[this.size]) {
                    url = media.sizes.full?.source_url ?? '';
                }
                return url ? `<img src="${url}" class="${this.post.title}" alt="">` : '';
            }
            return '';
        },
        setThumbnail() {
            this.getThumbnailHTML().then(html => {
                this.thumbnailHtml = html;
            });
        }
    },
    watch: {
        post(post) {
            if (post.id) {
                this.setThumbnail();
            }
        }
    }
}
</script>