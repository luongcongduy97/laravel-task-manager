<template>
  <form @submit.prevent="login" class="fade-in bg-white/80 backdrop-blur-md p-8 rounded-lg shadow-xl w-96 space-y-5">
    <h2 class="text-2xl font-bold text-center text-gray-700">Welcome back</h2>
    <div>
      <input v-model="form.email" type="email" placeholder="Email" class="w-full border px-4 py-2 rounded focus:ring-2 focus:ring-purple-500 outline-one transition" required>
    </div>
    <div>
      <input v-model="form.password" type="password" placeholder="Password" class="w-full border px-4 py-2 rounded focus:ring-2 focus:ring-purple-500 outline-one transition" required>
    </div>
    <div v-if="error" class="text-red-500 text-sm">{{ error }}</div>
    <button type="submit" class="w-full bg-gradient-to-r from-purple-500 to-pink-500 hover:from-pink-500 hover:to-purple-500 text-white py-2 rounded-lg shadow-md transition transform hover:scale-105">Login</button>
    <p class="text-sm text-center text-gray-600">Don't have an account? <router-link to="/register" class="text-purple-600 hover:underline">Register</router-link></p>
  </form>
</template>

<script setup>
import { reactive, ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { isAuthenticated } from '../auth.js';

const router = useRouter();
const form = reactive({ email: '', password: '' });
const error = ref('');

async function login() {
  try {
    const { data } = await axios.post('/api/login', form);
    localStorage.setItem('token', data.token);
    axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`;
    isAuthenticated.value = true;
    error.value = '';
    router.push('/users');
  } catch (e) {
    error.value = 'Invalid credentials';
  }
}
</script>