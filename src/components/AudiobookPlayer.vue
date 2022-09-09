<template>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="https://www.mella.ee" target="_blank">
                <img src="@/assets/vektor_mella.png" alt="" width="50" height="50"
                    class="d-inline-block align-text-top">
            </a>
            <span class="navbar-text text-white fw-bold">
                <h4 class="appname display-8">AudioBook Player</h4>
            </span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">

        <div class="row">
            <div v-if="dirs" class="col">
                <span class="fs-2 fw-bolder">Catalogs</span>
            </div>
            <div v-else-if="files" class="col">
                <span class="fs-2 fw-bolder">{{currentCatalog}}</span>
            </div>
            <div v-else class="col">
                <span class="fs-2 fw-bolder">Fatal Error!</span>
            </div>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a :href="siteUrl">Home</a></li>
                <li v-for="p in path" :key="p.id" class="breadcrumb-item active" aria-current="page">{{p}}</li>
            </ol>
        </nav>

        <div v-if="dirs" class="row">
            <div class="col">

                <div class="row" v-for="dir in dirs" :key="dir.id">
                    <div class="col">
                        <div class="shadow p-3 mb-1 bg-body rounded border bg-light catalogs" @click="loadDir(dir)">{{ dir }}</div>
                    </div>
                </div>

            </div>
        </div>

        <audio :src="src" type="audio/mpeg" hidden ref="audio" :id="playerid"></audio>

        <div v-if="files" class="shadow p-3 mb-5 bg-body rounded">
            <div class="col">

                <div class="row">
                    <div class="col-1 border border-primary rounded-2 p-1 mx-1 mb-2 button_box">
                        <svg version="1.1" @click="toggleAudio()" v-show="!isPlaying" baseProfile="tiny"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
                            y="0px" viewBox="0 0 100 100" overflow="visible" xml:space="preserve">
                            <g id="play">
                                <path fill="#0d6efd"
                                    d="M95,50c0,24.85-20.15,45-45,45S5,74.85,5,50S25.15,5,50,5S95,25.15,95,50z M80.77,47.83l-45.03-26
        		c-1.67-0.97-3.76,0.24-3.76,2.17l0,52c0,1.93,2.09,3.14,3.76,2.17l45.03-26C82.44,51.21,82.44,48.79,80.77,47.83z" />
                            </g>
                        </svg>
                        <svg version="1.1" @click="toggleAudio()" v-show="isPlaying" baseProfile="tiny"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
                            y="0px" viewBox="0 0 100 100" overflow="visible" xml:space="preserve">
                            <g id="pause">
                                <path fill="#0d6efd" d="M95,50c0,24.85-20.15,45-45,45S5,74.85,5,50S25.15,5,50,5S95,25.15,95,50z M45.78,72.47V27.53
        		c0-1.4-1.13-2.53-2.53-2.53H27.3c-1.4,0-2.53,1.13-2.53,2.53v44.94c0,1.4,1.13,2.53,2.53,2.53h15.95
        		C44.65,75,45.78,73.87,45.78,72.47z M74.78,72.47V27.53c0-1.4-1.13-2.53-2.53-2.53H56.29c-1.4,0-2.53,1.13-2.53,2.53v44.94
        		c0,1.4,1.13,2.53,2.53,2.53h15.95C73.64,75,74.78,73.87,74.78,72.47z" />
                            </g>
                        </svg>
                    </div>
                    <div class="col border border-primary rounded-2 p-1 mx-1 mb-2">
                        <div class="progress" ref="aubar" @click="seekTo" style="height: 30px;">
                            <div class="progress-bar progress-bar-striped" :class="{ 'progress-bar-animated': isPlaying }" role="progressbar" aria-label="Example with label"
                                aria-valuemin="0" :aria-valuenow="valuenow" :aria-valuemax="audioDuration"
                                :style="audioPos">
                                {{valuenow}}%
                            </div>
                        </div>
                    </div>
                    <!--             <div class="col-1 button_box">
                    
                    </div>
                    <div class="col-1 button_box">
                    
                    </div>
                    <div class="col-1 button_box">
                    
                    </div>
 -->
                </div>

                <div class="row">
                    <div class="col border border-primary rounded-2 mx-1">
                        <label for="speed" class="form-label">Speed: {{speedRate}}</label>
                        <input type="range" v-model="speedRate" class="form-range" min="0.25" max="2" step="0.25" id="speed">
                    </div>                    
                    <div class="col-2 border border-primary rounded-2 mx-1 d-flex align-items-center timeinfo">
            	        <span v-show="!audioLoaded" class="w-100 text-center fw-bold">Loading please wait...</span>                    
            	        <span v-show="audioLoaded" class="w-100 text-center fw-bold lh-1">{{elapsed}}<br />{{total}}</span>
                    </div>
                    <div class="col border border-primary rounded-2 mx-1">
                        <label for="volune" class="form-label">Volume: {{parseInt(Volume*100)}}</label>
                        <input type="range" v-model="Volume" class="form-range" min="0.0" max="1" step="0.01" id="volume">
                    </div>
               </div>

            </div>
        </div>

        <div v-if="files" class="row">
            <div class="col">
                <span class="fs-3 fw-bolder">Chapter(s):</span>
                <div ref="chapButtons" class="d-grid gap-2 d-md-block p-2 mx-auto">
                    <button v-for="(file, index) in afile" :key="file.id" :id="'cap'+index" :data-play-track="index"
                        class="btn btn-primary m-1 track" type="button" @click="loadMP3" :title="file.name">
                        {{file.nr}}
                    </button>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import { ref, onMounted } from "vue";
import axios from "axios";
import qs from "qs";

export default {
    name: 'AudiobookPlayer',

    setup() {
        const audio = ref(null);
        const aubar = ref(null);
        let chapButtons = ref(null);
        
        onMounted(() => { 
            // the DOM element will be assigned to the ref after initial render
            // console.log(root.value.children[0]) // this is your $el
            // let el = chapButtons.value.children[0].value;
            // console.log('1st el: '+el);
            //Sortable.create(el, {})
        })
        
        return {
            audio,
            aubar,
            chapButtons
        }
    },

    data() {
        return {
            debug: false,
            siteUrl: window.location.href,
            booksUrl: 'https://audiobooks.mella.ee/data/',
            path: [],
            directories: [],
            dirs: [],
            files: [],
            afile: [{
                nr: null,
                name: null,
                url: null
            }],
            folder: '',
            src: '',
            playbackTime: 0,
            audioDuration: 100,
            audioPos: 0,
            speedRate: 1,
            Volume: 1,
            valuenow: 0,
            audioLoaded: false,
            isPlaying: false,
            listenerActive: false,
            currentTrack: 0,
            currentCatalog: '',
            elapsed: '00:00',
            total: '00:00'
        }
    },

    created() {

    },

    mounted() {

        this.loadDir(this.folder);
        
        this.$nextTick(function () {

            //Wait for audio to load, then run initSlider() to get audio duration and set the max value of our slider 
            // "loademetadata" Event https://www.w3schools.com/tags/av_event_loadedmetadata.asp
            this.audio.addEventListener(
                "loadedmetadata",
                function () {
                    this.initSlider();
                }.bind(this)
            );

            // "canplay" HTML Event lets us know audio is ready for play https://www.w3schools.com/tags/av_event_canplay.asp
            this.audio.addEventListener(
                "canplay",
                function () {
                    this.audioLoaded = true;
                }.bind(this)
            );

            //Wait for audio to begin play, then start playback listener function
            this.$watch("isPlaying", function () {

                if (this.isPlaying) {
                    this.initSlider();

                    //prevent starting multiple listeners at the same time
                    if (!this.listenerActive) {
                        this.listenerActive = true;

                        //for a more consistent timeupdate, include freqtimeupdate.js and replace both instances of 'timeupdate' with 'freqtimeupdate'
                        this.audio.addEventListener("timeupdate", this.playbackListener);
                    }
                }

            });

            //Update current audio position when user drags progress slider
            this.$watch("playbackTime", function () {

                var diff = Math.abs(this.playbackTime - this.audio.currentTime);

                //Throttle synchronization to prevent infinite loop between playback listener and this watcher
                if (diff > 0.01) {
                    this.audio.currentTime = this.playbackTime;

                }
            });

        });
    },

    watch: {
        files: function () {
            this.afile.splice(0);
            if (this.files) {
                this.files.forEach((e,i) => {
                    this.afile.push({
                        nr: '#' + (i + 1),
                        name: e.split('.').slice(0, -1).join('.'),
                        url: this.booksUrl + this.folder + '/' + e
                    })
                });
                this.src = this.afile[this.currentTrack].url;
                this.isPlaying = false;
                this.listenerActive = false;
                this.audio.load();
                this.audioDuration = Math.round(this.audio.duration);

                const playPromise = this.audio.play();
                if (playPromise !== null) {
                    playPromise.catch(() => { 
                        this.audio.play();
                        this.isPlaying = true;
                    })
                }                
            }
        },

        isPlaying: function() {
            this.activeTrackButton();
        },

        speedRate: function() {
            this.audio.playbackRate = this.speedRate;
        },
        
        Volume: function () {
            this.audio.volume = this.Volume;
        }

    },

    methods: {
        //Set the range slider max value equal to audio duration
        initSlider() {
            if (this.audio) {
                this.audioDuration = Math.round(this.audio.duration);
                this.audioPos = 'width: 0%;';
            }
        },

        loadDir(dir) {
            
            // if (this.debug) console.log('Href: ' + window.location.href);
            // if (this.debug) console.log('Pathname: ' + window.location.pathname + ', length: ' + window.location.pathname.length);

            // if (window.location.pathname.length > 1) {
            //     if (this.debug) console.log('window.location.pathname is more 1 character long.');
            
            //     this.folder = window.location.pathname.substring(1);
            // }

            this.folder = '';

            if (dir) {
                this.path.push(dir);
                if (this.debug) console.log(this.path);
            }

            this.currentCatalog = this.path[this.path.length - 1];

            this.path.forEach((e) => {
                this.folder = this.folder + e + '/';   
                if (this.debug) console.log('Current folder: ' + this.folder);
            });
                
            var url = './php/getFiles.php';
            const params = {
                folder: this.folder
            };
            axios
                .post(url, qs.stringify(params))
                .then((response) => {
                    this.dirs = response.data.dir;
                    this.files = response.data.file;          
                });

            // if (this.folder.length > 1) {
            //     const curFolder = new URL(window.location.href + this.folder); 
            //     window.history.pushState("state", "title", curFolder.href);
            //     if (this.debug) console.log('Url changed!');
            // }        
        },

        loadMP3(e) {
            e.preventDefault();
    
            this.currentTrack = e.target.getAttribute('data-play-track');
            this.isPlaying = false;
            this.listenerActive = false;
            this.audio.pause();
            
            if (this.debug) console.log('Track nr: '+ this.currentTrack);

            this.src = this.afile[this.currentTrack].url;
            this.audio.load();
            this.audioDuration = Math.round(this.audio.duration);

            const playPromise = this.audio.play();
            if (playPromise !== null) {
                playPromise.catch(() => {
                    this.audio.play();
                    this.isPlaying = true;
                })
            }   

            this.activeTrackButton();

        },

        seekTo(e) {
            var percentage = Math.floor((e.offsetX / this.aubar.clientWidth) * 100);
            this.audio.currentTime = this.audio.duration * (percentage / 100);
            this.valuenow = percentage;
            this.audioPos = 'width: ' + percentage + '%;';
            if (this.debug) console.log("Check Things: percentage=" + percentage + ', currentTime=' + this.audio.currentTime);
        },

        activeTrackButton() {

            for (let i = 0; i < this.chapButtons.childElementCount; i++) {
                const button = this.chapButtons.children[i];
                if (button.classList.contains('btn-danger')) {
                    button.classList.remove('btn-danger');
                    button.classList.add('btn-primary');
                } 
                if (i == this.currentTrack) {
                    button.classList.remove('btn-primary');
                    button.classList.add('btn-danger');
                }
            }

        },

        toggleAudio() {
            if (this.audio.paused) {
                this.audio.play();
                this.isPlaying = true;
            } else {
                this.audio.pause();
                this.isPlaying = false;
            }
            if (this.debug) console.log('isPlaying: ' + this.isPlaying);
        },

        //Convert audio current time from seconds to min:sec display
        convertTime(seconds) {
            const format = val => `0${Math.floor(val)}`.slice(-2);
        //    var hours = seconds / 3600;
            var minutes = (seconds % 3600) / 60;

            return [minutes, seconds % 60].map(format).join(":");
        },

        //Show the total duration of audio file
        totalTime() {
            if (this.audio) {
                var seconds = this.audio.duration;
                return this.convertTime(seconds);

            } else {
                return '00:00';
            }
        },

        //Display the audio time elapsed so far
        elapsedTime() {
            if (this.audio) {
                var seconds = this.audio.currentTime;
                return this.convertTime(seconds);

            } else {
                return '00:00';
            }
        },

        //Playback listener function runs every 100ms while audio is playing
        playbackListener() {
            //Sync local 'playbackTime' var to audio.currentTime and update global state
            this.playbackTime = this.audio.currentTime;

            let percentage = parseInt((this.audio.currentTime / this.audioDuration) * 100);
            
            if (percentage) {
                this.audioPos = 'width: ' + percentage + '%;';
                this.valuenow = percentage;
            
                if (this.debug) console.log('Percentage - '+ percentage);
            }

            this.elapsed = this.elapsedTime();
            this.total = this.totalTime();
            //Add listeners for audio pause and audio end events
            this.audio.addEventListener("ended", this.endListener);
        //    this.audio.addEventListener("pause", this.pauseListener);
        },
        
        //Function to run when audio is paused by user
        pauseListener() {
            this.isPlaying = false;
            this.listenerActive = false;
            if (this.debug) console.log('File paused.');
            this.cleanupListeners();
        },

        //Function to run when audio play reaches the end of file
        endListener() {
            this.isPlaying = false;
            this.listenerActive = false;
            if (this.debug) console.log('File ended.');
            this.cleanupListeners();

            this.currentTrack++;
            if (this.afile[this.currentTrack]) {
                this.src = this.afile[this.currentTrack].url;
                this.audio.load();
                this.audioDuration = Math.round(this.audio.duration);

                const playPromise = this.audio.play();
                if (playPromise !== null) {
                    playPromise.catch(() => {
                        this.audio.play();
                        this.isPlaying = true;
                    })
                }

                this.activeTrackButton();

                if (this.debug) console.log('Next part exist');
            } else {
                if (this.debug) console.log('Next part not exist');
            }
        },
        
        //Remove listeners after audio play stops
        cleanupListeners() {
            this.audio.removeEventListener("timeupdate", this.playbackListener);
            this.audio.removeEventListener("ended", this.endListener);
            this.audio.removeEventListener("pause", this.pauseListener);

            if (this.debug) console.log("All cleaned up!");
        },
    
    }
}
</script>

<style lang="scss">
.container {
        padding: 35px;
        box-shadow: rgba(0, 0, 0, 0.25) 0px 54px 55px, rgba(0, 0, 0, 0.12) 0px -12px 30px, rgba(0, 0, 0, 0.12) 0px 4px 6px, rgba(0, 0, 0, 0.17) 0px 12px 13px, rgba(0, 0, 0, 0.09) 0px -3px 5px;
    }
    .catalogs {
        cursor: pointer;
    }
    .progress {
        margin: 10px;
    }
    .form-label {
        margin-bottom: 0px;
    }
    .button_box {
        width: 60px;
        height: 60px;
        padding-left: 0px;
        padding-right: 0px;
    /*    border: 1px solid black;
         background-color: coral;*/
    }

    .timeinfo {
        background-color:adjust-hue($color: #002503, $degrees: 0);
        color: #43cc4e;
        font-size: rfs-fluid-value(1.5rem);
    }

/*
    @media (min-width: 576px) {
        .track {
            width: 100%;
        }
    }
*/    
    @media (min-width: 768px) {
        .track {
            width: 203px;
        }
    }
    
    @media (min-width: 992px) {
        .track {
            width: 210px;
        }
    }
    
    @media (min-width: 1200px) {
        .track {
            width: 202px;
        }
    }
    
    @media (min-width: 1400px) {
        .track {
            width: 197px;
        }
    }
</style>