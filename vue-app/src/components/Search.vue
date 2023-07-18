<template>
    <div :class="`search ${layout}`">
        <form :action="`${info.basePath}/search`" @submit.prevent="search">
            <div class="search-wrapper">
                <label v-if="label" for="search">{{ label }}</label>
                <div class="input-wrapper">
                    <input 
                        type="text" 
                        name="search" 
                        id="search" 
                        :placeholder="placeholder"
                        @focus="$event.target.select()"
                        v-model="searchTerm" 
                    >
                </div>
                <div class="button-wrapper">
                    <button type="submit">{{ buttonLabel }}</button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    name: 'Search',
    props: {
        layout: {
            type: String,
            validator: (val) => {
                return ['normal', 'horizontal', 'vertical'].includes(val);
            },
            default: 'normal'
        },
        label: {
            type: String,
            default: 'Search'
        },
        buttonLabel: {
            type: String,
            default: 'Go'
        },
        placeholder: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            searchTerm: ''
        };
    },
    computed: {
        lastSearch() {
            return this.$store.state.lastSearch;
        }
    },
    methods: {
        search() {
            this.$store.dispatch('setLastSearch', this.searchTerm);
            const s = encodeURIComponent(this.searchTerm);
            this.$router.push(`${this.info.basePath}/search/${s}`);
        }
    },
    mounted() {
        if (this.lastSearch && !this.searchTerm) {
            this.searchTerm = this.lastSearch;
        }
    }
}
</script>

<style lang="scss">
.search {
    .search-wrapper {
        width: 98%;
        max-width: 300px;
        margin-left: auto;
        margin-right: auto;
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;

        label {
            display: block;
            width: 100%;
            margin-bottom: 0.25rem;
            padding-left: 4px;
            padding-right: 4px;
        }
        .input-wrapper {
            width: 80%;
            flex-grow: 4;
            flex-shrink: 4;
            padding-left: 4px;
            padding-right: 4px;
            
            input {
                width: 100%;
            }
        }
        .button-wrapper {
            width: 20%;
            flex-grow: 0;
            flex-shrink: 0;
            padding-right: 4px;
            button {
                width: 100%;
            }
        }
    }

    &.horizontal .search-wrapper {
        flex-wrap: nowrap;
        label {
            width: auto;
            flex-grow: 0;
            flex-shrink: 0;
        }
        .input-wrapper {
            width: auto;
        }
        .button-wrapper {
            width: auto;
            flex-grow: 0;
            flex-shrink: 0;
        }
    }

    &.vertical .search-wrapper {
        label,
        .input-wrapper {
            width: 100%;
        }
        .button-wrapper {
            width: 100%;
            button {
                width: auto;
            }
        }
    }
}
</style>