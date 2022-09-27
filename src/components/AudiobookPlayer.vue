<template>
     
    <main class="container shadow p-3 mb-5 bg-body rounded">

        <div class="row">
            <header v-if="headingText" class="col">
                <h1 class="fs-2 fw-bolder">{{ headingText }}</h1>
            </header>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item" :class="(path.length === 0) ? 'active' : ''">
                    <a v-if="path.length !== 0" href="#" @click="breadcrumb" :data-brdb-val="null" >{{ t.Home }}</a>
                    <span v-else>{{ t.Home }}</span>
                </li>
                <li v-for="(p, index) in path" 
                    :key="index" 
                    :class="(index === path.length-1) ? 'active' : ''"
                    class="breadcrumb-item">
                    <a v-if="index !== path.length-1" href="#" @click="breadcrumb" :data-brdb-val="(index+1)">{{p}}</a>
                    <span v-else>{{p}}</span>
                </li>
            </ol>
        </nav>

        <div v-if="dirs" class="row">
            <div class="col">

                <div class="row" v-for="dir in dirs" :key="dir.id">
                    <div class="col d-grid gap-2">
                        <button type="button"
                                role="button" 
                                :title="dir" 
                                class="btn shadow p-3 mb-1 bg-body rounded border bg-light"
                                @click="loadDir(dir)">
                            {{ dir }}
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <div v-if="files.length > 0" class="shadow p-3 mb-5 bg-body rounded">
            <div class="col">

                <div class="row">
                    <div class="col-1 border border-primary rounded-2 p-1 mx-1 mb-2 button_box">
                        <div v-if="!isPlaying" role="button" @click="toggleAudio()" :aria-label="t.Play">
                            <svg version="1.1" baseProfile="tiny" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" overflow="visible" xml:space="preserve">
                                <g id="play">
                                    <path fill="#0d6efd" d="M95,50c0,24.85-20.15,45-45,45S5,74.85,5,50S25.15,5,50,5S95,25.15,95,50z M80.77,47.83l-45.03-26 c-1.67-0.97-3.76,0.24-3.76,2.17l0,52c0,1.93,2.09,3.14,3.76,2.17l45.03-26C82.44,51.21,82.44,48.79,80.77,47.83z" />
                                </g>
                            </svg>
                        </div>
                        <div v-if="isPlaying" role="button" @click="toggleAudio()" :aria-label="t.Pause">
                            <svg version="1.1" baseProfile="tiny" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" overflow="visible" xml:space="preserve">
                                <g id="pause"> 
                                    <path fill="#0d6efd" d="M95,50c0,24.85-20.15,45-45,45S5,74.85,5,50S25.15,5,50,5S95,25.15,95,50z M45.78,72.47V27.53 c0-1.4-1.13-2.53-2.53-2.53H27.3c-1.4,0-2.53,1.13-2.53,2.53v44.94c0,1.4,1.13,2.53,2.53,2.53h15.95 C44.65,75,45.78,73.87,45.78,72.47z M74.78,72.47V27.53c0-1.4-1.13-2.53-2.53-2.53H56.29c-1.4,0-2.53,1.13-2.53,2.53v44.94 c0,1.4,1.13,2.53,2.53,2.53h15.95C73.64,75,74.78,73.87,74.78,72.47z" />
                                </g>
                            </svg>
                        </div>
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
                    <!--             
                    <div class="col-1 button_box">       

                    </div>
                    -->
                </div>

                <div class="row">
                    <div class="col border border-primary rounded-2 mx-1">
                        <label for="speed" class="form-label">{{t.Speed + speedRate}}</label>
                        <input type="range" v-model="speedRate" class="form-range" min="0.25" max="2" step="0.05" id="speed">
                    </div>                    
                    <div class="col-2 border border-primary rounded-2 mx-1 d-flex align-items-center timeinfo">
            	        <span v-show="!audioLoaded" class="w-100 text-center fw-bold">Loading please wait...</span>                    
            	        <span v-show="audioLoaded" class="w-100 text-center fw-bold lh-1">{{elapsed}}<br />{{total}}</span>
                    </div>
                    <div class="col border border-primary rounded-2 mx-1">
                        <label for="volune" class="form-label">{{t.Volume + parseInt(Volume*100)}}</label>
                        <input type="range" v-model="Volume" class="form-range" min="0.0" max="1" step="0.01" id="volume">
                    </div>
               </div>

            </div>
        </div>

        <div v-if="files.length > 0" class="row">
            <div class="col">
                <span class="fs-3 fw-bolder">{{t.Chapters}}</span>
                <div ref="chapButtons" class="d-grid gap-2 d-md-block p-2 mx-auto">
                    <button v-for="(file, index) in afile" :key="file.id" :id="'cap'+index" :data-play-track="index"
                        class="btn btn-primary m-1 track" type="button" @click="loadTrack" :title="file.name">
                        {{file.nr}}
                    </button>
                </div>
            </div>
        </div>
        
    </main>

    <audio :src="src" :type="type" hidden ref="audio" :id="playerid"></audio>

</template>

<script>
import { ref, onMounted } from "vue";
import axios from "axios";
import qs from "qs";
// import { Tooltip } from "bootstrap";

// new Tooltip(document.body, {
//     selector: "[data-bs-toggle='tooltip']",
// });

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

    props: ["t", "conf"],
    
    data() {
        return {
            // App stuff
            debug: false,
            siteUrl: window.location.href,
            booksUrl: null,
            root: './data/',
            path: [],
            dirs: [],
            files: [],
            afile: [{
                nr: null,
                name: null,
                type: null,
                url: null
            }],
            folder: '',
            currentTrack: 0,
            currentCatalog: '',
            headingText: '',
            result: null,

            // Audio stuff
            src: null,
            type: null,
            playbackTime: 0,
            audioDuration: 100,
            audioPos: 0,
            speedRate: 1,
            Volume: 1,
            valuenow: 0,
            audioLoaded: false,
            isPlaying: false,
            listenerActive: false,
            elapsed: '00:00',
            total: '00:00'
        }
    },

    created() {

    },

    mounted() {

        document.title = this.conf.AppName;
        this.debug = this.conf.debug;
        this.booksUrl = this.conf.booksUrl;
        
        this.loadDir(this.folder);
        
        this.$nextTick(function () {

            this.$watch('t', function() {
                this.changeHeading();
            });

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
            let ptypes = {'mp3': 'audio/mpeg', 'wav': 'audio/wav', 'm4a': 'audio/m4a', 'ogg': 'audio/ogg', 'flac': 'audio/flac', 'webm':'audio/webm'};

            this.afile.splice(0);
            if (this.files.length > 0) {
                this.files.forEach((e,i) => {
                    this.afile.push({
                        nr: '#' + (i + 1),
                        name: e.split('.').slice(0, -1).join('.'),
                        type: ptypes[e.split('.').pop().toLowerCase()],
                        url: this.booksUrl + this.folder + '/' + e
                    });
                });
                this.src = this.afile[this.currentTrack].url;
                this.type = this.afile[this.currentTrack].type;

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
        
        result: function() {
            this.changeHeading();
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

        // Set the range slider max value equal to audio duration
        initSlider() {
            if (this.audio) {
                this.audioDuration = Math.round(this.audio.duration);
                this.audioPos = 'width: 0%;';
            }
        },

        changeHeading() {
            switch (this.result) {
                case -1:
                    this.headingText = 'ERROR';
                    break;
                case 0:
                    this.headingText = this.t.NoFiles;
                    break;
                case 1:
                    this.headingText = this.t.Catalogs;
                    break;
                case 2:
                    this.headingText = this.t.Catalogs;
                    break;
                case 3:
                    this.headingText = this.currentCatalog;
                    break;

            }
        },

        loadDir(dir) {

            if (this.isPlaying) {
                this.isPlaying = false;
                this.listenerActive = false;
                this.audio.pause();
            }

            this.dirs.splice(0);
            this.files.splice(0);
            this.folder = '';
            this.currentTrack = 0;

            if (dir === '') {
                this.path = [];
                //this.entrance_inputs.splice(0, this.entrance_inputs.length);
            } else {
                this.path.push(dir);
                if (this.debug) console.log(this.path);
            }

            this.currentCatalog = this.path[this.path.length - 1];

            this.path.forEach((e) => {
                this.folder = this.folder + e + '/';   
                if (this.debug) console.log('Current folder: ' + this.folder);
            });
            
            this.getFiles();

        },

        getFiles() {
            let url = './php/getFiles.php';
            const params = {
                folder: this.folder,
                root: this.conf.root,
                types: this.conf.ftypes
            };
            axios
                .post(url, qs.stringify(params))
                .then((response) => {
                    if (response.data.dir !== undefined) { this.dirs = response.data.dir; }
                    if (response.data.file !== undefined) { this.files = response.data.file; }
                    this.result = response.data.result;
                    if (this.debug) console.log(response.data);
                });
        },

        breadcrumb(e) {
            e.preventDefault();

            let item = e.target.getAttribute('data-brdb-val');

            if (this.audio.duration > 0 && !this.audio.paused) {
                this.isPlaying = false;
                this.listenerActive = false;
                this.audio.pause();
            }

            this.dirs.splice(0);
            this.files.splice(0);
            this.folder = '';
            this.currentTrack = 0;
 
            this.path = this.path.slice(0, item);

            this.path.forEach((e) => {
                this.folder = this.folder + e + '/';
                if (this.debug) console.log('Current folder: ' + this.folder);
            });
            this.currentCatalog = this.path[this.path.length - 1];

            this.getFiles();

        },

        loadTrack(e) {
            e.preventDefault();
    
            this.currentTrack = e.target.getAttribute('data-play-track');
            this.isPlaying = false;
            this.listenerActive = false;
            this.audio.pause();
            
            if (this.debug) console.log('Track nr: '+ this.currentTrack);

            this.src = this.afile[this.currentTrack].url;
            this.type = this.afile[this.currentTrack].type;

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

            if (this.chapButtons.childElementCount > 0) {
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
                this.type = this.afile[this.currentTrack].type;

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

    body {
        min-height: 100vh;
        margin: 0 0;
        background: repeating-linear-gradient(-60deg, #888 0 20px, #999 0 40px);
    }

    .example {
        float: left;
        width: 300px;
        margin: 30px 10px;
        padding: 10px;
        border: 1px solid transparent;
        /* нужен для работы в Edge, ну и как фолбэк сгодится */
        border-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink'%3E%3Cdefs%3E%3Cfilter id='blurMe'%3E%3CfeGaussianBlur in='SourceGraphic' stdDeviation='3' /%3E%3C/filter%3E%3Csymbol id='card' viewBox='0 0 80 80' width='80' height='80'%3E%3Cpath d='M10,0h60c7,1+9,3+10,10v60h-10v10h-60a10,10+0,00-10-10v-60Z'/%3E%3C/symbol%3E%3C/defs%3E%3Cuse xlink:href='%23card' style='fill:%23000; opacity: 0.4; filter:url(%23blurMe);' x='10' y='15' width='80' height='80'/%3E%3Cuse xlink:href='%23card' style='fill:%23ffc' x='10' y='10' width='80' height='80'/%3E%3C/svg%3E%0A") 20 fill / 20px / 10px stretch;
    }
    
    .example:hover {
        border-image: none;
        background: #ffc;
        border-radius: 10px;
        box-shadow: 0 5px 5px rgba(0, 0, 0, 0.4);
    }
    
    p {
        clear: left;
    }
    
    :focus-visible {
        outline: none;
    }

    .navheading {
        margin-top: 0;
        margin-bottom: 0;
        font-weight: 500;
        line-height: 1;
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
            width: 216px;
        }
    }
    
    @media (min-width: 992px) {
        .track {
            width: 220px;
        }
    }
    
    @media (min-width: 1200px) {
        .track {
            width: 210px;
        }
    }
    
    @media (min-width: 1400px) {
        .track {
            width: 204px;
        }
    }
</style>