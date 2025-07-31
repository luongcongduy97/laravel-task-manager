<template>
  <div class="fade-in bg-white/80 backdrop-blur-md p-6 rounded-2xl shadow-2xl w-full max-w-3xl space-y-6">
    <h2 class="text-3xl font-extrabold bg-gradient-to-r from-purple-600 to-pink-500 text-transparent bg-clip-text text-center">
      Projects
    </h2>

    <div class="flex flex-col sm:flex-row sm:space-x-3 space-y-3 sm:space-y-0">
      <input
        v-model="newProject.name"
        placeholder="Name"
        class="border px-3 py-2 rounded-md w-full focus:ring-2 focus:ring-purple-500 transition"
      />
      <input
        v-model="newProject.description"
        placeholder="Description"
        class="border px-3 py-2 rounded-md w-full focus:ring-2 focus:ring-purple-500 transition"
      />
      <button
        @click="addProject"
        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md font-semibold transition"
      >
        Add
      </button>
    </div>

    <ul class="space-y-5">
      <li
        v-for="p in projects"
        :key="p.id"
        class="border rounded-xl p-5 shadow-sm hover:shadow-md transition bg-white"
      >
        <!-- Normal View -->
        <div v-if="!editData[p.id].editing">
          <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">{{ p.name }}</h3>
            <router-link
              :to="`/projects/${p.id}/tasks`"
              class="text-blue-600 hover:text-blue-800 text-sm font-medium"
            >
              View Tasks →
            </router-link>
          </div>
          <p class="text-sm text-gray-600 mt-1" v-if="p.description">{{ p.description }}</p>
          <div class="flex space-x-4 mt-3">
            <button @click="startEdit(p.id)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
              Edit
            </button>
            <button
              @click="removeProject(p.id)"
              class="text-red-600 hover:text-red-800 text-sm font-medium"
              data-test="delete-project"
            >
              Delete
            </button>
          </div>
        </div>

        <!-- Edit Mode -->
        <div v-else class="space-y-3">
          <input
            v-model="editData[p.id].name"
            class="border px-3 py-2 rounded-md w-full focus:ring-2 focus:ring-purple-500 transition"
          />
          <input
            v-model="editData[p.id].description"
            class="border px-3 py-2 rounded-md w-full focus:ring-2 focus:ring-purple-500 transition"
          />
          <div class="flex space-x-3">
            <button
              @click="updateProject(p.id)"
              class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md font-semibold transition"
            >
              Save
            </button>
            <button
              @click="cancelEdit(p.id)"
              class="text-gray-600 hover:text-gray-800 text-sm font-medium"
            >
              Cancel
            </button>
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter, useRoute } from 'vue-router';
import { isAuthenticated } from '../auth.js';

const router = useRouter();
const route = useRoute();
const projects = ref([]);
const newProject = ref({ name: '', description: '' });
const editData = ref({});

onMounted(async () => {
  const token = localStorage.getItem('token');
  if (!token) {
    router.push('/login');
    return;
  }
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
  isAuthenticated.value = true;
  const res = await axios.get(`/api/teams/${route.params.teamId}/projects`);
  projects.value = res.data;
  projects.value.forEach(p => {
    editData.value[p.id] = {
      name: p.name,
      description: p.description,
      editing: false,
    };
  });
});

async function addProject() {
  if (!newProject.value.name) return;
  const { data } = await axios.post(`/api/teams/${route.params.teamId}/projects`, newProject.value);
  projects.value.push(data);
  editData.value[data.id] = { name: data.name, description: data.description, editing: false };
  newProject.value = { name: '', description: '' };
}

function startEdit(id) {
  editData.value[id].editing = true;
}

function cancelEdit(id) {
  editData.value[id].editing = false;
  const original = projects.value.find(p => p.id === id);
  editData.value[id].name = original.name;
  editData.value[id].description = original.description;
}

async function updateProject(id) {
  const payload = {
    name: editData.value[id].name,
    description: editData.value[id].description,
  };
  const { data } = await axios.put(`/api/projects/${id}`, payload);
  const idx = projects.value.findIndex(p => p.id === id);
  projects.value[idx] = data;
  editData.value[id] = { ...payload, editing: false };
}

async function removeProject(id) {
  await axios.delete(`/api/projects/${id}`);
  projects.value = projects.value.filter(p => p.id !== id);
  delete editData.value[id];
}
</script>
