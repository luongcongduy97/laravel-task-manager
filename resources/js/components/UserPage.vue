<template>
  <div class="bg-white p-6 rounded shadow w-full max-w-2xl space-y-4">
    <h2 class="text-xl font-semibold text-center">User Page</h2>
    <div v-if="user">
      <p class="font-semibold">Logged in as: {{ user.name }} ({{ user.role }})</p>
    </div>
    <div v-if="isAdmin">
      <h3 class="text-lg font-semibold mt-4">All Users</h3>
      <table class="w-full text-left border">
        <thead>
          <tr>
            <th class="border px-2">Name</th>
            <th class="border px-2">Email</th>
            <th class="border px-2">Role</th>
            <th class="border px-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="u in users" :key="u.id">
            <td class="border px-2">{{ u.name }}</td>
            <td class="border px-2">{{ u.email }}</td>
            <td class="border px-2">
              <select v-model="u.role" @change="changeRole(u)" class="border rounded px-1">
                <option value="Admin">Admin</option>
                <option value="Manager">Manager</option>
                <option value="Member">Member</option>
              </select>
            </td>
            <td class="border px-2 text-center">
              <button @click="removeUser(u)" class="text-red-500">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const user = ref(null);
const users = ref([]);
const isAdmin = ref(false);

async function fetchUser() {
  const { data } = await axios.get('/api/user');
  user.value = data;
  isAdmin.value = data.role === 'Admin';
  if (isAdmin.value) {
    const res = await axios.get('/api/users');
    users.value = res.data;
  }
}

async function changeRole(u) {
  await axios.put(`/api/users/${u.id}`, { role: u.role });
}

async function removeUser(u) {
  await axios.delete(`/api/users/${u.id}`);
  users.value = users.value.filter(item => item.id !== u.id);
}

onMounted(() => {
  const token = localStorage.getItem('token');
  if (!token) {
    router.push('/login');
  } else {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    fetchUser();
  }
});
</script>