<template>
  <div class="home">
    <h1>{{ t('Vue WP starter theme') }}</h1>

    <p v-html="t('vuewp_intro')">
    </p>

    <h3>{{ t('Pages') }}</h3>
    <list postType="page" :thumbnails="false"></list>

    <h3>{{ t('Posts') }}</h3>
    <list postType="post" :thumbnails="false"></list>
  </div>
</template>

<script>
import List from '../components/List.vue';

export default {
  name: 'Home',
  components: { List }
}
</script>
