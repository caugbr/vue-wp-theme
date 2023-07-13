<template>
    <div class="not-found">
        <h1>Not found</h1>
        <p>
            The content '{{ searched }}' was not found.
        </p>
    </div>
</template>

<script>
export default {
    name: 'NotFound',
    props: {
        path: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            urlPath: ''
        }
    },
    computed: {
        searched() {
            return this.path || this.urlPath;
        }
    },
    beforeMount() {
        this.urlPath = this.$route.params.pathMatch;
    }
}
</script>

<style lang="scss">
.not-found {
    
}
</style>