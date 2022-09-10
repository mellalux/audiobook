<template>
    <AudiobookPlayer :t="t" :curlang="curLang" :langs="langs" v-on:changeLang="changeLang" />
</template>

<script>

import language from './locales/langs.json';
import AudiobookPlayer from './components/AudiobookPlayer.vue';

export default {
    name: 'App',
    data() {
        return {
            t: [],
            curLang: Object.keys(language)[0],
            langs: Object.keys(language)
        }
    },

    components: {
        AudiobookPlayer
    },

    watch: {
        curLang: function () {
            this.t = language[this.curLang];
        }
    },

    mounted() {
        this.t = language[this.curLang];
    },

    methods: {
        changeLang: function (lng) {
            this.curLang = lng;
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
