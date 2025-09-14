<template>
    <div v-if="payer">
        <div class="payer-accordion ibox">
            <div class="payer-header d-flex align-items-center border-bottom py-2">
                <div class="radio i-checks d-flex align-items-center">
                    <label class="mb-0">
                        <div class="iradio_square-green">
                            <input 
                                type="radio" 
                                name="payer-radio" 
                                class="payer-radio" 
                                :value="payer.ID"
                                @change="handlePayerSelect(payer.ID)"
                            >
                            <ins class="iCheck-helper"></ins>
                        </div>
                    </label>
                </div>
                <p id="Caption" class="mb-0 mx-3 flex-grow-1">{{ payer.Caption }}</p>
                <div class="ibox-tools">
                    <a class="collapse-link"><i class="bi bi-chevron-down"></i></a>
                </div>
            </div>
            
            <div class="ibox-content" style="display: none;">
                <div class="payer-details p-3">
                    <div class="row align-items-center">
                        <!-- Информация -->
                        <div class="col-md-8">
                            <div class="mb-2">
                                <strong>Адрес: </strong><span id="Address">{{ payer.Address }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>ИНН: </strong><span id="INN">{{ payer.INN }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>КПП: </strong><span id="KPP">{{ payer.KPP }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>Email: </strong><span id="Email">{{ payer.Email }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>Телефон: </strong><span id="Phone">{{ payer.Phone }}</span>
                            </div>
                            <div>
                                <strong>Основание: </strong><span id="Base">{{ payer.Base }}</span>
                            </div>
                        </div>
                        
                        <!-- Кнопки вертикально -->
                        <div class="col-md-4 text-end">
                            <div class="d-grid gap-2">
                                <button 
                                    class="btn btn-sm btn-primary"
                                    @click="handlePayerEdit(payer)"
                                >
                                    <i class="bi bi-pencil"></i> Редактировать
                                </button>
                                <button 
                                    class="btn btn-sm btn-danger"
                                    @click="handlePayerDelete(payer.ID)"
                                >
                                    <i class="bi bi-trash"></i> Удалить
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useSession } from '@/composables/useSession';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import $ from 'jquery';

const route = useRoute();

// Props
const props = defineProps({
    payer: {
        type: Object,
        default: null
    }
});

// Композаблы
const { sessionToken } = useSession();

const emit = defineEmits(['payer-selected', 'set-notification']);

const handlePayerSelect = (payerId) => {
  emit('payer-selected', payerId);
};

const handlePayerEdit = (payer) => {
  emit('payer-edit', payer);
};

const handlePayerDelete = async (id) => {
    try {
        const params = {
            userId : route.params.id,
            ID : id // HINT: Поправить на беке название переменной, пусть будет везде payerId
        }
        const response = await axios.post(
            process.env.VUE_APP_API_SERVER_URL + '...',
            params,
            {
                headers: {
                    'X-Session-Token': sessionToken.value
                }
            }
        );
        console.log('Payer deleted:', response.data);
        emit('set-notification', 'Плательщик успешно удалён', 'success');
        emit('payers-reload');
        // closeForm();
    } catch (error) {
        console.error('Error deleting payer:', error);
        emit('set-notification', 'Ошибка при удалении плательщика', 'danger');
    }
}

// ...

// Хуки жизненного цикла
onMounted(() => {
    initICheck();
    initCollapse();
    // Инициализация при монтировании
});

</script>