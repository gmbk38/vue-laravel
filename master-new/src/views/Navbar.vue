<template>
    <nav class="navbar navbar-light" style="background-color: #e3f2fd;">
        <div class="container-fluid justify-content-end">
            <p class="mb-0 me-3">Добро пожаловать, {{ user }}!<br>Вы авторизованы. Сессия истечет через: {{
                remainingTime }} сек.</p>
            <button class="btn btn-outline-danger" @click="handleLogout">Выйти</button>
        </div>
    </nav>
</template>
<script setup>
import { useSession } from '@/composables/useSession';
import { useRouter } from 'vue-router';

const { user, remainingTime, sessionToken, logout } = useSession();
const router = useRouter();

const handleLogout = () => {
    logout();
    router.push('/login');
};

</script>