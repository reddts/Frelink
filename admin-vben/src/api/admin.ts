import { client } from './client';
import type {
  AdminBootstrapPayload,
  AdminDashboardPayload,
  AdminMenuItem,
  AdminProfile,
  ApiEnvelope,
} from '@/types';

export async function loginAdmin(payload: { username: string; password: string }) {
  const response = await client.post<ApiEnvelope<AdminBootstrapPayload>>('/Admin/login', payload);
  return response.data.data;
}

export async function logoutAdmin() {
  const response = await client.post<ApiEnvelope<{ logged_out: boolean }>>('/Admin/logout');
  return response.data.data;
}

export async function fetchAdminMe() {
  const response = await client.get<ApiEnvelope<{ user: AdminProfile; permissions: string[] }>>('/Admin/me');
  return response.data.data;
}

export async function fetchAdminMenu() {
  const response = await client.get<ApiEnvelope<{ home: { title: string; path: string }; menus: AdminMenuItem[] }>>('/Admin/menu');
  return response.data.data;
}

export async function fetchAdminDashboard() {
  const response = await client.get<ApiEnvelope<AdminDashboardPayload>>('/Admin/dashboard');
  return response.data.data;
}
