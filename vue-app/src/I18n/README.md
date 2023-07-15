# I18n
This is a really simple script. It works with objects stored in JSON files, that can use the string itself as index, so you can preserve the legibility of your code using directly the default language instead of IDs to refer to your translatable strings. Well, you can use IDs too. Actualy you'll must use IDs to strings that uses variables or have a plural form. It's also recommended to use IDs for long strings.

## Install

I18n should be added at the moment you start Vue (commonly in 'main.js')
        
    import Vue from 'vue'
    import App from './App.vue'
    import store from './store'
    import I18n, { i18nMixin } from "./I18n"
    
    Vue.use(I18n);
    Vue.mixin(i18nMixin);
    
    Vue.config.productionTip = false

    new Vue({
        store,
        render: h => h(App)
    }).$mount('#app')

After that, all components will have the property `this.$i18n` that allows to access the object directly, but the mixin object adds everything you'll need to use I18n in your components.

Than you can save your JSON language files under `[...]/I18n/langs`, using the language codes as file names, with the extension `.json`. The system expects the codes to be lower case and use dashes to separate the parts, when it's necessary. Some examples: `en.json`, `es.json`, `pt-br.json`. These files should have the item "language_name" containing the legible name of the translation language.

### The translation files
Take a look at the translation files used in the example page. Note that, as english is the default language (language used in code), the english file only have the necessary items. The language name and the items that uses IDs ('intro1'), variables ('applesSample') or plural ('applesSample', 'projecstSample')

**en.json**

    {
        "language_name": "English",
        "intro1": "I18n is really simple. It works with objects stored in JSON files, that use the string itself as index, so you can preserve the legibility of your code.",
        "applesSample": "No apples | One apple | {apples} apples",
        "projectsSample": "You have a single project | You have multiple projects"
    }
**es.json**

    {
        "language_name": "Español",
        "intro1": "I18n es realmente simple. Funciona con objetos almacenados en archivos JSON, que usan la propia string como índice, para que puedas conservar tu código legible.",
        "Translate strings in a simple way": "Traducir cadenas de forma sencilla",
        "Choose your language": "Elige tu idioma",
        "Amount of apples": "Cantidad de manzanas",
        "Amount of projects": "Cantidad de proyectos",
        "applesSample": "Sin manzanas | Una manzana | {apples} manzanas",
        "projectsSample": "Tienes un solo proyecto | Tienes varios proyectoss",
        "Result": "Resultado"
    }
**pt-br.json**

    {
        "language_name": "Português (Brasil)",
        "intro1": "I18n é realmente simples. Funciona com objetos em arquivos JSON, que usam as próprias strings como índices, assim podemos preservar nosso código legível.",
        "Translate strings in a simple way": "Traduza sua interface de forma simples",
        "Choose your language": "Escolha sua linguagem",
        "Amount of apples": "Número de maçãs",
        "Amount of projects": "Número de projetos",
        "applesSample": "Nenhuma maçã | Uma maçã | {apples} maçãs",
        "projectsSample": "Você tem um único projeto | Você tem múltiplos projetos",
        "Result": "Resultado"
    }

### Using in a component
The functions added by `I18nMixin` will be present in all components, so you just need to use it. The code below uses two functions, `t('string')`, that translates a string and `tp('string', variables)`, if you want to use plural forms based on a sent variable (the first number present in variables object).

    <div class="formline">
        <label for="apples">{{ t('Amount of projects') }}</label>
        <select id="apples" v-model.number="projects">
            <option value="1">1</option>
            <option value="5">5</option>
            <option value="10">10</option>
        </select>
    </div>
    <div class="formline">
        <label>{{ t('Result') }}</label>
        <div class="result">{{ tp('projectsSample', { projects }) }}</div>
    </div>

### Changing the interface language
To translate the entire website, the best way is use Vuex to create a global variable to hold the current language.
Use the method `getLanguages()` to get a list of available languages you can build a language selector. The code below is used used in our sample page.

First set the store variables and functions.

**store/index.js**

    import Vue from 'vue'
    import Vuex from 'vuex'
      
    Vue.use(Vuex)
      
    export default new Vuex.Store({
        state: {
            language: 'en'
        },
        mutations: {
            SET_LANGUAGE(state, lng) {
                state.language = lng;
            }
        },
        actions: {
            setLanguage({ commit }, lng) {
                commit('SET_LANGUAGE', lng)
            }
        }
    })

All the interaction with Vuex will be done in App.vue. `changeLang()` sets the global language variable. Than we watch the changes in `$store.state.language` and call `setLang()` to define the local variable when it changes. Besides that, we check on beforeMount if there is a global language set and call `setLang()`. It will change the local variable `language`, translating the interface.

**App.vue**

    export default {
        name: 'App',
        components: {
            // this component receives the computed value 'languages' and
            // display a select that triggers the method changeLang() on change
            LangsMenu 
        },
        data() {
            return {
                apples: 0,
                projects: 1
            }
        },
        computed: {
            languages() {
                return this.getLangs();
            }
        },
        methods: {
            changeLang(lng) {
                this.$store.dispatch('setLanguage', lng)
            }
        },
        watch: {
            '$store.state.language' (lng) {
                this.setLang(lng);
            }
        },
        beforeMount() {
            if (this.$store.state.language) {
                this.setLang(this.$store.state.language);
            }
        }
    }
