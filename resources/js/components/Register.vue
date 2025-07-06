<template>
  <form @submit.prevent="register" class="fade-in bg-white/80 backdrop-blur-md p-8 rounded-lg shadow-xl w-96 space-y-5">
    <h2 class="text-2xl font-bold text-center text-gray-700">Create Account</h2>
    <div>
      <input v-model="form.name" type="text" placeholder="Name" class="w-full border px-4 py-2 rounded focus:ring-2 focus:ring-teal-500 outline-none transition" required>
    </div>
    <div>
      <input v-model="form.email" type="email" placeholder="Email" class="w-full border px-4 py-2 rounded focus:ring-2 focus:ring-teal-500 outline-none transition" required>
    </div>
    <div>
      <input v-model="form.password" type="password" placeholder="Password" class="w-full border px-4 py-2 rounded focus:ring-2 focus:ring-teal-500 outline-none transition" required>
    </div>
    <div>
      <input v-model="form.password_confirmation" type="password" placeholder="Confirm Password" class="w-full border px-4 py-2 rounded focus:ring-2 focus:ring-teal-500 outline-none transition" required>
    </div>
    <div v-if="error" class="text-red-500 text-sm">{{ error }}</div>
    <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-teal-500 hover:from-teal-500 hover:to-green-500 text-white py-2 rounded-lg shadow-md transition transform hover:scale-105">Register</button>
    <p class="text-sm text-center text-gray-600">Already have an account? <router-link to="/login" class="text-purple-600 hover:underline">Login</router-link></p>
  </form>
</template>

<script setup>
import { reactive, ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const form = reactive({ name: '', email: '', password: '', password_confirmation: ''});
const error = ref('');

async function register(){
  try {
    const { data } = await axios.post('/api/register', form);
    localStorage.setItem('token', data.token);
    axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`;
    error.value = '';
    router.push('/users');
  } catch (err) {
    error.value = 'Registration failed';
  }
}
</script>