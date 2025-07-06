<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-br from-purple-300 via-pink-300 to-yellow-300">
    <NavBar />
    <div class="flex-grow flex items-center justify-center">
      <router-view />
    </div>
  </div>
</template>

<script setup>
  import NavBar from './components/NavBar.vue';
</script>