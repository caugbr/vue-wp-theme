import NotFound from '../views/NotFound';

export default {
    components: {
        NotFound
    },
    data() {
        return {
            post: {
                id: '',
                title: '',
                content: ''
            },
            taxonomyLinks: {
                categories: [],
                tags: []
            },
            thumbnail: '',
            thumbnail_size: 'medium',
            is404: false,
            path: window.location.pathname
        }
    },
    methods: {
        normalizeStringsArray(arr) {
            let narr = [];
            for (let index = 0; index < arr.length; index++) {
                const nobj = this.normalizeStrings(arr[index]);
                narr.push(nobj);
            }
            return narr;
        },
        normalizeStrings(obj) {
            let nobj = {};
            for (const key in obj) {
                if (typeof obj[key] == 'object' && obj[key].rendered) {
                    nobj[key] = obj[key].rendered;
                } else {
                    nobj[key] = obj[key];
                }
            }
            return nobj;
        },
        getPost(slug, postType, fields = []) {
            this.loading(true);
            return new Promise(resolve => {
                const api = this.api;
                api.fields = api.fields.concat(fields);
                api.embed = true;
                api.getBySlug(postType, slug).then(async video => {
                    if (video.data.length) {
                        this.post = this.normalizeStrings(video.data[0]);
                        this.thumbnail = await this.getThumbnailHTML(this.post);
                    } else {
                        this.is404 = true;
                    }
                    this.loading(false);
                    resolve(!this.is404);
                });
            })
        },
        async getMediaInfo(id) {
            const res = await this.api._get(`/media/${id}`);
            return {
                id: res.data.id,
                post_id: res.data.post,
                ...res.data.media_details
            };
        },
        getThumbnailId(post) {
            if (post && post._embedded) {
                if (post._embedded['wp:featuredmedia']) {
                    if (undefined !== post._embedded['wp:featuredmedia'][0]) {
                        return post._embedded['wp:featuredmedia'][0].id ?? 0;
                    }
                }
            }
            return 0;
        },
        async getThumbnailHTML(post, fallbackFull = true) {
            const mid = this.getThumbnailId(post);
            if (mid) {
                const media = await this.getMediaInfo(mid);
                let url = media.sizes[this.thumbnail_size]?.source_url;
                if (fallbackFull && !media.sizes[this.thumbnail_size]) {
                    url = media.sizes.full?.source_url ?? '';
                }
                return url ? `<img src="${url}" class="${this.post.title}" alt="">` : '';
            }
            return '';
        },
        async getTerm(tax, term) {
            const api = this.getApi({ namespace: 'vuewp/v1' });
            return api._get(`/term/${tax}/${term}`);
        },
        termLink(term, type = 'link') {
            let url = `${this.info.basePath}/${term.post_type}/${term.taxonomy}/${term.term_id}`;
            if (type == 'url') {
                return url;
            }
            return `<a href="${url}">${term.name}</a>`;
        },
        setTermLinks(taxName) {
            if (this.post[taxName] && this.post[taxName].length) {
                const taxTerms = this.post[taxName];
                if (!this.taxonomyLinks[taxName]) {
                    this.taxonomyLinks[taxName] = [];
                }
                taxTerms.forEach(taxTerm => {
                    let singular = taxName;
                    if ('categories' == taxName) {
                        singular = 'category';
                    }
                    if ('tags' == taxName) {
                        singular = 'post_tag';
                    }
                    this.getTerm(singular, taxTerm).then(term => {
                        this.taxonomyLinks[taxName].push(this.termLink(term.data));
                    });
                });
            }
        }
    }
}