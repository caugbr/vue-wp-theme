<template>
  <div class="home">
    <h1>Home</h1>

    <h3>Pages</h3>
    <list postType="page" :thumbnails="false"></list>

    <h3>Posts</h3>
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
