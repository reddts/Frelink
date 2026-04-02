import { client } from './client';
import type {
  AdminBootstrapPayload,
  AdminDashboardPayload,
  AdminMenuItem,
  AdminProfile,
  ApiEnvelope,
  SystemAuthDetail,
  SystemAuthListPayload,
  SystemAuthMetaPayload,
  SystemGroupDetail,
  SystemGroupListPayload,
  SystemGroupMetaPayload,
  SystemConfigDetail,
  SystemConfigGroupItem,
  SystemConfigMetaPayload,
  SystemConfigPagePayload,
  SystemConfigOverviewPayload,
  SystemUserDetail,
  SystemUserOverviewPayload,
  SystemMenuListPayload,
  SystemMenuNode,
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

export async function fetchSystemAuths() {
  const response = await client.get<ApiEnvelope<SystemAuthListPayload>>('/SystemAuth/index');
  return response.data.data;
}

export async function fetchSystemAuthDetail(id: number) {
  const response = await client.get<ApiEnvelope<SystemAuthDetail>>('/SystemAuth/detail', {
    params: { id },
  });
  return response.data.data;
}

export async function fetchSystemAuthMeta() {
  const response = await client.get<ApiEnvelope<SystemAuthMetaPayload>>('/SystemAuth/meta');
  return response.data.data;
}

export async function saveSystemAuth(payload: Record<string, unknown>) {
  const response = await client.post<ApiEnvelope<{ id: number }>>('/SystemAuth/save', payload);
  return response.data.data;
}

export async function deleteSystemAuth(id: number) {
  const response = await client.post<ApiEnvelope<null>>('/SystemAuth/delete', { id });
  return response.data.data;
}

export async function toggleSystemAuthState(id: number) {
  const response = await client.post<ApiEnvelope<null>>('/SystemAuth/state', { id });
  return response.data.data;
}

export async function fetchSystemMenus(group = 'nav') {
  const response = await client.get<ApiEnvelope<SystemMenuListPayload>>('/SystemMenu/index', {
    params: { group },
  });
  return response.data.data;
}

export async function fetchSystemMenuDetail(id: number) {
  const response = await client.get<ApiEnvelope<SystemMenuNode>>('/SystemMenu/detail', {
    params: { id },
  });
  return response.data.data;
}

export async function saveSystemMenu(payload: Record<string, unknown>) {
  const response = await client.post<ApiEnvelope<{ id: number }>>('/SystemMenu/save', payload);
  return response.data.data;
}

export async function deleteSystemMenu(id: number) {
  const response = await client.post<ApiEnvelope<null>>('/SystemMenu/delete', { id });
  return response.data.data;
}

export async function toggleSystemMenuState(id: number, field: 'status' | 'is_home') {
  const response = await client.post<ApiEnvelope<null>>('/SystemMenu/state', { id, field });
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

export async function fetchSystemGroupMeta() {
  const response = await client.get<ApiEnvelope<SystemGroupMetaPayload>>('/SystemGroup/createMeta');
  return response.data.data;
}

export async function saveSystemGroup(payload: Record<string, unknown>) {
  const response = await client.post<ApiEnvelope<{ id: number }>>('/SystemGroup/save', payload);
  return response.data.data;
}

export async function deleteSystemGroup(id: number) {
  const response = await client.post<ApiEnvelope<null>>('/SystemGroup/delete', { id });
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

export async function fetchSystemConfigPage(groupId = 0) {
  const response = await client.get<ApiEnvelope<SystemConfigPagePayload>>('/SystemConfig/configPage', {
    params: { group_id: groupId },
  });
  return response.data.data;
}

export async function saveSystemConfigPage(groupId: number, values: Record<string, unknown>) {
  const response = await client.post<ApiEnvelope<null>>('/SystemConfig/configPageSave', {
    group_id: groupId,
    values,
  });
  return response.data.data;
}

export async function fetchSystemUsers(status = 1, keyword = '', forbiddenIp = 0) {
  const response = await client.get<ApiEnvelope<SystemUserOverviewPayload>>('/SystemUser/index', {
    params: { status, keyword, forbidden_ip: forbiddenIp },
  });
  return response.data.data;
}

export async function fetchSystemUserDetail(uid: number) {
  const response = await client.get<ApiEnvelope<SystemUserDetail>>('/SystemUser/detail', {
    params: { uid },
  });
  return response.data.data;
}

export async function saveSystemUser(payload: Record<string, unknown>) {
  const response = await client.post<ApiEnvelope<{ uid: number }>>('/SystemUser/save', payload);
  return response.data.data;
}

export async function createSystemUser(payload: Record<string, unknown>) {
  const response = await client.post<ApiEnvelope<{ uid: number }>>('/SystemUser/create', payload);
  return response.data.data;
}

export async function approveSystemUser(id: number) {
  const response = await client.get<ApiEnvelope<null>>('/SystemUser/approve', {
    params: { id },
  });
  return response.data.data;
}

export async function declineSystemUser(id: number) {
  const response = await client.get<ApiEnvelope<null>>('/SystemUser/decline', {
    params: { id },
  });
  return response.data.data;
}

export async function forbidSystemUser(id: number, forbiddenTime: string, forbiddenReason: string) {
  const response = await client.post<ApiEnvelope<null>>('/SystemUser/forbid', {
    id,
    forbidden_time: forbiddenTime,
    forbidden_reason: forbiddenReason,
  });
  return response.data.data;
}

export async function unForbidSystemUser(id: number) {
  const response = await client.get<ApiEnvelope<null>>('/SystemUser/unForbid', {
    params: { id },
  });
  return response.data.data;
}

export async function toggleSystemUserIp(id: number, relieve = false) {
  const response = await client.get<ApiEnvelope<null>>('/SystemUser/forbiddenIp', {
    params: { id, action: relieve ? 'relieve' : 'forbidden' },
  });
  return response.data.data;
}
