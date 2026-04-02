import { client } from './client';
import type {
  AdminBootstrapPayload,
  AdminDashboardPayload,
  AdminMenuItem,
  AdminProfile,
  ApiEnvelope,
  SystemGroupDetail,
  SystemGroupListPayload,
  SystemConfigDetail,
  SystemConfigGroupItem,
  SystemConfigMetaPayload,
  SystemConfigOverviewPayload,
  SystemMenuListPayload,
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

export async function fetchSystemMenus(group = 'nav') {
  const response = await client.get<ApiEnvelope<SystemMenuListPayload>>('/SystemMenu/index', {
    params: { group },
  });
  return response.data.data;
}

export async function fetchSystemGroups(keyword = '') {
  const response = await client.get<ApiEnvelope<SystemGroupListPayload>>('/SystemGroup/index', {
    params: { keyword },
  });
  return response.data.data;
}

export async function fetchSystemGroupDetail(id: number) {
  const response = await client.get<ApiEnvelope<SystemGroupDetail>>('/SystemGroup/detail', {
    params: { id },
  });
  return response.data.data;
}

export async function fetchSystemConfigs(groupId = 0, keyword = '') {
  const response = await client.get<ApiEnvelope<SystemConfigOverviewPayload>>('/SystemConfig/index', {
    params: { group_id: groupId, keyword },
  });
  return response.data.data;
}

export async function fetchSystemConfigDetail(id: number) {
  const response = await client.get<ApiEnvelope<SystemConfigDetail>>('/SystemConfig/detail', {
    params: { id },
  });
  return response.data.data;
}

export async function fetchSystemConfigMeta() {
  const response = await client.get<ApiEnvelope<SystemConfigMetaPayload>>('/SystemConfig/meta');
  return response.data.data;
}

export async function saveSystemConfig(payload: Record<string, unknown>) {
  const response = await client.post<ApiEnvelope<{ id: number }>>('/SystemConfig/save', payload);
  return response.data.data;
}

export async function deleteSystemConfig(id: number) {
  const response = await client.post<ApiEnvelope<null>>('/SystemConfig/delete', { id });
  return response.data.data;
}

export async function fetchSystemConfigGroupDetail(id: number) {
  const response = await client.get<ApiEnvelope<SystemConfigGroupItem>>('/SystemConfig/groupDetail', {
    params: { id },
  });
  return response.data.data;
}

export async function saveSystemConfigGroup(payload: Record<string, unknown>) {
  const response = await client.post<ApiEnvelope<{ id: number }>>('/SystemConfig/groupSave', payload);
  return response.data.data;
}

export async function deleteSystemConfigGroup(id: number) {
  const response = await client.post<ApiEnvelope<null>>('/SystemConfig/groupDelete', { id });
  return response.data.data;
}
