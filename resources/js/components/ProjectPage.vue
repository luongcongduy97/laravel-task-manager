<template>
  <div class="fade-in bg-white/80 backdrop-blur-md p-6 rounded-lg shadow-xl w-full max-w-xl space-y-4">
    <h2 class="text-2xl font-bold text-center text-gray-700">Projects</h2>
    <div class="flex space-x-2">
      <input v-model="newProject.name" placeholder="Name" class="border px-2 py-1 rounded w-full" />
      <input v-model="newProject.description" placeholder="Description" class="border px-2 py-1 rounded w-full" />
      <button @click="addProject" class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded">Add</button>
    </div>
    <ul class="space-y-4">
      <li v-for="p in projects" :key="p.id" class="border p-3 rounded">
        <div v-if="!editData[p.id].editing">
          <h3 class="font-semibold">{{ p.name }}</h3>
          <p class="text-sm text-gray-600" v-if="p.description">{{ p.description }}</p>
          <div class="space-x-2 mt-2">
            <button @click="startEdit(p.id)" class="text-blue-600 hover:text-blue-800">Edit</button>
            <button @click="removeProject(p.id)" class="text-red-600 hover:text-red-800" data-test="delete-project">Delete</button>
          </div>
        </div>
        <div v-else class="space-y-2">
          <input v-model="editData[p.id].name" class="border px-2 py-1 rounded w-full" />
          <input v-model="editData[p.id].description" class="border px-2 py-1 rounded w-full" />
          <div class="space-x-2">
            <button @click="updateProject(p.id)" class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded">Save</button>
            <button @click="cancelEdit(p.id)" class="text-gray-600 hover:text-gray-800">Cancel</button>
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
  projects.value.forEach(p => editData.value[p.id] = { name: p.name, description: p.description, editing: false });
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
  editData.value[id].name = projects.value.find(p => p.id === id).name;
  editData.value[id].description = projects.value.find(p => p.id === id).description;
}

async function updateProject(id) {
  const payload = { name: editData.value[id].name, description: editData.value[id].description };
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