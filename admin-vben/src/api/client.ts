import axios from 'axios';
import type { ApiEnvelope } from '@/types';

const client = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api',
  withCredentials: true,
  headers: {
    version: 'v1',
  },
});

client.interceptors.response.use((response) => {
  const payload = response.data as ApiEnvelope<unknown>;
  if (typeof payload?.code !== 'number') {
    return response;
  }

  if (payload.code === 1) {
    return response;
  }

  const error = new Error(payload.msg || '请求失败') as Error & {
    code?: number;
    errorCode?: string;
    payload?: ApiEnvelope<unknown>;
  };
  error.code = payload.code;
  error.errorCode = payload.error_code;
  error.payload = payload;
  throw error;
});

export { client };
