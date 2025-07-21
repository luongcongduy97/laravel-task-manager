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
          <p v-if="inviteData[team.id].error" class="text-red-600">{{ inviteData[team.id].error }}</p>
          <button @click="removeTeam(team.id)" data-test="delete-team" class="text-red-600 hover:text-red-800">Delete Team</button>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup>
  import { ref, onMounted } from 'vue';
  import axios from 'axios';
  import { useRouter } from 'vue-router';
  import { isAuthenticated } from '../auth.js';

  const router = useRouter();
  const teams = ref([]);
  const isAdmin = ref(false);
  const inviteData = ref({});
  const newTeamName = ref('');

  onMounted(async () => {
    const token = localStorage.getItem('token');
    if (!token) {
      router.push('/login');
      return;
    }

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    isAuthenticated.value = true;

    const userRes = await axios.get('/api/user');
    isAdmin.value = userRes.data.role === 'Admin';

    const res = await axios.get('/api/teams');
    teams.value = res.data;
    teams.value.forEach(t => inviteData.value[t.id] = { name: '', email: '', message: '', error: '' });
  });

  async function addTeam() {
    if (!newTeamName.value) return;
    const { data } = await axios.post('/api/teams', { name: newTeamName.value });
    teams.value.push(data);
    inviteData.value[data.id] = { name: '', email: '', message: '', error: '' };
    newTeamName.value = '';
  }

  async function invite(teamId) {
    const entry = inviteData.value[teamId];
    entry.message = '';
    entry.error = '';
    try {
      const { data } = await axios.post(`/api/teams/${teamId}/invite`, { name: entry.name, email: entry.email });
      entry.message = `Invited ${data.email}`;
      entry.name = '';
      entry.email = '';
    } catch (err) {
      if (err.response?.data?.message) {
        entry.error = err.response.data.message;
      } else {
        entry.error = err.message;
      }
    }
  }

  async function removeTeam(teamId) {
    await axios.delete(`/api/teams/${teamId}`);
    teams.value = teams.value.filter(t => t.id !== teamId);
    delete inviteData.value[teamId];
  }
</script>
