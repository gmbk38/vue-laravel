<template>
  <div class="create-invoice-container">
    <notification :message="nText" :type="nType"></notification>
    <payer-form v-if="payerEdit" :payer="payerEdit" @close="closeForm"
      @set-notification="showNotification"
      @payers-reload="initPayers"></payer-form>

    <div class="createInvoiceTab">
      <!-- Секция: Таблица устройств -->
      <div class="section-card mb-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body p-0">
            <table id="userDevTable" class="table table-striped table-bordered m-0" style="width:100%">
              <thead>
                <tr>
                  <th>DevID</th>
                  <th>DescrOName</th>
                  <th>Tariff</th>
                  <th>PerMonth</th>
                  <th>TariffFinish</th>
                  <th>addedDays</th>
                  <th>NewTariffFinish</th>
                  <th>DevSum</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Секция: Выбор периода -->
      <div class="section-card mb-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h5 class="section-title mb-3 text-primary">Выберите срок продления:</h5>
            <div class="d-flex flex-wrap gap-2">
              <div class="form-check form-check-inline period-option">
                <input class="form-check-input" type="radio" id="periodYear" value="year" v-model="selectedPeriod">
                <label class="form-check-label btn btn-outline-primary py-2 px-3 rounded" for="periodYear">
                  На год
                </label>
              </div>
              <div class="form-check form-check-inline period-option">
                <input class="form-check-input" type="radio" id="periodQuarter" value="quarter"
                  v-model="selectedPeriod">
                <label class="form-check-label btn btn-outline-primary py-2 px-3 rounded" for="periodQuarter">
                  На квартал
                </label>
              </div>
              <div class="form-check form-check-inline period-option">
                <input class="form-check-input" type="radio" id="periodMonth" value="month" v-model="selectedPeriod">
                <label class="form-check-label btn btn-outline-primary py-2 px-3 rounded" for="periodMonth">
                  На месяц
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Секция: Выбор плательщика -->
      <div class="section-card mb-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="section-title text-primary m-0">Выберите плательщика:</h5>
              <button type="button" class="btn btn-outline-primary btn-sm" @click="handlePayerAdd">
                <i class="bi bi-plus-circle me-1"></i> Добавить плательщика
              </button>
            </div>

            <div v-if="payers && payers.length" class="payers-grid">
              <payer-card v-for="payer in payers" :key="payer.ID" :payer="payer" @payer-selected="handlePayerSelected"
                @payer-edit="handlePayerEdit"
                @set-notification="showNotification"
                @payers-reload="initPayers">
              </payer-card>
            </div>
            <div v-else class="empty-state py-4 text-center">
              <i class="bi bi-person-x fs-1 text-muted mb-2"></i>
              <p class="text-muted">Нет доступных плательщиков</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Секция: Формирование счета -->
      <div class="section-card">
        <div class="card border-0 shadow-sm">
          <div class="card-body text-center py-3">
            <button v-show="showCreateBtn" type="button" class="btn btn-success px-5 py-2 fw-medium"
              @click="createInvoice">
              <i class="bi bi-file-earmark-text me-2"></i> Сформировать счёт
            </button>
            <p v-show="!showCreateBtn" class="text-muted mb-0">
              Выберите плательщика для активации кнопки
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import { useSession } from '@/composables/useSession';
import { renderPrice, renderDate } from '@/assets/js/renderFunctions.js';
import { useRouter, useRoute } from 'vue-router';
import $ from 'jquery';
import 'datatables.net';
import 'datatables.net-dt';
import 'datatables.net-select';
import axios from 'axios';

// Инициализация композаблов
const { sessionToken, logout } = useSession();
const router = useRouter();
const route = useRoute();

// Реактивные переменные
const selectedDevices = ref([]);
const selectedPayer = ref(null);
const dataTable = ref(null);
const payers = ref([]);
const selectedPeriod = ref(null);
const payerEdit = ref(null);
const showCreateBtn = ref(false);
const isInitialLoad = ref(true); // Флаг первой загрузки

const emit = defineEmits(['invoices-reload']);
// Уведомления
const nText = ref('')
const nType = ref('info')

const showNotification = (text, type = 'info') => {
  nText.value = text;
  nType.value = type;
};

const handlePayerSelected = (payerId) => {
  console.log('Выбран плательщик:', payerId);
  selectedPayer.value = payerId;
};

const handlePayerEdit = (payer) => {
  payerEdit.value = payer;
};

const handlePayerAdd = () => {
  payerEdit.value = {};
};

const closeForm = () => {
  payerEdit.value = null;
};

// Инициализация плательщиков
const initPayers = async () => {
  try {
    const params = {
      userId: route.params.id,
      period: selectedPeriod.value
    }
    const response = await axios.get(
      process.env.VUE_APP_API_SERVER_URL + '...',
      {
        params,
        headers: {
          'X-Session-Token': sessionToken.value
        }
      }
    );
    payers.value = response.data.payers || [];
    // showNotification('Плательщики успешно загружены', 'success');
  } catch (error) {
    showNotification('Ошибка при получении плательщиков', 'danger');
    console.error('Ошибка при получении плательщиков:', error);
  }
};

// Восстановление выделенных строк
const restoreSelection = () => {
  if (!dataTable.value || selectedDevices.value.length === 0) return;

  dataTable.value.off('select deselect'); // Отключаем обработчики

  // Снимаем текущее выделение
  dataTable.value.rows().deselect();

  // Выделяем строки из selectedDevices
  dataTable.value.rows().every(function () {
    const rowData = this.data();
    if (selectedDevices.value.includes(rowData.ID)) {
      this.select();
    }
  });

  // Возвращаем обработчики
  dataTable.value.on('select deselect', function (e, dt, type, indexes) {
    updateSelectedDevices();
  });
};

// Обновление списка выбранных устройств
const updateSelectedDevices = () => {
  const selectedRows = dataTable.value.rows({ selected: true });
  selectedDevices.value = selectedRows.data().toArray().map(row => row.ID);
  fetchData(); // Вызываем после ручного изменения выделения
};

// Инициализация таблицы
const initDataTable = () => {
  dataTable.value = $('#userDevTable').DataTable({
    searching: false,
    paging: false,
    info: false,
    serverSide: true,
    processing: true,
    ajax: (data, callback, settings) => {
      const params = {
        draw: data.draw,
        start: data.start,
        length: data.length,
        search: data.search.value,
        orderColumn: data.order[0]?.column,
        orderDir: data.order[0]?.dir,
        userId: route.params.id,
        period: selectedPeriod.value,
        devices: selectedDevices.value.join(',')
      };

      axios.get(
        process.env.VUE_APP_API_SERVER_URL + '...',
        {
          params,
          headers: {
            'X-Session-Token': sessionToken.value
          }
        }
      )
        .then(response => {
          const formattedData = response.data.devices.map(item => ({
            DevID: item.ID,
            DescrOName: item.DescrOName,
            Tariff: item.Tariff,
            PerMonth: item.tariff?.PerMonth,
            TariffFinish: item.TariffFinish,
            addedDays: item.addedDays ?? null,
            NewTariffFinish: item.NewTariffFinish ?? null,
            DevSum: item.sum ?? null,
            ...item
          }));

          callback({
            draw: response.data.draw,
            recordsTotal: response.data.recordsTotal,
            recordsFiltered: response.data.recordsFiltered,
            data: formattedData
          });

          // Добавляем устройства с sum при первой загрузке
          if (isInitialLoad.value && response.data.draw === 1) {
            const devicesWithSum = response.data.devices
              .filter(device => device.sum != null)
              .map(device => device.ID);

            // Добавляем только новые устройства
            const newDevices = devicesWithSum.filter(id =>
              !selectedDevices.value.includes(id)
            );

            if (newDevices.length > 0) {
              selectedDevices.value = [...selectedDevices.value, ...newDevices];
            }

            isInitialLoad.value = false;
          }
        })
        .catch(error => {
          console.error('Ошибка при загрузке данных:', error);
          callback({
            draw: data.draw,
            recordsTotal: 0,
            recordsFiltered: 0,
            data: []
          });
        });
    },
    columns: [
      { data: 'ID' },
      { data: 'DescrOName' },
      { data: 'Tariff' },
      {
        data: 'PerMonth',
        render: function (data, type, row) {
          if (row.tariff?.PerMonth != null) {
            return renderPrice(row.tariff.PerMonth);
          }
          return '';
        }
      },
      {
        data: 'TariffFinish',
        render: function (data, type, row) {
          if (data !== undefined && data !== null) {
            return renderDate(data);
          }
          return data;
        }
      },
      {
        data: 'addedDays',
        render: function (data, type, row) {
          if (data != 0 && data !== undefined && data !== null) {
            return `<span style="color: var(--bs-success)">+${data}</span>`;
          }
          return data;
        }
      },
      {
        data: 'NewTariffFinish',
        render: function (data, type, row) {
          if (data !== undefined && data !== null) {
            return renderDate(data);
          }
          return data;
        }
      },
      {
        data: 'DevSum',
        render: function (data, type, row) {
          if (data !== undefined && data !== null) {
            return renderPrice(data);
          }
          return '';
        }
      }
    ],
    language: {
      lengthMenu: "Показать _MENU_ записей",
      zeroRecords: "Ничего не найдено",
      info: "Показать записи с _START_ по _END_ из _TOTAL_ записей",
      infoEmpty: "Записи отсутствуют",
      infoFiltered: "(отфильтровано из _MAX_ записей)",
      search: "Поиск:",
      paginate: {
        first: "Первая",
        last: "Последняя",
        next: "Следующая",
        previous: "Предыдущая"
      },
      select: {
        rows: {
          _: ', выбрано строк: %d'
        }
      }
    },
    select: {
      style: 'multi',
      selector: 'td:not(:last-child)'
    },
    createdRow: function (row, data, dataIndex) {
      $(row).attr('data-id', data.ID);
    }
  });

  // Обработчики выделения строк
  dataTable.value.on('select deselect', function (e, dt, type, indexes) {
    updateSelectedDevices();
  });

  // Восстановление выделения после перерисовки
  dataTable.value.on('draw.dt', function () {
    restoreSelection();
  });

  $('.dt-input').addClass('form-control');
};

const createInvoice = async () => {
  try {
    const params = {
      userId: route.params.id,
      payerId: selectedPayer.value,
      period: selectedPeriod.value,
      devices: selectedDevices.value.join(',')
    };
    const response = await axios.post(
      process.env.VUE_APP_API_SERVER_URL + '...',
      params,
      {
        headers: {
          'X-Session-Token': sessionToken.value
        }
      }
    );
    showNotification('Счёт успешно сформирован', 'success');
    emit('invoices-reload');
  } catch (error) {
    console.error('Ошибка создания счёта:', error);
    showNotification('Ошибка создания счёта:', 'danger');
  }
}

// Обновление данных таблицы
const fetchData = () => {
  if (dataTable.value) {
    dataTable.value.ajax.reload(null, false); // Сохраняем текущую позицию
  }
};

// Обработчик выхода
const handleLogout = () => {
  logout();
  router.push('/login');
};

// Хуки жизненного цикла
onMounted(() => {
  initDataTable();
  initPayers();
});

onBeforeUnmount(() => {
  if (dataTable.value) {
    dataTable.value.destroy();
    dataTable.value = null;
  }
});

// Следим за изменением периода и плательщика
watch(selectedPeriod, () => {
  if (selectedPeriod.value !== null && selectedPayer.value !== null && selectedDevices.value !== null) {
    showCreateBtn.value = true;
  } else {
    showCreateBtn.value = false;
  }
  fetchData();
  initPayers();
});

watch(selectedPayer, () => {
  if (selectedPeriod.value !== null && selectedPayer.value !== null && selectedDevices.value !== null) {
    showCreateBtn.value = true;
  } else {
    showCreateBtn.value = false;
  }
  fetchData();
});
</script>

<style scoped>
.create-invoice-container {
  /* max-width: 1200px; */
  margin: 0 auto;
}

.section-card {
  border-radius: 8px;
  overflow: hidden;
}

.section-title {
  font-weight: 600;
  font-size: 1.1rem;
  position: relative;
  padding-bottom: 0.5rem;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 50px;
  height: 2px;
  background-color: #0d6efd;
}

.payers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.2rem;
}

.period-option .form-check-input {
  position: absolute;
  opacity: 0;
}

.period-option .form-check-input:checked + .btn {
  background-color: #0d6efd;
  color: white;
  border-color: #0d6efd;
}

.empty-state {
  background-color: #f8f9fa;
  border-radius: 8px;
}
</style>