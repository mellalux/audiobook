<template>
  <AudiobookPlayer></AudiobookPlayer>
</template>

<script>

import AudiobookPlayer from './components/AudiobookPlayer.vue'

export default {
  name: 'App',
  components: {
    AudiobookPlayer
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
