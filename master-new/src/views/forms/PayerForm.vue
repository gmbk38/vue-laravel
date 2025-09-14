<template>
    <div v-if="localPayer" class="formElementWrapper d-flex align-items-center justify-content-center" @click.self="closeForm">
        <div class="formElement bg-white rounded-lg shadow-lg p-4" style="max-width: 900px; width: 100%;">
            <h3 class="text-center mb-4 pb-3 border-bottom text-primary">
              {{ localPayer.ID ? 'Редактирование плательщика' : 'Добавление плательщика' }}
            </h3>
            
            <div class="row g-4">
                <!-- Левая колонка -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="Caption" class="form-label fw-medium">Наименование</label>
                        <input class="form-control form-control-lg" 
                               id="Caption" 
                               type="text" 
                               v-model="localPayer.Caption"
                               placeholder="Введите полное название организации">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="Country" class="form-label fw-medium">Страна</label>
                        <input class="form-control form-control-lg" 
                               id="Country" 
                               type="text" 
                               v-model="localPayer.Country"
                               placeholder="Россия, Казахстан и т.д.">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="Address" class="form-label fw-medium">Юридический адрес</label>
                        <input class="form-control form-control-lg" 
                               id="Address" 
                               type="text" 
                               v-model="localPayer.Address"
                               placeholder="Введите юридический адрес">
                    </div>
                </div>
                
                <!-- Правая колонка -->
                <div class="col-md-6">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="INN" class="form-label fw-medium">ИНН</label>
                                <input class="form-control form-control-lg" 
                                       id="INN" 
                                       type="text" 
                                       v-model="localPayer.INN"
                                       placeholder="10 или 12 цифр">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="KPP" class="form-label fw-medium">КПП</label>
                                <input class="form-control form-control-lg" 
                                       id="KPP" 
                                       type="text" 
                                       v-model="localPayer.KPP"
                                       placeholder="9 цифр">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="Phone" class="form-label fw-medium">Телефон</label>
                        <input class="form-control form-control-lg" 
                               id="Phone" 
                               type="tel" 
                               v-model="localPayer.Phone"
                               placeholder="+7 (___) ___-__-__">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="Email" class="form-label fw-medium">Электронная почта</label>
                        <input class="form-control form-control-lg" 
                               id="Email" 
                               type="email" 
                               v-model="localPayer.Email"
                               placeholder="example@company.ru">
                    </div>
                </div>
            </div>
            
            <!-- Дополнительные поля -->
            <div class="row">
              <div class="col-12">
                <div class="form-group mb-3">
                  <label for="Base" class="form-label fw-medium">Основание платежа</label>
                  <input class="form-control form-control-lg" 
                         id="Base" 
                         type="text" 
                         v-model="localPayer.Base"
                         placeholder="Договор, УПД и т.д.">
                </div>
              </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
              <button type="button" 
                      class="btn btn-outline-secondary btn-lg px-4 py-2 fw-medium"
                      @click.prevent="closeForm">
                  Отменить
              </button>
              <button type="button" 
                      class="btn btn-success btn-lg px-5 py-2 fw-medium"
                      @click.prevent="save">
                  {{ localPayer.ID ? 'Сохранить изменения' : 'Добавить плательщика' }}
              </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useSession } from '@/composables/useSession';
import { useRoute } from 'vue-router';
import 'jquery.maskedinput';
import axios from 'axios';
import $ from 'jquery';

const props = defineProps({
    payer: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close', 'payers-reload', 'set-notification']);
const route = useRoute();
const { sessionToken } = useSession();

// Создаем реактивную копию объекта payer
const localPayer = ref(JSON.parse(JSON.stringify(props.payer || {
    ID: null,
    Caption: '',
    Country: '',
    Address: '',
    INN: '',
    KPP: '',
    Phone: '',
    Email: '',
    Base: ''
})));

// Следим за изменениями входящего payer
watch(() => props.payer, (newVal) => {
    if (newVal) {
        localPayer.value = JSON.parse(JSON.stringify(newVal));
    } else {
        // Сброс к пустым значениям если payer null
        localPayer.value = {
            ID: null,
            Caption: '',
            Country: '',
            Address: '',
            INN: '',
            KPP: '',
            Phone: '',
            Email: '',
            Base: ''
        };
    }
}, { immediate: true });

const closeForm = () => {
    emit('close');
    $('body').css('overflow', 'visible');
};

const save = async () => {
    try {
        const params = {
            userId: route.params.id,
            ID: localPayer.value.ID,
            Caption: localPayer.value.Caption,
            Country: localPayer.value.Country,
            INN: localPayer.value.INN,
            KPP: localPayer.value.KPP,
            Address: localPayer.value.Address,
            Email: localPayer.value.Email,
            Phone: localPayer.value.Phone,
            Base: localPayer.value.Base,
        };

        const endpoint = localPayer.value.ID 
            ? '/payer/update' 
            : '/payer/create';

        const response = await axios.post(
            process.env.VUE_APP_API_SERVER_URL + endpoint,
            params,
            {
                headers: {
                    'X-Session-Token': sessionToken.value
                }
            }
        );

        emit('set-notification', 
            `Плательщик успешно ${localPayer.value.ID ? 'обновлён' : 'добавлен'}`,
            'success');
        emit('payers-reload');
        closeForm();
    } catch (error) {
        console.error('Error saving payer data:', error);
        emit('set-notification', 'Ошибка при сохранении данных', 'danger');
    }
};
</script>