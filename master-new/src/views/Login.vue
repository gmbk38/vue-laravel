<template>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card shadow">
          <div class="card-body p-4">
            <h2 class="card-title text-center mb-4">Авторизация</h2>
            <form @submit.prevent="handleLogin">
              <div class="mb-3">
                <label for="username" class="form-label">Логин:</label>
                <input 
                  class="form-control form-control-lg"
                  type="text" 
                  id="username" 
                  v-model="loginForm.username" 
                  required
                  placeholder="Введите логин"
                  autocomplete="unimon-master-username"
                />
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Пароль:</label>
                <input 
                  class="form-control form-control-lg"
                  type="password" 
                  id="password" 
                  v-model="loginForm.password" 
                  required
                  placeholder="Введите пароль"
                  autocomplete="unimon-master-password"
                />
              </div>
              <div class="d-grid gap-2 pt-2">
                <button type="submit" class="btn btn-primary btn-lg">
                  Войти
                </button>
              </div>
              <div v-if="loginError" class="alert alert-danger mt-3 mb-0" role="alert">
                {{ loginError }}
              </div>
              <div v-if="registrationWarning" class="alert alert-warning mt-3 mb-0" role="alert">
                {{ registrationWarning }}
              </div>
            </form>
            <div class="text-center mt-3">
              <router-link to="/register" class="text-decoration-none">
                Зарегистрироваться
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useSession } from '@/composables/useSession';
import { useRouter, useRoute } from 'vue-router';

const { login } = useSession();
const router = useRouter();
const route = useRoute();

const loginForm = ref({
  username: '',
  password: ''
});
const loginError = ref('');
const registrationWarning = ref('');

onMounted(() => {
  // Показываем сообщение об успешной регистрации если есть параметр
  if (route.query.registrationSuccess) {
    registrationWarning.value = 'Регистрация прошла успешно. Для активации учётной записи обратитесь к администратору';
  }
});

const handleLogin = async () => {
  try {
    const success = await login(loginForm.value.username, loginForm.value.password);
    if (success) {
      // Перенаправляем на защищенную страницу
      router.push('/devices');
    } else {
      loginError.value = 'Неверный логин или пароль';
    }
  } catch (error) {
    loginError.value = 'Ошибка авторизации: ' + error.message;
  }
};
</script>