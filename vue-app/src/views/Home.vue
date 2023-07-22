<template>
    <div class="home">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>{{ t('Vue WP starter theme') }}</h1>
                </div>
                <div class="col-12">
                    <p v-html="t('vuewp_intro')"></p>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h3>{{ t('Pages') }}</h3>
                    <list postType="page" :thumbnails="false"></list>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h3>{{ t('Posts') }}</h3>
                    <list postType="post"></list>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import List from '../components/List.vue';

export default {
    name: 'Home',
    components: { List }
}
</script>
