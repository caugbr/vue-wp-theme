<template>
    <div class="site-footer">
        <div class="copyright">
            &copy;{{ year }} Cau Guanabara
        </div>
    </div>
</template>

<script>
export default {
    name: 'SiteFooter',
    data() {
        return {
            year: (new Date()).getFullYear()
        }
    }
}
</script>

<style lang="scss">
.site-footer {
    background-color: #efefef;
    border-top: 1px solid #ccc;
    padding: 0.6rem;
    width: 100%;
    height: 40px;
    position: absolute;
    bottom: 0;
    left: 0;
    box-sizing: border-box;
}
</style>