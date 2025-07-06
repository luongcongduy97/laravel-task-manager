<template>
  <nav class="sticky top-0 z-10 bg-white/70 backdrop-blur-lg shadow-lg px-4 md:px-6 py-3 flex items-center justify-between">
    <h1 class="text-xl sm:text-2xl font-semibold text-gray-800">Task Manager</h1>
    <button
      v-if="showLogout"
      @click="logout"
      class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm sm:text-base transition-colors"
    >Logout</button>
  </nav>
</template>

<script setup>
import { computed } from 'vue';
import axios from 'axios';
import { useRouter, useRoute } from 'vue-router';
import { isAuthenticated } from '../auth.js';

const router = useRouter();
const route = useRoute();

const showLogout = computed(() => {
  return isAuthenticated.value && route.path !== '/login' && route.path !== '/register';
});

async function logout() {
  await axios.post('/api/logout');
  localStorage.removeItem('token');
  delete axios.defaults.headers.common['Authorization'];
  isAuthenticated.value = false;
  router.push('/login');
}
</script>
