<template>
    <div class="langs-menu">
        <form action="#" @submit.prevent>
            <label v-if="label" for="chose-lang">
                {{ label }}
            </label>
            <label v-else for="chose-lang">
                {{ defaultLabel }}
            </label>
            <select id="chose-lang" @change="select">
                <option 
                    v-for="name, code in langs" 
                    :value="code" 
                    :key="code" 
                    :selected="language === code"
                >
                    {{ name }}
                </option>
            </select>
        </form>
    </div>
</template>

<script>
export default {
    name: 'LangsMenu',
    props: {
        label: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            langs: this.getLangs(),
            // defaultLabel: 'Choose your language'
        };
    },
    computed: {
        defaultLabel() {
            return this.t('Choose your language');
        }
    },
    methods: {
        select(val) {
            this.$store.dispatch('setLanguage', val.target.value)
        }
    }
}
</script>

<style>
.langs-menu {
    text-align: center;
    /* position: absolute;
    right: 20px;
    top: 20px; */
}
.langs-menu label {
    display: block;
    margin-bottom: 0.25rem;
}
</style>