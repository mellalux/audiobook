<template>
    <AudiobookPlayer :t="t" :lang="lang" v-on:changeLang="clang()" />
</template>

<script>

import language from './locales/langs.json';
import AudiobookPlayer from './components/AudiobookPlayer.vue';

export default {
    name: 'App',
    data() {
        return {
            t: [],
            lang: 'et'
            //language: []
        }
    },

    components: {
        AudiobookPlayer
    },

    watch: {
        lang: function () {
            this.t = language[this.lang];
        }
    },

    mounted() {
        this.t = language[this.lang];
    },

    methods: {
        clang: function () {
            this.lang = (this.lang === 'et') ? 'en' : 'et';
        }
    }
}
</script>

<style>
    #app {
        font-family: Avenir, Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        color: #2c3e50;
    }
</style>
