import Vue from 'vue';
import VueRouter from 'vue-router';
import Home from '../views/Home.vue';
import Post from '../views/Post.vue';
import Page from '../views/Page.vue';
import NotFound from '../views/NotFound.vue';

Vue.use(VueRouter);

const basePath = window.vueWpThemeInfo.basePath;
const routes = [
    {
        path: basePath ? basePath : '/',
        name: 'Home',
        component: Home
    },
    {
        path: basePath + '/posts/:slug',
        name: 'Post',
        component: Post
    },
    {
        path: basePath + '/pages/:slug',
        name: 'Page',
        component: Page
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
