import { client } from './client';
import type {
  AdminBootstrapPayload,
  AdminDashboardPayload,
  AdminMenuItem,
  AdminProfile,
  ApiEnvelope,
  ContentAnswerDetail,
  ContentAnswerOverviewPayload,
  ContentApprovalDetail,
  ContentApprovalOverviewPayload,
  ContentArticleDetail,
  ContentArticleOverviewPayload,
  ContentAnnounceDetail,
  ContentAnnounceOverviewPayload,
  ContentCategoryDetail,
  ContentCategoryOverviewPayload,
  ContentQuestionDetail,
  ContentQuestionOverviewPayload,
  ContentTopicDetail,
  ContentTopicOverviewPayload,
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
  SystemForbiddenIpOverviewPayload,
  SystemVerifyDetail,
  SystemVerifyOverviewPayload,
  SystemUserDetail,
  SystemUserIntegralLogPayload,
  SystemUserOverviewPayload,
  SystemMenuListPayload,
  SystemMenuNode,
} from '@/types';

type IdInput = number | number[];

function serializeIds(ids: IdInput) {
  return Array.isArray(ids) ? ids.join(',') : ids;
}

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

export async function approveSystemUser(id: IdInput) {
  const response = await client.get<ApiEnvelope<null>>('/SystemUser/approve', {
    params: { id: serializeIds(id) },
  });
  return response.data.data;
}

export async function declineSystemUser(id: IdInput) {
  const response = await client.get<ApiEnvelope<null>>('/SystemUser/decline', {
    params: { id: serializeIds(id) },
  });
  return response.data.data;
}

export async function forbidSystemUser(id: IdInput, forbiddenTime: string, forbiddenReason: string) {
  const response = await client.post<ApiEnvelope<null>>('/SystemUser/forbid', {
    id: serializeIds(id),
    forbidden_time: forbiddenTime,
    forbidden_reason: forbiddenReason,
  });
  return response.data.data;
}

export async function unForbidSystemUser(id: IdInput) {
  const response = await client.get<ApiEnvelope<null>>('/SystemUser/unForbid', {
    params: { id: serializeIds(id) },
  });
  return response.data.data;
}

export async function toggleSystemUserIp(id: IdInput, relieve = false) {
  const response = await client.get<ApiEnvelope<null>>('/SystemUser/forbiddenIp', {
    params: { id: serializeIds(id), action: relieve ? 'relieve' : 'forbidden' },
  });
  return response.data.data;
}

export async function recoverSystemUser(id: IdInput) {
  const response = await client.get<ApiEnvelope<null>>('/SystemUser/recover', {
    params: { id: serializeIds(id) },
  });
  return response.data.data;
}

export async function removeSystemUser(id: IdInput, real = false) {
  const response = await client.get<ApiEnvelope<null>>('/SystemUser/remove', {
    params: { id: serializeIds(id), real: real ? 1 : 0 },
  });
  return response.data.data;
}

export async function fetchSystemUserIntegralLogs(uid: number, page = 1) {
  const response = await client.get<ApiEnvelope<SystemUserIntegralLogPayload>>('/SystemUser/integralLogs', {
    params: { uid, page },
  });
  return response.data.data;
}

export async function awardSystemUserIntegral(uid: number, integral: number) {
  const response = await client.post<ApiEnvelope<null>>('/SystemUser/integralAward', {
    uid,
    integral,
  });
  return response.data.data;
}

export async function fetchSystemVerifies(status = 1, type = '') {
  const response = await client.get<ApiEnvelope<SystemVerifyOverviewPayload>>('/SystemVerify/index', {
    params: { status, type },
  });
  return response.data.data;
}

export async function fetchSystemVerifyDetail(id: number) {
  const response = await client.get<ApiEnvelope<SystemVerifyDetail>>('/SystemVerify/detail', {
    params: { id },
  });
  return response.data.data;
}

export async function approveSystemVerify(id: IdInput) {
  const response = await client.get<ApiEnvelope<null>>('/SystemVerify/approve', {
    params: { id: serializeIds(id) },
  });
  return response.data.data;
}

export async function declineSystemVerify(id: IdInput, reason: string) {
  const response = await client.post<ApiEnvelope<null>>('/SystemVerify/decline', {
    id: serializeIds(id),
    reason,
  });
  return response.data.data;
}

export async function fetchSystemForbiddenIps(ip = '') {
  const response = await client.get<ApiEnvelope<SystemForbiddenIpOverviewPayload>>('/SystemForbiddenIp/index', {
    params: { ip },
  });
  return response.data.data;
}

export async function addSystemForbiddenIp(ip: string) {
  const response = await client.post<ApiEnvelope<null>>('/SystemForbiddenIp/add', { ip });
  return response.data.data;
}

export async function removeSystemForbiddenIp(id: IdInput) {
  const response = await client.get<ApiEnvelope<null>>('/SystemForbiddenIp/remove', {
    params: { id: serializeIds(id) },
  });
  return response.data.data;
}

export async function fetchContentArticles(status = 1, keyword = '') {
  const response = await client.get<ApiEnvelope<ContentArticleOverviewPayload>>('/ContentArticle/index', {
    params: { status, keyword },
  });
  return response.data.data;
}

export async function fetchContentArticleDetail(id: number) {
  const response = await client.get<ApiEnvelope<ContentArticleDetail>>('/ContentArticle/detail', {
    params: { id },
  });
  return response.data.data;
}

export async function saveContentArticleSeo(payload: Record<string, unknown>) {
  const response = await client.post<ApiEnvelope<{ id: number }>>('/ContentArticle/saveSeo', payload);
  return response.data.data;
}

export async function saveContentArticle(payload: Record<string, unknown>) {
  const response = await client.post<ApiEnvelope<{ id: number }>>('/ContentArticle/save', payload);
  return response.data.data;
}

export async function deleteContentArticle(id: IdInput) {
  const response = await client.get<ApiEnvelope<null>>('/ContentArticle/delete', {
    params: { id: serializeIds(id) },
  });
  return response.data.data;
}

export async function manageContentArticle(id: IdInput, type: 'recover' | 'remove') {
  const response = await client.get<ApiEnvelope<null>>('/ContentArticle/manager', {
    params: { id: serializeIds(id), type },
  });
  return response.data.data;
}

export async function fetchContentQuestions(status = 1, keyword = '') {
  const response = await client.get<ApiEnvelope<ContentQuestionOverviewPayload>>('/ContentQuestion/index', {
    params: { status, keyword },
  });
  return response.data.data;
}

export async function fetchContentQuestionDetail(id: number) {
  const response = await client.get<ApiEnvelope<ContentQuestionDetail>>('/ContentQuestion/detail', {
    params: { id },
  });
  return response.data.data;
}

export async function saveContentQuestionSeo(payload: Record<string, unknown>) {
  const response = await client.post<ApiEnvelope<{ id: number }>>('/ContentQuestion/saveSeo', payload);
  return response.data.data;
}

export async function saveContentQuestion(payload: Record<string, unknown>) {
  const response = await client.post<ApiEnvelope<{ id: number }>>('/ContentQuestion/save', payload);
  return response.data.data;
}

export async function deleteContentQuestion(id: IdInput) {
  const response = await client.get<ApiEnvelope<null>>('/ContentQuestion/delete', {
    params: { id: serializeIds(id) },
  });
  return response.data.data;
}

export async function manageContentQuestion(id: IdInput, type: 'recover' | 'remove') {
  const response = await client.get<ApiEnvelope<null>>('/ContentQuestion/manager', {
    params: { id: serializeIds(id), type },
  });
  return response.data.data;
}

export async function fetchContentAnswers(status = 1, keyword = '') {
  const response = await client.get<ApiEnvelope<ContentAnswerOverviewPayload>>('/ContentAnswer/index', {
    params: { status, keyword },
  });
  return response.data.data;
}

export async function fetchContentAnswerDetail(id: number) {
  const response = await client.get<ApiEnvelope<ContentAnswerDetail>>('/ContentAnswer/detail', {
    params: { id },
  });
  return response.data.data;
}

export async function saveContentAnswer(payload: Record<string, unknown>) {
  const response = await client.post<ApiEnvelope<{ id: number }>>('/ContentAnswer/save', payload);
  return response.data.data;
}

export async function deleteContentAnswer(id: IdInput, real = false) {
  const response = await client.get<ApiEnvelope<null>>('/ContentAnswer/delete', {
    params: { id: serializeIds(id), real: real ? 1 : 0 },
  });
  return response.data.data;
}

export async function fetchContentApprovals(status = 0, type = '', isAgent = '', keyword = '') {
  const response = await client.get<ApiEnvelope<ContentApprovalOverviewPayload>>('/ContentApproval/index', {
    params: { status, type, is_agent: isAgent, keyword },
  });
  return response.data.data;
}

export async function fetchContentTopics(rootOnly = 0, keyword = '') {
  const response = await client.get<ApiEnvelope<ContentTopicOverviewPayload>>('/ContentTopic/index', {
    params: { root_only: rootOnly, keyword },
  });
  return response.data.data;
}

export async function fetchContentTopicDetail(id: number) {
  const response = await client.get<ApiEnvelope<ContentTopicDetail>>('/ContentTopic/detail', {
    params: { id },
  });
  return response.data.data;
}

export async function saveContentTopic(payload: Record<string, unknown>) {
  const response = await client.post<ApiEnvelope<{ id: number }>>('/ContentTopic/save', payload);
  return response.data.data;
}

export async function deleteContentTopic(id: IdInput) {
  const response = await client.get<ApiEnvelope<null>>('/ContentTopic/delete', {
    params: { id: serializeIds(id) },
  });
  return response.data.data;
}

export async function fetchContentCategories(type = '', keyword = '') {
  const response = await client.get<ApiEnvelope<ContentCategoryOverviewPayload>>('/ContentCategory/index', {
    params: { type, keyword },
  });
  return response.data.data;
}

export async function fetchContentCategoryDetail(id: number) {
  const response = await client.get<ApiEnvelope<ContentCategoryDetail>>('/ContentCategory/detail', {
    params: { id },
  });
  return response.data.data;
}

export async function saveContentCategory(payload: Record<string, unknown>) {
  const response = await client.post<ApiEnvelope<{ id: number }>>('/ContentCategory/save', payload);
  return response.data.data;
}

export async function deleteContentCategory(id: IdInput) {
  const response = await client.get<ApiEnvelope<null>>('/ContentCategory/delete', {
    params: { id: serializeIds(id) },
  });
  return response.data.data;
}

export async function fetchContentAnnounces(status = -1, keyword = '') {
  const response = await client.get<ApiEnvelope<ContentAnnounceOverviewPayload>>('/ContentAnnounce/index', {
    params: { status, keyword },
  });
  return response.data.data;
}

export async function fetchContentAnnounceDetail(id: number) {
  const response = await client.get<ApiEnvelope<ContentAnnounceDetail>>('/ContentAnnounce/detail', {
    params: { id },
  });
  return response.data.data;
}

export async function saveContentAnnounce(payload: Record<string, unknown>) {
  const response = await client.post<ApiEnvelope<{ id: number }>>('/ContentAnnounce/save', payload);
  return response.data.data;
}

export async function deleteContentAnnounce(id: IdInput) {
  const response = await client.get<ApiEnvelope<null>>('/ContentAnnounce/delete', {
    params: { id: serializeIds(id) },
  });
  return response.data.data;
}

export async function fetchContentApprovalDetail(id: number) {
  const response = await client.get<ApiEnvelope<ContentApprovalDetail>>('/ContentApproval/detail', {
    params: { id },
  });
  return response.data.data;
}

export async function approveContentApproval(id: IdInput) {
  const response = await client.get<ApiEnvelope<null>>('/ContentApproval/approve', {
    params: { id: serializeIds(id) },
  });
  return response.data.data;
}

export async function declineContentApproval(id: IdInput, reason: string) {
  const response = await client.post<ApiEnvelope<null>>('/ContentApproval/decline', {
    id: serializeIds(id),
    reason,
  });
  return response.data.data;
}

export async function deleteContentApproval(id: IdInput) {
  const response = await client.get<ApiEnvelope<null>>('/ContentApproval/delete', {
    params: { id: serializeIds(id) },
  });
  return response.data.data;
}

export async function forbidContentApprovalUser(uid: IdInput, forbiddenTime: string, forbiddenReason: string) {
  const response = await client.post<ApiEnvelope<null>>('/ContentApproval/forbid', {
    uid: serializeIds(uid),
    forbidden_time: forbiddenTime,
    forbidden_reason: forbiddenReason,
  });
  return response.data.data;
}

export async function forbidContentApprovalUserIp(uid: IdInput) {
  const response = await client.get<ApiEnvelope<null>>('/ContentApproval/forbiddenIp', {
    params: { uid: serializeIds(uid) },
  });
  return response.data.data;
}
