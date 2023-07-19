<template>
    <div class="tax-links">
        <div 
            v-for="tax, index in taxonomies" 
            :class="`taxonomy-links taxonomy-${tax}`" 
            :key="index"
        >
            <div v-if="thereAreLinks(tax)" :class="tax">
                <span 
                    :class="`tax-link ${tax}`" 
                    v-for="lnk, ind in taxonomyLinks[tax]" 
                    :key="ind" 
                    v-html="lnk"
                ></span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'TaxonomyLinks',
    props: {
        taxonomies: {
            type: Array,
            default() {
                return ['categories', 'tags'];
            }
        },
        taxonomyLinks: {
            type: Object,
            default() {
                return { categories: [], tags: [] };
            }
        }
    },
    methods: {
        thereAreLinks(tax) {
            const tl = this.taxonomyLinks;
            return (tl[tax] instanceof Array && tl[tax].length);
        }
    }
}
</script>

<style lang="scss">
.taxonomy-links {
    
}
</style>