import Vue from 'vue';
import VueRouter from 'vue-router';
import Home from '../views/Home.vue';
import Post from '../views/Post.vue';
import PostArchive from '../views/PostArchive.vue';
import Page from '../views/Page.vue';
import PageArchive from '../views/PageArchive.vue';
import TaxonomyArchive from '../views/TaxonomyArchive.vue';
import SearchResults from '../views/SearchResults.vue';
import NotFound from '../views/NotFound.vue';
import Video from '../views/Video.vue';
import VideoArchive from '../views/VideoArchive.vue';

Vue.use(VueRouter);

const basePath = window.vueWpThemeInfo.basePath;
const routes = [
    {
        path: basePath ? basePath : '/',
        name: 'Home',
        component: Home
    },
    {
        path: basePath + '/posts',
        name: 'PostArchive',
        component: PostArchive
    },
    {
        path: basePath + '/posts/:slug',
        name: 'Post',
        component: Post
    },
    {
        path: basePath + '/pages',
        name: 'PageArchive',
        component: PageArchive
    },
    {
        path: basePath + '/pages/:slug',
        name: 'Page',
        component: Page
    },
    {
        path: basePath + '/videos',
        name: 'VideoArchive',
        component: VideoArchive
    },
    {
        path: basePath + '/video/:slug',
        name: 'Video',
        component: Video
    },
    {
        path: basePath + '/:postType/:taxonomy/:term',
        name: 'TaxonomyArchive',
        component: TaxonomyArchive
    },
    {
        path: basePath + '/search/:term',
        name: 'SearchResults',
        component: SearchResults
    },
    {
        path: basePath + '/:pathMatch(.*)*',
        name: 'NotFound',
        component: NotFound
    }
];

const router = new VueRouter({
    mode: 'history',
    routes
});

export default router;
