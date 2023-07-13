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
                content: '',
                created: '',
                updated: '',
                author: {},
                categories: [],
                tags: []
            },
            thumbnail: '',
            loaded: false,
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
        getPost(slug, postType) {
            this.loading(true);
            return new Promise(resolve => {
                this.api.getBySlug(postType, slug).then(video => {
                    if (video.data.length) {
                        this.post = this.normalizeStrings(video.data[0]);
                        this.thumbnail = this.getThumbnailImg(this.post);
                    } else {
                        this.is404 = true;
                    }
                    this.loaded = true;
                    this.loading(false);
                    resolve(!this.is404);
                });
            })
        },
        getThumbnailImg(post) {
            const turl = this.getThumbnailUrl(post);
            return turl ? `<img src="${turl}" alt="thumbnail">` : '';
        },
        getThumbnailUrl(post) {
            if (post && post._embedded) {
                if (post._embedded['wp:featuredmedia']) {
                    if (undefined !== post._embedded['wp:featuredmedia'][0]) {
                        return post._embedded['wp:featuredmedia'][0].source_url ?? '';
                    }
                }
            }
            return '';
        }
    }
}