<template>
  <nav
    class="sticky top-0 z-20 bg-white/80 backdrop-blur-md shadow-md px-5 md:px-8 py-3 flex items-center justify-between border-b border-gray-200"
  >
    <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight bg-gradient-to-r from-purple-600 to-pink-500 text-transparent bg-clip-text">
      Task Manager
    </h1>

    <div class="flex items-center space-x-4">
      <router-link
        v-if="showTeamLink"
        to="/teams"
        class="text-gray-700 hover:text-purple-600 font-medium text-sm sm:text-base transition"
      >
        Teams
      </router-link>

      <button
        v-if="showLogout"
        @click="logout"
        class="bg-red-500 hover:bg-red-600 text-white px-4 py-1.5 rounded-md text-sm sm:text-base font-medium shadow-sm transition"
      >
        Logout
      </button>
    </div>
  </nav>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter, useRoute } from 'vue-router';
import { isAuthenticated } from '../auth.js';

const router = useRouter();
const route = useRoute();

const isAdmin = ref(false);

const showLogout = computed(() => {
  return isAuthenticated.value && route.path !== '/login' && route.path !== '/register';
});

const showTeamLink = computed(() => isAuthenticated.value && isAdmin.value);

onMounted(async () => {
  if (isAuthenticated.value) {
    const { data } = await axios.get('/api/user');
    isAdmin.value = data.role === 'Admin';
  }
});

async function logout() {
  await axios.post('/api/logout');
  localStorage.removeItem('token');
  delete axios.defaults.headers.common['Authorization'];
  isAuthenticated.value = false;
  isAdmin.value = false;
  router.push('/login');
}
</script>
