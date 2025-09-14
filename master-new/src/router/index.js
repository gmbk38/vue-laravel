import { createRouter, createWebHistory } from 'vue-router';
import Login from '@/views/Login.vue';
import Register from '@/views/Register.vue';
import Devices from '@/views/Devices.vue';
import User from '@/views/User.vue';
import Alfa from '@/views/Alfa.vue';
import Dashboard from '@/views/Dashboard.vue';

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresAuth: false }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { requiresAuth: false }
  },
  {
    path: '/devices',
    name: 'Devices',
    component: Devices,
    meta: { requiresAuth: true }
  },
  {
    path: '/user/:id',
    name: 'User',
    component: User,
    meta: { requiresAuth: true }
  },
  {
    path: '/alfa',
    name: 'Alfa',
    component: Alfa,
    meta: { requiresAuth: true }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/',
    redirect: '/devices'
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach((to, from, next) => {
  // Проверяем наличие сессии в localStorage
  const authSession = localStorage.getItem('authSession');
  const isAuthenticated = !!authSession;

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login'); // Используем next для перенаправления
  } else if ((to.path === '/login' || to.path === '/register') && isAuthenticated) {
    next('/'); // Перенаправляем на главную страницу, если пользователь уже авторизован
  } else {
    next(); // Продолжаем переход, если всё в порядке
  }
});

export default router;
