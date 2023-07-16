<template>
    <div v-if="position != 'none'" :class="`sidebar position-${position}`">
        <a class="toggle" href="#" @click.prevent="toggle">
            <span class="open">&plus;</span>
            <span class="close">&minus;</span>
        </a>
        <wp-menu 
            v-if="info.settings.sidebar_menu" 
            :menu="info.settings.sidebar_menu" 
            :inline="false"
        />
    </div>
</template>

<script>
import WpMenu from '../WpMenu.vue';

export default {
    name: 'Sidebar',
    components: {
        WpMenu
    },
    // data() {
    //     return {
    //         position: this.info.settings.sidebar_location
    //     }
    // },
    computed: {
        position() {
            return this.info.settings.sidebar_location;
        }
    }, 
    methods: {
        toggle() {
            document.body.classList.toggle('menu-open');
        }
    }
}
</script>

<style lang="scss">
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 200px;
    height: 100%;
    background-color: #fefefe;
    border-right: 1px solid #ccc;
    transform: translateX(-100%);
    transition: transform 200ms ease-in-out 0s;
    z-index: 999;
    
    .toggle {
        position: fixed;
        top: 0;
        right: 0;
        width: 30px;
        height: 30px;
        transform: translateX(calc(100% - 1px));
        background-color: #fefefe;
        border: 1px solid #ccc;
        border-width: 0 1px 1px 0;
        -webkit-border-bottom-right-radius: 6px;
        -moz-border-radius-bottomright: 6px;
        border-bottom-right-radius: 6px;

        color: #333;
        text-decoration: none;
        font-size: 20px;
        display: flex;
        justify-content: center;
        align-items: center;

        .close { display: none; }
    }

    &.position-right {
        left: auto;
        right: 0;
        transform: translateX(100%);
        border-right: 0;
        border-left: 1px solid #ccc;

        .toggle {
            right: auto;
            left: 0;
            -webkit-border-bottom-left-radius: 6px;
            -moz-border-radius-bottomleft: 6px;
            border-bottom-left-radius: 6px;
            -webkit-border-bottom-right-radius: 0;
            -moz-border-radius-bottomright: 0;
            border-bottom-right-radius: 0;
            transform: translateX(calc(-100% + 1px));
            border-width: 0 0 1px 1px;
        }
    }
}
.menu-open .sidebar {
    transform: translateX(0%);
    .toggle {
        .open { display: none; }
        .close { display: inline; }
    }
}
</style>