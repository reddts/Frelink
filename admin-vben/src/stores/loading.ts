import { computed, ref } from 'vue';
import { defineStore } from 'pinia';

export const useLoadingStore = defineStore('admin-loading', () => {
  const requestCount = ref(0);
  const message = ref('页面加载中...');

  const visible = computed(() => requestCount.value > 0);

  function start(nextMessage = '页面加载中...') {
    message.value = nextMessage;
    requestCount.value += 1;
  }

  function finish() {
    requestCount.value = Math.max(0, requestCount.value - 1);
  }

  function reset() {
    requestCount.value = 0;
    message.value = '页面加载中...';
  }

  return {
    visible,
    message,
    start,
    finish,
    reset,
  };
});
