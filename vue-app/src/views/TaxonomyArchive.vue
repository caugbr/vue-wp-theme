<template>
    <div class="taxonomy-archive">
        <h1>{{ t('Posts tax') }}</h1>
        <list :postType="postType" :taxonomy="taxonomy" :term="term" />
    </div>
</template>

<script>
import List from '../components/List.vue';

export default {
    name: 'TaxonomyArchive',
    components: { List },
    route_params: 'postType,taxonomy,term',
    data() {
        return {
            postType: '',
            taxonomy: '',
            term: ''
        };
    },
    beforeMount() {
        this.postType = this.$route.params.postType;
        this.taxonomy = this.$route.params.taxonomy;
        this.term = this.$route.params.term;
    }
}
</script>

<style lang="scss">
.taxonomy-archive {
    
}
</style>