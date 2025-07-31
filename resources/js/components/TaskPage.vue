<template>
  <div class="fade-in bg-white/80 backdrop-blur-md p-6 rounded-2xl shadow-2xl w-full max-w-3xl space-y-6">
    <h2 class="text-3xl font-extrabold bg-gradient-to-r from-purple-600 to-pink-500 text-transparent bg-clip-text text-center">
      Tasks
    </h2>

    <div class="flex flex-col sm:flex-row sm:space-x-3 space-y-3 sm:space-y-0">
      <input
        v-model="newTask.name"
        placeholder="Name"
        class="border px-3 py-2 rounded-md w-full focus:ring-2 focus:ring-purple-500 transition"
      />
      <input
        v-model="newTask.status"
        placeholder="Status"
        class="border px-3 py-2 rounded-md w-full focus:ring-2 focus:ring-purple-500 transition"
      />
      <input
        v-model="newTask.priority"
        placeholder="Priority"
        class="border px-3 py-2 rounded-md w-full focus:ring-2 focus:ring-purple-500 transition"
      />
      <input
        v-model="newTask.due_date"
        type="date"
        placeholder="Due Date"
        class="border px-3 py-2 rounded-md w-full focus:ring-2 focus:ring-purple-500 transition"
      />
      <button
        @click="addTask"
        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md font-semibold transition"
      >
        Add
      </button>
    </div>

    <ul class="space-y-5">
      <li
        v-for="t in tasks"
        :key="t.id"
        class="border rounded-xl p-5 shadow-sm hover:shadow-md transition bg-white"
      >
        <!-- Normal View -->
        <div v-if="!editData[t.id].editing">
          <h3 class="text-lg font-semibold text-gray-800">{{ t.name }}</h3>
          <p class="text-sm text-gray-600 mt-1" v-if="t.description">{{ t.description }}</p>
          <p class="text-sm text-gray-600">Status: {{ t.status }}</p>
          <p class="text-sm text-gray-600">Priority: {{ t.priority }}</p>
          <p class="text-sm text-gray-600">Due: {{ t.due_date }}</p>
          <div class="flex space-x-4 mt-3">
            <button @click="startEdit(t.id)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
              Edit
            </button>
            <button
              @click="removeTask(t.id)"
              class="text-red-600 hover:text-red-800 text-sm font-medium"
              data-test="delete-task"
            >
              Delete
            </button>
          </div>
        </div>

        <!-- Edit Mode -->
        <div v-else class="space-y-3">
          <input
            v-model="editData[t.id].name"
            class="border px-3 py-2 rounded-md w-full focus:ring-2 focus:ring-purple-500 transition"
          />
          <input
            v-model="editData[t.id].description"
            class="border px-3 py-2 rounded-md w-full focus:ring-2 focus:ring-purple-500 transition"
          />
          <input
            v-model="editData[t.id].status"
            class="border px-3 py-2 rounded-md w-full focus:ring-2 focus:ring-purple-500 transition"
          />
          <input
            v-model="editData[t.id].priority"
            class="border px-3 py-2 rounded-md w-full focus:ring-2 focus:ring-purple-500 transition"
          />
          <input
            v-model="editData[t.id].due_date"
            type="date"
            class="border px-3 py-2 rounded-md w-full focus:ring-2 focus:ring-purple-500 transition"
          />
          <div class="flex space-x-3">
            <button
              @click="updateTask(t.id)"
              class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md font-semibold transition"
            >
              Save
            </button>
            <button
              @click="cancelEdit(t.id)"
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
const tasks = ref([]);
const newTask = ref({ name: '', description: '', status: '', priority: '', due_date: '' });
const editData = ref({});

onMounted(async () => {
  const token = localStorage.getItem('token');
  if (!token) {
    router.push('/login');
    return;
  }
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
  isAuthenticated.value = true;
  const res = await axios.get(`/api/projects/${route.params.projectId}/tasks`);
  tasks.value = res.data;
  tasks.value.forEach(t => {
    editData.value[t.id] = { ...t, editing: false };
  });
});

async function addTask() {
  if (!newTask.value.name) return;
  const { data } = await axios.post(`/api/projects/${route.params.projectId}/tasks`, newTask.value);
  tasks.value.push(data);
  editData.value[data.id] = { ...data, editing: false };
  newTask.value = { name: '', description: '', status: '', priority: '', due_date: '' };
}

function startEdit(id) {
  editData.value[id].editing = true;
}

function cancelEdit(id) {
  editData.value[id].editing = false;
  const original = tasks.value.find(t => t.id === id);
  editData.value[id] = { ...original, editing: false };
}

async function updateTask(id) {
  const payload = {
    name: editData.value[id].name,
    description: editData.value[id].description,
    status: editData.value[id].status,
    priority: editData.value[id].priority,
    due_date: editData.value[id].due_date,
  };
  const { data } = await axios.put(`/api/tasks/${id}`, payload);
  const idx = tasks.value.findIndex(t => t.id === id);
  tasks.value[idx] = data;
  editData.value[id] = { ...data, editing: false };
}

async function removeTask(id) {
  await axios.delete(`/api/tasks/${id}`);
  tasks.value = tasks.value.filter(t => t.id !== id);
  delete editData.value[id];
}
</script>