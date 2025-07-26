<template>
  <div class="fade-in bg-white/80 backdrop-blur-md p-6 rounded-2xl shadow-2xl w-full max-w-3xl space-y-6">
    <h2 class="text-3xl font-extrabold bg-gradient-to-r from-purple-600 to-pink-500 text-transparent bg-clip-text text-center">
      Teams
    </h2>

    <div v-if="isAdmin" class="flex flex-col sm:flex-row sm:space-x-3 sm:items-center space-y-3 sm:space-y-0">
      <input
        v-model="newTeamName"
        placeholder="Team name"
        class="border px-3 py-2 rounded-md w-full focus:ring-2 focus:ring-purple-500 transition"
      />
      <button
        @click="addTeam"
        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md font-semibold transition"
      >
        Add Team
      </button>
    </div>

    <ul class="space-y-5">
      <li
        v-for="team in teams"
        :key="team.id"
        class="border rounded-xl p-5 shadow-sm hover:shadow-md transition bg-white"
      >
        <div class="flex justify-between items-center mb-2">
          <h3 class="text-lg font-semibold text-gray-800">{{ team.name }}</h3>
          <router-link
            :to="`/teams/${team.id}/projects`"
            class="text-blue-600 hover:text-blue-800 text-sm font-medium"
          >
            View Projects →
          </router-link>
        </div>

        <div v-if="isAdmin" class="space-y-3">
          <form
            @submit.prevent="invite(team.id)"
            class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 space-y-3 sm:space-y-0"
          >
            <input
              v-model="inviteData[team.id].name"
              type="text"
              placeholder="Name"
              class="border px-3 py-2 rounded-md w-full focus:ring-2 focus:ring-purple-500 transition"
              required
            />
            <input
              v-model="inviteData[team.id].email"
              type="email"
              placeholder="Email"
              class="border px-3 py-2 rounded-md w-full focus:ring-2 focus:ring-purple-500 transition"
              required
            />
            <button
              type="submit"
              class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md font-semibold transition"
            >
              Invite
            </button>
          </form>

          <p v-if="inviteData[team.id].message" class="text-green-600 text-sm font-medium">
            {{ inviteData[team.id].message }}
          </p>
          <p v-if="inviteData[team.id].error" class="text-red-600 text-sm font-medium">
            {{ inviteData[team.id].error }}
          </p>

          <button
            @click="removeTeam(team.id)"
            data-test="delete-team"
            class="text-red-600 hover:text-red-800 text-sm font-medium"
          >
            Delete Team
          </button>
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
      const { data } = await axios.post(`/api/teams/${teamId}/invite`, {
        name: entry.name,
        email: entry.email,
      });
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
