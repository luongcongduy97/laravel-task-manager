<template>
  <div class="fade-in bg-white/80 backdrop-blur-md p-6 rounded-lg shadow-xl w-full max-w-xl space-y-4">
    <h2 class="text-2xl font-bold text-center text-gray-700">Teams</h2>

    <div v-if="isAdmin" class="flex space-x-2">
      <input v-model="newTeamName" placeholder="Team name" class="border px-2 py-1 rounded w-full">
      <button @click="addTeam" class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded">Add Team</button>
    </div>

    <ul class="space-y-4">
      <li v-for="team in teams" :key="team.id" class="border p-3 rounded">
        <h3 class="font-semibold">{{ team.name }}</h3>
        <div v-if="isAdmin" class="mt-2 space-y-2">
          <form @submit.prevent="invite(team.id)" class="space-y-2 flex flex-col sm:flex-row sm:space-x-2 sm:space-y-0">
            <input v-model="inviteData[team.id].name" type="text" placeholder="Name" class="border px-2 py-1 rounded" required>
            <input v-model="inviteData[team.id].email" type="email" placeholder="Email" class="border px-2 py-1 rounded" required>
            <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded">Invite</button>
          </form>
          <p v-if="inviteData[team.id].message" class="text-green-600">{{ inviteData[team.id].message }}</p>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup>
  import { ref, onMounted } from 'vue';
  import axios from 'axios';
  import { useRoute, useRouter } from 'vue-router';
  import { isAuthenticated } from '../auth.js';

  const route = useRoute();
  const router = useRouter();
  const name = ref('');
  const email = ref('');
  const message = ref('');

  onMounted(() => {
    const token = localStorage.getItem('token');
    if (!token) {
      router.push('/login');
    } else {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      isAuthenticated.value = true;
    }
  });

  async function invite() {
    const teamId = route.params.teamId || 1;
    const { data } = await axios.post(`/api/teams/${teamId}/invite`, { name: name.value, email: email.value });
    message.value = `Invited ${data.email}`;
    name.value = '';
    email.value = '';
  }
</script>