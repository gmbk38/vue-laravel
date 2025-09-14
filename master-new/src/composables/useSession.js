import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const SESSION_DURATION = 86400000; // 24 часа в мс

// Состояние
const user = ref(null);
const isAuthenticated = ref(false);
const sessionStartTime = ref(null);
const sessionTimer = ref(null);
const sessionTimeout = ref(null);
const currentTime = ref(Date.now());
const sessionToken = ref(null);

// Проверяем сессию при загрузке
const checkSession = () => {
  const sessionStored = localStorage.getItem('authSession');
  if (sessionStored) {
    const { user: savedUser, token, startTime } = JSON.parse(sessionStored);
    const elapsed = Date.now() - startTime;
    
    if (elapsed < SESSION_DURATION) {
      startSession(savedUser, token, startTime);
    } else {
      localStorage.removeItem('authSession');
    }
  }
};

// Запускаем сессию
const startSession = (username, token, startTime = Date.now()) => {
  user.value = username;
  isAuthenticated.value = true;
  sessionStartTime.value = startTime;
  sessionToken.value = token;
  currentTime.value = Date.now();
  
  // Сохраняем в localStorage
  localStorage.setItem('authSession', JSON.stringify({
    user: username,
    token: token,
    startTime: startTime
  }));
  
  // Очищаем предыдущие таймеры
  clearTimers();
  
  // Таймер для автоматического выхода
  const timeLeft = SESSION_DURATION - (Date.now() - startTime);
  if (timeLeft > 0) {
    sessionTimeout.value = setTimeout(() => {
      handleAutoLogout();
    }, timeLeft);
  }
  
  // Таймер для обновления оставшегося времени (каждую секунду)
  sessionTimer.value = setInterval(() => {
    currentTime.value = Date.now();
    if (remainingTime.value <= 0) {
      clearInterval(sessionTimer.value);
    }
  }, 1000);
};

// Оставшееся время в секундах
const remainingTime = computed(() => {
  if (!sessionStartTime.value) return 0;
  const elapsed = currentTime.value - sessionStartTime.value;
  const secondsLeft = Math.round((SESSION_DURATION - elapsed) / 1000);
  return secondsLeft > 0 ? secondsLeft : 0;
});

// Автоматический выход
const handleAutoLogout = () => {
  logout();
};

// Выход
const logout = () => {
  isAuthenticated.value = false;
  user.value = null;
  sessionStartTime.value = null;
  clearTimers();
  localStorage.removeItem('authSession');
  sessionToken.value = null;
};

// Очистка таймеров
const clearTimers = () => {
  if (sessionTimer.value) {
    clearInterval(sessionTimer.value);
    sessionTimer.value = null;
  }
  if (sessionTimeout.value) {
    clearTimeout(sessionTimeout.value);
    sessionTimeout.value = null;
  }
};

// Проверяем сессию при инициализации
checkSession();

// Экспортируем функции для использования
export function useSession() {
  const router = useRouter();
  
  // Логин
  const login = async (username, password) => {
    try {
      const response = await axios.post(process.env.VUE_APP_API_SERVER_URL + '...', {
        login: username,
        pwd: password
      });
      if (response.data.session) {
        startSession(username, response.data.session);
        return true;
      }
    } catch (error) {
      console.error('Login error:', error);
      throw error;
    }
    return false;
  };
  
  // Регистрация
  const register = async (username, password) => {
    try {
      const response = await axios.post(process.env.VUE_APP_API_SERVER_URL + '...', {
        login: username,
        pwd: password
      });
      return response.data;
    } catch (error) {
      console.error('Registration error:', error);
      throw error;
    }
  };
  
  return {
    user,
    isAuthenticated,
    sessionToken,
    remainingTime,
    login,
    register,
    logout,
    handleAutoLogout
  };
}