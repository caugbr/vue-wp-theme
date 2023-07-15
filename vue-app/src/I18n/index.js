/**
 * Internationalization for Vue apps
 * ---------------------------------
 * 
 * Depends on Vuex as a global property (this.$store). 
 * This script expects two things in your store:
 * - The state value 'language'
 * - The action 'setLanguage'
 * 
 * Like this:
    export default new Vuex.Store({
        state: { language: 'pt-br' },
        mutations: {
            SET_LANGUAGE(state, payload) {
                state.language = payload;
            }
        },
        actions: {
            setLanguage({ commit }, lang) {
                commit('SET_LANGUAGE', lang);
            }
        }
    });
 */

class Translate {

    constructor(lang, defaultLang = 'en') {
        lang = lang ? lang : process.env.LANGUAGE;
        this.navigatorLang = this.normalizeCode();
        this.defaultLang = defaultLang;
        this.fetch();
        this.setLang(lang);
    }

    normalizeCode(code = '') {
        if (!code) {
            code = (navigator.language || navigator.userLanguage);
        }
        return code.toLowerCase().replace('_', '-');
    }

    fetch() {
        this.translations = {};
        const context = require.context('./langs', false, /\.json$/);
        context.keys().forEach(key => {
            const fileName = key.replace('./', '');
            const resource = require(`./langs/${fileName}`);
            const lang = fileName.replace(/\.json$/, '');
            this.translations[lang] = JSON.parse(JSON.stringify(resource));
        });
    }

    getLangs = () => {
        return Object.keys(this.translations);
    }
    
    setLang = (lang = false) => {
        this.lang = lang ? lang : this.navigatorLang;
        this.strings = {};
        if (this.translations[this.lang]) {
            this.strings = this.translations[this.lang]
        }
        else if (this.translations[this.defaultLang]) {
            this.strings = this.translations[this.defaultLang]
        }
    };

    // Translate to the default language
    t = (txt, vars = null) => {
        let str = this.strings[txt] ? this.strings[txt] : txt;
        if (typeof vars === 'object') {
            for (const key in vars) {
                if (str.includes(`{${key}}`)) {
                    while (str.includes(`{${key}}`)) {
                        str = str.replace(`{${key}}`, vars[key]);
                    }
                }
            }
        }
        return str;
    };

    // Translate to any language
    tl = (txt, lang, vars = null) => {
        let str;
        try {
            str = this.translations[lang][txt] ? this.translations[lang][txt] : txt;
        } catch(e) {
            str = txt;
        }
        if (typeof vars === 'object') {
            for (const key in vars) {
                if (str.includes(`{${key}}`)) {
                    while (str.includes(`{${key}}`)) {
                        str = str.replace(`{${key}}`, vars[key]);
                    }
                }
            }
        }
        return str;
    };

    // Translate to plural form, based on the
    // first element with type 'number' in vars
    // The string should be:
    // 'if zero | if one | if more then 1'
    // 'if one | if zero or more then one'
    tp = (txt, vars = null, lang = '') => {
        const str = lang ? this.tl(txt, lang, vars) : this.t(txt, vars);
        if (!str.includes('|')) {
            return str;
        }
        let parts = str.split(/\s*\|\s*/), num = 2;
        if (typeof vars === 'object') {
            for (const key in vars) {
                if (typeof vars[key] === 'number') {
                    num = vars[key];
                    break;
                }
            }
        }
        if (parts.length === 2) {
            if (num === 1) {
                return parts[0];
            }
            return parts[1];
        }
        if (parts.length === 3) {
            if (num === 0) {
                return parts[0];
            }
            if (num === 1) {
                return parts[1];
            }
            return parts[2];
        }
        return this.t(parts.length > 2 ? parts[2] : parts[1], vars)
    };

    // Get language name
    langName = lang => {
        const ln = this.tl("language_name", lang);
        if ("language_name" === ln) {
            return lang;
        }
        return ln;
    }

}

// Vue plugin
const I18n = {
    install(Vue) {
        const lang = process.env.LANGUAGE ?? null;
        Vue.prototype.$i18n = new Translate(lang);
    }
};
export default I18n;

// Helper functions as a Vue mixin
export const i18nMixin = {
    computed: {
        language() {
            return this.$store.state.language;
        }
    },
    methods: {
        t(str, vars = null) {
            return this.$i18n.tl(str, this.language, vars);
        },
        tp(str, vars) {
            return this.$i18n.tp(str, vars, this.language);
        },
        getLangs() {
            let languages = {};
            const langs = this.$i18n.getLangs();
            langs.forEach(lng => {
                languages[lng] = this.$i18n.langName(lng);
            });
            return languages;
        },
        setLanguage(lng) {
            this.$i18n.setLang(lng);
        }
    }
};