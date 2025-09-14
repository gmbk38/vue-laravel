<template>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card shadow">
          <div class="card-body p-4">
            <h2 class="card-title text-center mb-4">Регистрация</h2>
            <form @submit.prevent="handleRegistration">
              <div class="mb-3">
                <label for="newUsername" class="form-label">Логин:</label>
                <input 
                  class="form-control form-control-lg"
                  type="text" 
                  id="newUsername" 
                  v-model="registrationForm.username" 
                  required
                  placeholder="Введите логин"
                />
              </div>
              <div class="mb-3">
                <label for="newPassword" class="form-label">Пароль:</label>
                <input 
                  class="form-control form-control-lg"
                  type="password" 
                  id="newPassword" 
                  v-model="registrationForm.password" 
                  required
                  placeholder="Введите пароль"
                />
              </div>
              <div class="d-grid gap-2 pt-2">
                <button type="submit" class="btn btn-success btn-lg">
                  Зарегистрироваться
                </button>
              </div>
              <div v-if="registrationError" class="alert alert-danger mt-3 mb-0" role="alert">
                {{ registrationError }}
              </div>
            </form>
            <div class="text-center mt-3">
              <router-link to="/login" class="text-decoration-none">
                Вернуться к авторизации
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useSession } from '@/composables/useSession';
import { useRouter } from 'vue-router';

const { register } = useSession();
const router = useRouter();

const registrationForm = ref({
  username: '',
  password: ''
});
const registrationError = ref('');

const handleRegistration = async () => {
  try {
    const response = await register(registrationForm.value.username, registrationForm.value.password);
    
    if (!response.errors) {
      // Переходим на страницу входа с параметром успешной регистрации
      router.push({ 
        path: '/login', 
        query: { registrationSuccess: true } 
      });
    } else {
      registrationError.value = response.errors;
    }
  } catch (error) {
    registrationError.value = error.response?.data?.message || 'Ошибка регистрации';
  }
};
</script>