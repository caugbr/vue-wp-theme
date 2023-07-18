<template>
    <div class="search">
        <form :action="`${info.basePath}/search`" @submit.prevent="search">
            <div class="formline">
                <label for="search">{{ t('Search') }}</label>
                <input type="text" name="search" id="search" v-model="searchTerm">
                <button type="submit">{{ t('Go') }}</button>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    name: 'Search',
    data() {
        return {
            searchTerm: ''
        };
    },
    methods: {
        search() {
            const s = encodeURIComponent(this.searchTerm);
            this.$router.push(`${this.info.basePath}/search/${s}`);
            this.toggleMenu();
        }
    }
}
</script>

<style lang="scss">
.search {
    
}
</style>