import axios from 'axios';

class WpApi {
    apiUrl;
    nonce;
    namespace;
    embed;
    fields;
    formats;

    constructor(info) {
        this.apiUrl = info.root;
        this.nonce = info.nonce;
        this.namespace = info.namespace ?? 'wp/v2';
        this.embed = info.embed ?? false;
        this.formats = info.formats ?? false;
        this.fields = info.fields ?? [
            'id', 'title', 'slug', 'content', 'author',
            'featured_media', 'categories', 'tags', 'type'
        ];
    }

    /**
     * Generic GET request
     * @param {string} url URL to get
     * @returns Promise
     */
    _get(url) {
        const config = { headers: { "X-WP-Nonce": this.nonce } };
        return axios.get(this.apiUrl + this.namespace + url, config);
    }

    /**
     * Generic POST request
     * @param {string} url      URL to get
     * @param {object} data     payload
     * @param {string} method   alternative method (PUT, PATCH, DELETE, etc)
     * @returns Promise
     */
    _post(url, data = {}, method = 'POST') {
        const config = { headers: { "X-WP-Nonce": this.nonce } };
        if (method !== 'POST') {
            config.headers['X-HTTP-Method-Override'] = method;
        }
        return axios.post(this.apiUrl + this.namespace + url, data, config);
    }

    /**
     * Returns the results of the search for searchTerm
     * Dependes on custom endpoint defined in extend-rest-api.php
     * @param {string} searchTerm   Search for this string
     * @param {object} params       Other request params
     * @returns array
     */
    getSearchResults(searchTerm, params = {}) {
        const urlParams = this.obj2url(params);
        this.namespace = 'vuewp/v1';
        return this._get(`/search/${searchTerm}${urlParams}`);
    }

    /**
     * Returns the menu items
     * Dependes on custom endpoint defined in extend-rest-api.php
     * @param {string} menuName     WP Menu name
     * @param {object} params       Other request params
     * @returns array
     */
    getMenu(menuName, params = {}) {
        this.embed = false;
        const urlParams = this.obj2url(params);
        return this._get(`/menu/${menuName}${urlParams}`);
    }

    /**
     * Returns a list of posts of the given taxonomy / term
     * @param {string} postType     Post type name
     * @param {string} taxonomy     Taxonomy name
     * @param {string} term         Term ID
     * @param {object} params       Other request params
     * @returns array
     */
    listByTaxonomy(postType, taxonomy, term, params = {}) {
        params[this.taxSlug(taxonomy)] = term;
        const urlParams = this.obj2url(params);
        this.embed = false;
        return this._get(`/${this.postSlug(postType)}${urlParams}`);
    }

    /**
     * Returns a list of posts of type postType
     * @param {string} postType     Post type name
     * @param {object} params       Other request params
     * @returns array
     */
    listByPostType(postType, params = {}) {
        const urlParams = this.obj2url(params);
        this.embed = false;
        return this._get(`/${this.postSlug(postType)}${urlParams}`);
    }

    /**
     * Returns a post of type postType havind ID == id
     * @param {string} postType     Post type name
     * @param {number} id           Post ID
     * @param {object} params       Other request params
     * @returns object
     */
    getById(postType, id, params = {}) {
        params.id = id;
        const urlParams = this.obj2url(params);
        this.embed = false;
        return this._get(`/${this.postSlug(postType)}${urlParams}`);
    }

    /**
     * Returns a post of type postType havind post_slug == slug
     * @param {string} postType     Post type name
     * @param {string} slug         Post slug
     * @param {object} params       Other request params
     * @returns object
     */
    getBySlug(postType, slug, params = {}) {
        params.slug = slug;
        const urlParams = this.obj2url(params);
        this.embed = false;
        return this._get(`/${this.postSlug(postType)}${urlParams}`);
    }

    /**
     * If val is an array, return a string joined by ','
     * @param {mixed}  val          Sent value
     * @param {string} separator    String separator
     * @returns string
     */
    arr2str(val, separator = ',') {
        if (val instanceof Array) {
            return val.join(separator);
        }
        return val;
    }

    /**
     * Object to query string
     * @param {object} obj Sent object
     * @returns String (query string)
     */
    obj2url(obj) {
        obj._fields = obj._fields ?? this.fields;
        if (this.embed) {
            obj._embed = 1;
            obj._fields.push('_links.wp:featuredmedia', '_embedded');
        }
        if (this.formats) {
            obj._fields.push('format');
        }
        let str = [];
        for (const key in obj) {
            const val = this.arr2str(obj[key]);
            str.push(`${key}=${encodeURIComponent(val)}`);
        }
        return str.length ? '?' + str.join('&') : '';
    }

    postSlug(slug) {
        return /^(post|page)$/.test(slug) ? `${slug}s` : slug;
    }

    taxSlug(slug) {
        if ('category' == slug) {
            return 'categories';
        }
        if ('tag' == slug || 'post_tag' == slug) {
            return 'tags';
        }
        return slug;
    }
}

export default WpApi;