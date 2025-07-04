<template>
  <form @submit.prevent="login" class="bg-white p-6 rounded shadow w-80 space-y-4">
    <h2 class="text-xl font-semibold text-center">Login</h2>
    <div>
      <input v-model="form.email" type="email" placeholder="Email" class="w-full border px-3 py-2 rounded" required>
    </div>
    <div>
      <input v-model="form.password" type="password" placeholder="Password" class="w-full border px-3 py-2 rounded" required>
    </div>
    <div v-if="error" class="text-red-500 text-sm">{{ error }}</div>
    <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded">Login</button>
    <p class="text-sm text-center">Don't have an account? <router-link to="/register" class="text-blue-500">Register</router-link></p>
  </form>
</template>

<script setup>
import { reactive, ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const form = reactive({ email: '', password: '' });
const error = ref('');

async function login() {
  try {
    const { data } = await axios.post('/api/login', form);
    localStorage.setItem('token', data.token);
    error.value = '';
    alert('Logged in!');
    // redirect or further logic
  } catch (e) {
    error.value = 'Invalid credentials';
  }
}
</script>