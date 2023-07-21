<template>
    <div class="tax-links">
        <div 
            v-for="tax, index in taxIds" 
            :class="`taxonomy-links taxonomy-${tax}`" 
            :key="index"
        >
            <div v-if="thereAreLinks(tax)" :class="tax">
                <div class="tax-name">{{ taxNames[index] }}</div>
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
            type: Object,
            default() {
                return { categories: 'Categories', tags: 'Tags' };
            }
        },
        taxonomyLinks: {
            type: Object,
            default() {
                return { categories: [], tags: [] };
            }
        }
    },
    computed: {
        taxNames() {
            return Object.values(this.taxonomies);
        },
        taxIds() {
            return Object.keys(this.taxonomies);
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