<!-- Шаблон панели управления пользователем -->
<template>
  <navbar></navbar>
  <notification :message="nText" :type="nType"></notification>
  <div class="container mt-4">

    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="create-invoice-tab" data-bs-toggle="tab" data-bs-target="#create-invoice"
          type="button" role="tab" aria-controls="create-invoice" aria-selected="true">Формирование счетов</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices" type="button"
          role="tab" aria-controls="invoices" aria-selected="false">Счета</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="acts-tab" data-bs-toggle="tab" data-bs-target="#acts" type="button"
          role="tab" aria-controls="acts" aria-selected="false">Акты</button>
      </li>
    </ul>

    <div class="tab-content" id="myTabContent">
      <div class="tab-pane fade show active" id="create-invoice" role="tabpanel" aria-labelledby="create-invoice-tab">
        <user-dev-table @invoices-reload="reloadInvoicesTab"></user-dev-table>
      </div>
      <div class="tab-pane fade" id="invoices" role="tabpanel" aria-labelledby="invoices-tab">
        <invoices-table ref="invoiceTabRef" @set-notification="showNotification"></invoices-table>
        <!-- <h2>User tab2 {{ $route.params.id }}</h2>
            <h2>{{ userData }}</h2> -->
      </div>
      <div class="tab-pane fade" id="acts" role="tabpanel" aria-labelledby="acts-tab">
        <acts-table @set-notification="showNotification"></acts-table>
        <!-- <h2>User tab2 {{ $route.params.id }}</h2>
            <h2>{{ userData }}</h2> -->
      </div>
    </div>

  </div>

</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useSession } from '@/composables/useSession';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';

const { user, remainingTime, sessionToken, logout } = useSession();
const router = useRouter();
const route = useRoute();
const userData = ref([]);

const invoiceTabRef = ref(null);

// Уведомления
const nText = ref('')
const nType = ref('info')

const showNotification = (text, type='info') => {
  nText.value = text;
  nType.value = type;
};

const fetchUserData = async () => {
  try {
    const params = {
      userId: route.params.id
    }
    const response = await axios.get(process.env.VUE_APP_API_SERVER_URL + '...', {
      params,
      headers: {
        'X-Session-Token': sessionToken.value
      }
    });
    userData.value = response.data;
  } catch (error) {
    console.error('Ошибка при получении данных для селекторов:', error);
  }
};

onMounted(async () => {
  await fetchUserData();
});

const reloadInvoicesTab = () => {
  if (invoiceTabRef.value) {
    invoiceTabRef.value.fetchInovices();
  }
};

const handleLogout = () => {
  logout();
  router.push('/login');
};

</script>