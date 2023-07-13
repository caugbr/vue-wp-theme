import axios from 'axios';

class WpAPI {
    apiUrl;
    nonce;
    namespace;
    embed;

    constructor(info) {
        this.apiUrl = info.root;
        this.nonce = info.nonce;
        this.namespace = info.namespace || 'wp/v2';
        this.embed = !!info.embed;
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
     * Returns a list of posts of type postType
     * @param {string} postType     Post type name
     * @param {object} params       Other request params
     * @returns array
     */
    getList(postType, params = {}) {
        const urlParams = this.obj2url(params);
        this.embed = false;
        return this._get(`/${postType}${urlParams}`);
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
        return this._get(`/${postType}${urlParams}`);
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
        return this._get(`/${postType}${urlParams}`);
    }

    /**
     * If val is an array, return a string joined by ','
     * @param {mixed} val Sent value
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
        if (this.embed) {
            obj._embed = 1;
            obj._fields.push('_links.wp:featuredmedia', '_embedded');
        }
        let str = [];
        for (const key in obj) {
            const val = this.arr2str(obj[key]);
            str.push(`${key}=${encodeURIComponent(val)}`)
        }
        return str.length ? '?' + str.join('&') : '';
    }
}

export default WpAPI;