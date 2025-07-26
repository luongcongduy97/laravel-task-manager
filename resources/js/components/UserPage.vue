<template>
  <div class="fade-in bg-white/80 backdrop-blur-md p-6 rounded-2xl shadow-2xl w-full max-w-4xl space-y-6">
    <h2 class="text-3xl font-extrabold bg-gradient-to-r from-purple-600 to-pink-500 text-transparent bg-clip-text text-center">
      User Dashboard
    </h2>

    <div v-if="user" class="space-y-4">
      <div class="flex items-center space-x-4">
        <img :src="user.avatar || '/default-avatar.png'" class="w-12 h-12 rounded-full ring-2 ring-purple-500" />
        <div>
          <p class="font-semibold text-gray-800 text-lg">{{ user.name }}</p>
          <p class="text-sm text-gray-500">{{ user.role }}</p>
        </div>
      </div>

      <div v-if="teams.length">
        <h3 class="font-semibold text-gray-700 mb-1">Your Teams</h3>
        <ul class="list-disc pl-5 text-gray-600">
          <li v-for="t in teams" :key="t.id">{{ t.name }}</li>
        </ul>
      </div>
    </div>

    <div v-if="isAdmin">
      <h3 class="text-xl font-semibold text-gray-700 mt-6 mb-2">All Users</h3>
      <div class="overflow-auto rounded-xl border border-gray-200 shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gradient-to-r from-purple-500 to-pink-500 text-white text-sm">
            <tr>
              <th class="px-4 py-3 text-left font-semibold">Name</th>
              <th class="px-4 py-3 text-left font-semibold">Email</th>
              <th class="px-4 py-3 text-left font-semibold">Role</th>
              <th class="px-4 py-3 text-center font-semibold">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100 text-sm text-gray-700">
            <tr v-for="u in users" :key="u.id" class="hover:bg-gray-50 transition">
              <td class="px-4 py-2 font-medium">{{ u.name }}</td>
              <td class="px-4 py-2">{{ u.email }}</td>
              <td class="px-4 py-2">
                <select
                  v-model="u.role"
                  @change="changeRole(u)"
                  class="border rounded-md px-2 py-1 w-full focus:ring-2 focus:ring-purple-500 outline-none transition"
                >
                  <option value="Admin">Admin</option>
                  <option value="Manager">Manager</option>
                  <option value="Member">Member</option>
                </select>
              </td>
              <td class="px-4 py-2 text-center">
                <button
                  @click="removeUser(u)"
                  class="text-red-600 hover:text-red-800 font-semibold transition"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, onMounted } from 'vue'
  import axios from 'axios'
  import { useRouter } from 'vue-router'
  import { isAuthenticated } from '../auth.js'

  const router = useRouter()
  const user = ref(null)
  const users = ref([])
  const isAdmin = ref(false)
  const teams = ref([])

  async function fetchUser() {
    const { data } = await axios.get('/api/user')
    user.value = data
    teams.value = data.teams || []
    isAdmin.value = data.role === 'Admin'
    if (isAdmin.value) {
      const res = await axios.get('/api/users')
      users.value = res.data
    }
  }

  async function changeRole(u) {
    await axios.put(`/api/users/${u.id}`, { role: u.role })
  }

  async function removeUser(u) {
    if (confirm(`Remove ${u.name}?`)) {
      await axios.delete(`/api/users/${u.id}`)
      users.value = users.value.filter(item => item.id !== u.id)
    }
  }

  onMounted(() => {
    const token = localStorage.getItem('token')
    if (!token) {
      router.push('/login')
    } else {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
      isAuthenticated.value = true
      fetchUser()
    }
  })
</script>
