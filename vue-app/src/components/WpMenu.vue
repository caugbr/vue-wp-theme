<template>
    <div :class="`wp-menu list ${inline ? 'inline-list' : ''}`">
        <ul>
            <li v-for="(itm, index) in items" :key="index">
                <router-link :to="itemUrl(itm)">
                    {{ itm.title }}
                </router-link>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: 'WpMenu',
    props: {
        menu: {
            type: String,
            required: true
        },
        inline: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            items: []
        };
    },
    methods: {
        itemUrl(itm) {
            let to;
            switch (itm.type) {
                case 'post_type':
                    to = `/${this.normalizeSlug(itm.object_type)}/${itm.slug}`;
                    break;

                case 'taxonomy':
                    to = `/${itm.slug.post_type}/${itm.slug.tax_name}/${itm.slug.term_id}`;
                    break;

                case 'post_type_archive':
                    to = `/${this.normalizeSlug(itm.slug)}`;
                    break;

                default:
                    to = itm.url ?? '';
            }
            to = to.replace(/^(https?:\/\/[^/]+)(.+)$/, '$2');
            if (!to.includes(this.info.basePath)) {
                to = this.info.basePath + to;
            }
            return to;
        },
        normalizeSlug(slug) {
            return /^(post|page|video)$/.test(slug) ? `${slug}s` : slug;
        }
    },
    beforeMount() {
        if (this.menu) {
            const api = this.getApi({ namespace: 'vuewp/v1' });
            api.getMenu(this.menu).then(menu => {
                this.items = menu.data;
            });
        }
    }
}
</script>