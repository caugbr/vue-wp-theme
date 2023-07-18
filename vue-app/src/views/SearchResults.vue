<template>
    <div class="search-results">
        <h1>SearchResults</h1>
        <list v-if="posts.length" :postList="posts" />
    </div>
</template>

<script>
import List from '../components/List.vue';

export default {
    name: 'SearchResults',
    components: { List },
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
        }
    },
    beforeMount() {
        this.search();
    }
}
</script>

<style lang="scss">
.search-results {
    
}
</style>