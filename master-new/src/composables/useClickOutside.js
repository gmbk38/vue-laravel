import { onMounted, onUnmounted } from 'vue';

export function useClickOutside(elementRef, callback) {
  const handler = (event) => {
    if (
      elementRef.value && 
      !elementRef.value.contains(event.target) &&
      !event.target.closest('.funnel-icon') // Игнорируем клики по иконке
    ) {
      callback();
    }
  };

  onMounted(() => document.addEventListener('click', handler));
  onUnmounted(() => document.removeEventListener('click', handler));
}

// ...