<template>
  <form @submit.prevent="register" class="bg-white p-6 rounded shadow w-80 space-y-4">
    <h2 class="text-xl font-semibold text-center">Register</h2>
    <div>
      <input v-model="form.name" type="text" placeholder="Name" class="w-full border px-3 py-2 rounded" required>
    </div>
    <div>
      <input v-model="form.email" type="email" placeholder="Email" class="w-full border px-3 py-2 rounded" required>
    </div>
    <div>
      <input v-model="form.password" type="password" placeholder="Password" class="w-full border px-3 py-2 rounded" required>
    </div>
    <div>
      <input v-model="form.password_confirmation" type="password" placeholder="Confirm Password" class="w-full border px-3 py-2 rounded" required>
    </div>
    <div v-if="error" class="text-red-500 text-sm">{{ error }}</div>
    <button type="submit" class="w-full bg-green-500 text-white py-2 rounded">Register</button>
    <p class="text-sm text-center">Already have an account? <router-link to="/login" class="text-blue-500">Login</router-link></p>
  </form>
</template>

<script setup>
import { reactive, ref } from 'vue';
import axios from 'axios';

const form = reactive({ name: '', email: '', password: '', password_confirmation: ''});
const error = ref('');

async function register(){
  try {
    const { data } = await axios.post('/api/register', form);
    localStorage.setItem('token', data.token);
    error.value = '';
    alert('Registration Successful!');
  } catch (err) {
    error.value = 'Registration failed';
  }
}
</script>