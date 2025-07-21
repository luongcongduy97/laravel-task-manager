import { createRouter, createWebHistory } from 'vue-router';
import Login from './components/Login.vue';
import Register from './components/Register.vue';
import UserPage from './components/UserPage.vue';
import TeamPage from './components/TeamPage.vue';
import { isAuthenticated } from './auth.js';

const routes = [
  { path: '/', redirect: '/login' },
  { path: '/login', component: Login },
  { path: '/register', component: Register },
  { path: '/users', component: UserPage, meta: { requiresAuth: true } },
  { path: '/teams', component: TeamPage, meta: { requiresAuth: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !isAuthenticated.value) {
    next('/login');
  } else if ((to.path === '/login' || to.path === '/register') && isAuthenticated.value) {
    next('/users');
  } else {
    next();
  }
});

export default router;