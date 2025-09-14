<template>
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div
      v-if="showToast"
      ref="toastElement"
      class="toast show"
      :class="toastClass"
      role="alert"
      aria-live="assertive"
      aria-atomic="true"
    >
      <div class="toast-header">
        <strong class="me-auto">{{ toastTitle }}</strong>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="toast"
          aria-label="Close"
          @click="hideToast"
        ></button>
      </div>
      <div class="toast-body">
        {{ message }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed, onMounted, nextTick } from 'vue'
import { Toast } from 'bootstrap'
import $ from 'jquery'

const props = defineProps({
  message: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['info', 'success', 'warning', 'danger'].includes(value)
  }
})

const showToast = ref(false)
const toastElement = ref(null)
let toastInstance = null
let timeoutId = null

const toastClass = computed(() => {
  return {
    'info': 'bg-info text-white',
    'success': 'bg-success text-white',
    'warning': 'bg-warning text-dark',
    'danger': 'bg-danger text-white'
  }[props.type]
})

const toastTitle = computed(() => {
  return {
    'info': 'Информация',
    'success': 'Успех',
    'warning': 'Предупреждение',
    'danger': 'Ошибка'
  }[props.type]
})

const show = () => {
  showToast.value = true
  
  nextTick(() => {
    if (toastElement.value && !toastInstance) {
      toastInstance = new Toast(toastElement.value)
    }
    
    if (toastInstance) {
      // Сброс стилей перед показом
      $(toastElement.value).css('opacity', 1)
      toastInstance.show()
    }
    
    clearTimeout(timeoutId)
    timeoutId = setTimeout(hideToast, 3000)
  })
}

const hideToast = () => {
  if (toastElement.value) {
    // Анимация fadeOut перед скрытием
    $(toastElement.value).animate({ opacity: 0 }, 1500, () => {
      if (toastInstance) {
        toastInstance.hide()
      }
      nextTick();
      showToast.value = false
    })
  } else {
    if (toastInstance) {
      toastInstance.hide()
    }
    nextTick();
    showToast.value = false
  }
}

watch(() => [props.message, props.type], () => {
  if (props.message) {
    show()
  }
})

onMounted(() => {
  if (props.message) {
    show()
  }
})
</script>

<style scoped>
.toast-container {
  z-index: 1100;
}
.toast {
  transition: opacity 0.5s ease;
}
</style>