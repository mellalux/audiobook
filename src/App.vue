<template>

    <nav v-if="start" class="navbar navbar-expand-md navbar-light sticky-top navstyle">
        <div class="container-fluid">
            <a :href="siteUrl" class="navbar-brand">
                <img src="@/assets/audiobookicon.png" role="button" :alt="t.AppName" width="50" height="50" class="d-inline-block">
            </a>
            <span class="navbar-text fw-bold">
                <h2 class="navheading">{{ t.AppName }}</h2>
            </span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#audionav"
                aria-controls="audionav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="audionav">
                
                <div v-if="langs" class="text-center"  :aria-label="t.Language" aria-labelledby="langs">
                    <span class="langlabel">{{t.Language}}:  </span>

                    <span id="langs" v-for="(lng, index) in langs" :key="index">
                        <a href="#" role="button" class="langlink" v-if="lng !== curLang" @click.prevent="curLang = lng;"
                            :title="langName[lng]" :aria-label="langName[lng]">
                            {{lng.toUpperCase()}}
                        </a>
                    </span>
                </div>

                <div class="author text-center" :aria-label="t.Author">
                    <a class="navbar-brand" href="https://www.mella.ee" target="_blank">
                        <img src="@/assets/vektor_mella.png" :alt="t.Author" width="50" height="50" class="d-inline-block">
                    </a>
                </div>

            </div>
        </div>
    </nav>    

    <AudiobookPlayer v-if="start" :t="t" :conf="conf" />

    <footer class="fixed-bottom sticky-bottom footerstyle">
        <p class="text-center pt-2 lh-1">Copyright (C) Meelis Luks 2022</p>
    </footer>

</template>

<script>
import axios from "axios";
import ini from "ini";
import language from './locales/langs.json';
import AudiobookPlayer from './components/AudiobookPlayer.vue';
import 'regenerator-runtime/runtime';
import { Accessibility } from 'accessibility';

export default {
    name: 'App',

    data() {
        return {
            t: [],
            langs: Object.keys(language),
            curLang: Object.keys(language)[0],
            langName: [],
            conf: null,
            start: false,
            siteUrl: null
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
        window.addEventListener('load', function () {
            new Accessibility();
        }, false);

        this.langs.forEach((e) => {
            this.langName[e] = language[e].langName;
        })

    },

    methods: {

        async loadIni() {
            let res = await axios.get(process.env.BASE_URL + "config.ini");
            this.conf = ini.parse(res.data);
            if (this.conf) {
                this.siteUrl = this.conf.siteUrl;
                this.curLang = this.conf.curLang;
                this.start = true;
            }
        },

    }
}
</script>

<style lang="scss">
    
    body {
        min-height: 100vh;
        margin: 0 0;
        background: repeating-linear-gradient(-60deg, #888 0 20px, #999 0 40px);
    }

    #app {
        font-family: Avenir, Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        color: #2c3e50;
        min-height: 80vh;
        height: auto !important;
        height: 100%;
        margin: 0 auto 50px;
    }
    .navstyle {
        margin: 0;
        border-radius: 0px;
        background-color: blanchedalmond;
        box-shadow: rgba(0, 0, 0, 0.4) 0px 2px 4px,
        rgba(0, 0, 0, 0.3) 0px 7px 13px -3px,
        rgba(0, 0, 0, 0.2) 0px -3px 0px inset;
    }
    .footerstyle {
        margin: 0;
        border-radius: 0px;
        background-color: blanchedalmond;
        box-shadow: //rgba(0, 0, 0, 0.4) 0px 2px 4px,
                rgba(0, 0, 0, 0.3) 0px -7px 13px -3px,
                rgba(0, 0, 0, 0.2) 0px 3px 0px inset;
        position: fixed;
        bottom: 0;
    }
    a.langlink {
        padding: 8px;
        margin: 0 5px;
        border-radius: 8px;
        background-color: rgb(252, 252, 252);
        text-decoration: none !important;
        color: #2c3e50;
    }

    @media (min-width: 768px) {
        .navstyle {
            margin: 0 25px;
            border-bottom-right-radius: 15px;
            border-bottom-left-radius: 15px;
        } 
        .footerstyle {
            margin: 0 25px;
            border-top-right-radius: 15px;
            border-top-left-radius: 15px;
                                }
        .langlabel {
            visibility: hidden;
        }
        .author {
            margin-left: 25px;
        }
    }
    @media (max-width: 768px) {
        .author {
            margin-top: 25px;
        }
    }

</style>
