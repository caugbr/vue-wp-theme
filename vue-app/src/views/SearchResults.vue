<template>
    <div class="search-results">
        <h1>{{ t('Search Results') }}</h1>
        <search layout="horizontal" />
        <div class="results" v-if="posts.length">
            {{ tp('One result for "{term}"|{count} results for "{term}".', { count, term }) }}
            <list :postList="posts" />
        </div>
        <p class="no-results" v-else>
            {{ t('No results for "{term}".', { term }) }}
        </p>
    </div>
</template>

<script>
import List from '../components/List.vue';
import Search from '../components/Search.vue';

export default {
    name: 'SearchResults',
    components: { List, Search },
    route_params: 'term',
    data() {
        return {
            posts: []
        }
    },
    methods: {
        async search() {
            const res = await this.apiCall('getSearchResults', this.term);
            this.posts = res.data;
        }
    },
    computed: {
        term() {
            return this.$route.params.term;
        },
        count() {
            return this.posts.length;
        }
    },
    beforeMount() {
        if (this.term) {
            this.$store.dispatch('setLastSearch', this.term);
        }
        this.search();
    }
}
</script>

<style lang="scss">
.search-results {
    
}
</style>