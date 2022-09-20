<template>
    <nav v-if="start" class="navbar navbar-expand-lg navbar-dark sticky-top bg-primary">
        <div class="container-fluid">
            <div class="navbar-brand">
                <img src="@/assets/audiobookicon.png" role="button" :alt="t.AppName" width="50" height="50"
                    class="d-inline-block align-text-top">
            </div>
            <span class="navbar-text text-white fw-bold">
                <h2 class="navheading">{{ t.AppName }}</h2>
            </span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#audionav"
                aria-controls="audionav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="audionav">
                <ul v-if="langs" class="navbar-nav mb-2 mb-lg-0">
                    <li v-for="(lng, index) in langs" :key="index">
                        <span role="button" class="nav-link" v-if="lng !== curLang" @click="changeLang(lng)"
                            :title="t.Language+': '+lng.toUpperCase()" :aria-label="t.Language+': '+lng.toUpperCase()">
                            {{lng.toUpperCase()}}
                        </span>
                    </li>
                </ul>
                <a class="navbar-brand ms-5" href="https://www.mella.ee" target="_blank">
                    <img src="@/assets/vektor_mella.png" :alt="t.Author" width="50" height="50" class="d-inline-block">
                </a>
            </div>
        </div>
    </nav>    

    <AudiobookPlayer v-if="start" :t="t" :conf="conf" />
</template>

<script>
import axios from "axios";
import ini from "ini";
import language from './locales/langs.json';
import AudiobookPlayer from './components/AudiobookPlayer.vue';
import { Accessibility } from 'accessibility/dist/main';

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
        window.addEventListener('load', function () { new Accessibility(); }, false);
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
