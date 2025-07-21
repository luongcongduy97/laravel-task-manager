<template>
  <nav class="sticky top-0 z-10 bg-white/70 backdrop-blur-lg shadow-lg px-4 md:px-6 py-3 flex items-center justify-between">
    <h1 class="text-xl sm:text-2xl font-semibold text-gray-800">Task Manager</h1>
    <div class="flex items-center space-x-4">
      <router-link
        v-if="showTeamLink"
        to="/teams"
        class="text-gray-700 hover:text-gray-900 text-sm sm:text-base"
      >Team</router-link>
      <button
        v-if="showLogout"
        @click="logout"
        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm sm:text-base transition-colors"
      >Logout</button>
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
