<template>
    <AudiobookPlayer v-if="start" :t="t" :curlang="curLang" :langs="langs" :conf="conf" v-on:changeLang="changeLang" />
</template>

<script>

import axios from "axios";
import ini from "ini";
import language from './locales/langs.json';
import AudiobookPlayer from './components/AudiobookPlayer.vue';

export default {
    name: 'App',

    data() {
        return {
            t: [],
            langs: Object.keys(language),
            curLang: Object.keys(language)[0],
            conf: null,
            start: false
        }
    },
    
    created() {

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
        this.loadIni();
        this.t = language[this.curLang];
    },

    methods: {

        async loadIni() {
            let res = await axios.get(process.env.BASE_URL + "config.ini");
            this.conf = ini.parse(res.data);
            if (this.conf) {
                this.curLang = this.conf.curLang;
                this.start = true;
            }
        },

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
