<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">System / Users</span>
        <h3>用户管理</h3>
        <p>用户列表、编辑和积分发放已迁入 `adminapi + Vue`，并按管理端统一基线组件重排交互结构。</p>
      </div>
      <label class="search-inline">
        <span>搜索</span>
        <Input v-model.trim="keyword" placeholder="用户名 / 昵称 / 邮箱 / 手机" @keydown.enter="reload" />
      </label>
      <Button type="button" @click="resetCreateForm">新增用户</Button>
      <div class="selection-toolbar">
        <span>已选 {{ selectedIds.length }} 个用户</span>
        <Button variant="outline" size="sm" type="button" @click="toggleSelectAll">
          {{ isAllSelected ? '取消全选' : '全选当前列表' }}
        </Button>
        <button
          v-if="currentStatus === 0"
          class="ghost-button"
          type="button"
          :disabled="!selectedIds.length"
          @click="recoverSelected"
        >
          批量恢复
        </button>
        <button
          class="ghost-button danger-button"
          type="button"
          :disabled="!selectedIds.length"
          @click="removeSelected(currentStatus === 0)"
        >
          {{ currentStatus === 0 ? '批量彻底删除' : '批量删除' }}
        </button>
        <button
          v-if="currentStatus === 2"
          class="ghost-button"
          type="button"
          :disabled="!selectedIds.length"
          @click="approveSelected"
        >
          批量通过
        </button>
        <button
          v-if="currentStatus === 2"
          class="ghost-button danger-button"
          type="button"
          :disabled="!selectedIds.length"
          @click="declineSelected"
        >
          批量拒绝
        </button>
        <button
          v-if="currentStatus !== 0 && currentStatus !== 3"
          class="ghost-button danger-button"
          type="button"
          :disabled="!selectedIds.length"
          @click="forbidSelected"
        >
          批量封禁
        </button>
        <button
          v-if="currentStatus === 3"
          class="ghost-button"
          type="button"
          :disabled="!selectedIds.length"
          @click="unForbidSelected"
        >
          批量解封
        </button>
        <button
          v-if="currentForbiddenIp === 0"
          class="ghost-button danger-button"
          type="button"
          :disabled="!selectedIds.length"
          @click="toggleSelectedIp(false)"
        >
          批量封禁IP
        </button>
        <button
          v-if="currentForbiddenIp === 1"
          class="ghost-button"
          type="button"
          :disabled="!selectedIds.length"
          @click="toggleSelectedIp(true)"
        >
          批量解封IP
        </button>
      </div>
    </section>

    <section class="stats-grid">
      <article class="stat-card">
        <span class="eyebrow">Users</span>
        <strong>{{ payload?.list.length || 0 }}</strong>
        <small>当前筛选条件下的用户数量</small>
      </article>
      <article class="stat-card">
        <span class="eyebrow">Editor</span>
        <strong>{{ selectedUid || '-' }}</strong>
        <small>{{ selectedUserLabel }}</small>
      </article>
      <article class="stat-card">
        <span class="eyebrow">Integral</span>
        <strong>{{ integralUid || '-' }}</strong>
        <small>{{ integralUid ? `当前第 ${integralPage} 页积分记录` : '未选择积分记录用户' }}</small>
      </article>
    </section>

    <article class="panel-card">
      <span class="eyebrow">状态筛选</span>
      <div class="tab-row">
        <Button
          v-for="item in payload?.tabs || []"
          :key="`${item.value}-${item.forbidden_ip}`"
          :variant="currentStatus === item.value && currentForbiddenIp === item.forbidden_ip ? 'default' : 'outline'"
          size="sm"
          type="button"
          @click="switchTab(item.value, item.forbidden_ip)"
        >
          {{ item.label }}
        </Button>
      </div>
    </article>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">用户列表</span>
        <div class="config-table user-table">
          <div class="config-table-head">
            <span>选择</span>
            <span>用户</span>
            <span>系统组</span>
            <span>积分 / 威望</span>
            <span>状态</span>
            <span>操作</span>
          </div>

          <div
            v-for="item in payload?.list || []"
            :key="item.uid"
            class="config-table-row"
            :class="{ 'is-current': selectedUid === item.uid }"
          >
            <span>
              <label class="table-check">
                <input v-model="selectedIds" :value="item.uid" type="checkbox" />
                <small>选中</small>
              </label>
            </span>
            <span>
              <strong>{{ item.nick_name || item.user_name }}</strong>
              <small>{{ item.user_name }} / {{ item.email || item.mobile || '无联系方式' }}</small>
            </span>
            <span>
              <strong>{{ item.group_name }}</strong>
              <small>最后登录：{{ item.last_login_time_text }}</small>
            </span>
            <span>
              <strong>{{ item.integral_group_name }}</strong>
              <small>{{ item.reputation_group_name }}</small>
            </span>
            <span>
              <strong>{{ item.status_label }}</strong>
              <small>注册：{{ item.create_time_text }}</small>
            </span>
            <span>
              <div class="config-actions">
                <button class="text-button" type="button" @click="editUser(item.uid)">编辑</button>
                <button v-if="item.actions.includes('integral')" class="text-button" type="button" @click="openIntegral(item.uid)">积分</button>
                <button v-if="item.actions.includes('delete')" class="text-button danger-button" type="button" @click="removeUser(item.uid, false)">删除</button>
                <button v-if="item.actions.includes('recover')" class="text-button" type="button" @click="recoverUser(item.uid)">恢复</button>
                <button v-if="item.actions.includes('remove')" class="text-button danger-button" type="button" @click="removeUser(item.uid, true)">彻底删除</button>
                <button v-if="item.actions.includes('approve')" class="text-button" type="button" @click="approveUser(item.uid)">通过</button>
                <button v-if="item.actions.includes('decline')" class="text-button danger-button" type="button" @click="declineUser(item.uid)">拒绝</button>
                <button v-if="item.actions.includes('forbid')" class="text-button danger-button" type="button" @click="forbidUser(item.uid)">封禁</button>
                <button v-if="item.actions.includes('unforbid')" class="text-button" type="button" @click="unForbidUser(item.uid)">解封</button>
                <button
                  v-if="item.actions.includes('forbid_ip')"
                  class="text-button danger-button"
                  type="button"
                  @click="toggleIp(item.uid, false)"
                >
                  封禁IP
                </button>
                <button
                  v-if="item.actions.includes('lift_ip')"
                  class="text-button"
                  type="button"
                  @click="toggleIp(item.uid, true)"
                >
                  解封IP
                </button>
              </div>
            </span>
          </div>
        </div>
      </article>

      <article class="panel-card">
        <span class="eyebrow">新增用户</span>
        <form class="editor-form" @submit.prevent="submitCreate">
          <label>
            <span>用户名</span>
            <Input v-model.trim="createForm.user_name" placeholder="请输入用户名" />
          </label>
          <label>
            <span>用户昵称</span>
            <Input v-model.trim="createForm.nick_name" placeholder="请输入用户昵称" />
          </label>
          <label>
            <span>登录密码</span>
            <Input v-model="createForm.password" type="password" placeholder="请输入登录密码" />
          </label>
          <label>
            <span>邮箱</span>
            <Input v-model.trim="createForm.email" placeholder="请输入邮箱" />
          </label>
          <label>
            <span>手机</span>
            <Input v-model.trim="createForm.mobile" placeholder="请输入手机号" />
          </label>
          <label>
            <span>头像地址</span>
            <Input v-model.trim="createForm.avatar" placeholder="请输入头像地址" />
          </label>
          <label>
            <span>个人签名</span>
            <Textarea v-model="createForm.signature" rows="3" />
          </label>
          <label>
            <span>系统组</span>
            <select v-model.number="createForm.group_id">
              <option v-for="item in meta?.group_options || []" :key="item.value" :value="Number(item.value)">
                {{ item.label }}
              </option>
            </select>
          </label>
          <label>
            <span>威望组</span>
            <select v-model.number="createForm.reputation_group_id">
              <option v-for="item in meta?.reputation_group_options || []" :key="item.value" :value="Number(item.value)">
                {{ item.label }}
              </option>
            </select>
          </label>
          <label>
            <span>积分组</span>
            <select v-model.number="createForm.integral_group_id">
              <option v-for="item in meta?.integral_group_options || []" :key="item.value" :value="Number(item.value)">
                {{ item.label }}
              </option>
            </select>
          </label>
          <label>
            <span>状态</span>
            <select v-model.number="createForm.status">
              <option v-for="item in meta?.status_options || []" :key="item.value" :value="Number(item.value)">
                {{ item.label }}
              </option>
            </select>
          </label>
          <div class="form-actions">
            <Button type="submit" :disabled="creating">
              {{ creating ? '创建中...' : '创建用户' }}
            </Button>
            <Button variant="outline" type="button" @click="resetCreateForm">重置</Button>
          </div>
        </form>
      </article>

      <article class="panel-card">
        <span class="eyebrow">用户编辑器</span>
        <form class="editor-form" @submit.prevent="submitUser">
          <div v-if="selectedUid" class="inline-links">
            <button class="text-button" type="button" @click="openIntegral(selectedUid)">查看积分记录</button>
          </div>
          <label>
            <span>用户昵称</span>
            <Input v-model.trim="userForm.nick_name" placeholder="请输入用户昵称" />
          </label>
          <label>
            <span>邮箱</span>
            <Input v-model.trim="userForm.email" placeholder="请输入邮箱" />
          </label>
          <label>
            <span>手机</span>
            <Input v-model.trim="userForm.mobile" placeholder="请输入手机号" />
          </label>
          <label>
            <span>头像地址</span>
            <Input v-model.trim="userForm.avatar" placeholder="请输入头像地址" />
          </label>
          <label>
            <span>个人签名</span>
            <Textarea v-model="userForm.signature" rows="4" placeholder="请输入个人签名" />
          </label>
          <label>
            <span>系统组</span>
            <select v-model.number="userForm.group_id">
              <option v-for="item in meta?.group_options || []" :key="item.value" :value="Number(item.value)">
                {{ item.label }}
              </option>
            </select>
          </label>
          <label>
            <span>威望组</span>
            <select v-model.number="userForm.reputation_group_id">
              <option v-for="item in meta?.reputation_group_options || []" :key="item.value" :value="Number(item.value)">
                {{ item.label }}
              </option>
            </select>
          </label>
          <label>
            <span>积分组</span>
            <select v-model.number="userForm.integral_group_id">
              <option v-for="item in meta?.integral_group_options || []" :key="item.value" :value="Number(item.value)">
                {{ item.label }}
              </option>
            </select>
          </label>
          <label>
            <span>认证类型</span>
            <select v-model="userForm.verified">
              <option v-for="item in meta?.verified_options || []" :key="item.value" :value="String(item.value)">
                {{ item.label }}
              </option>
            </select>
          </label>
          <label>
            <span>性别</span>
            <select v-model.number="userForm.sex">
              <option v-for="item in meta?.sex_options || []" :key="item.value" :value="Number(item.value)">
                {{ item.label }}
              </option>
            </select>
          </label>
          <label>
            <span>生日</span>
            <Input v-model="userForm.birthday" type="date" />
          </label>
          <label>
            <span>状态</span>
            <select v-model.number="userForm.status">
              <option v-for="item in meta?.status_options || []" :key="item.value" :value="Number(item.value)">
                {{ item.label }}
              </option>
            </select>
          </label>
          <label>
            <span>新登录密码</span>
            <Input v-model="userForm.password" type="password" placeholder="留空表示不修改" />
          </label>
          <label>
            <span>新交易密码</span>
            <Input v-model="userForm.deal_password" type="password" placeholder="留空表示不修改" />
          </label>
          <div class="form-actions">
            <Button type="submit" :disabled="saving">
              {{ saving ? '保存中...' : selectedUid ? '保存用户' : '请选择用户' }}
            </Button>
          </div>
        </form>
      </article>

      <article class="panel-card">
        <span class="eyebrow">积分记录</span>
        <template v-if="integralUid">
          <div class="toolbar-row">
            <label class="search-inline">
              <span>当前用户</span>
              <Input :value="integralUid" disabled />
            </label>
            <label class="search-inline">
              <span>积分增减</span>
              <Input v-model.number="integralAmount" type="number" placeholder="正数增加，负数扣减" />
            </label>
            <Button type="button" :disabled="awarding || integralAmount === 0" @click="submitIntegralAward">
              {{ awarding ? '提交中...' : '发放积分' }}
            </Button>
          </div>
          <div class="config-table content-table integral-table">
            <div class="config-table-head integral-table-head">
              <span>时间</span>
              <span>动作</span>
              <span>变动</span>
              <span>余额</span>
              <span>备注</span>
            </div>
            <div
              v-for="(item, index) in integralLogs.list"
              :key="`${item.action_type}-${item.record_id}-${index}`"
              class="config-table-row integral-table-row"
            >
              <span><strong>{{ item.create_time_text }}</strong></span>
              <span><strong>{{ item.action_type }}</strong><small>{{ item.record_db || '-' }} / #{{ item.record_id }}</small></span>
              <span><strong>{{ item.integral }}</strong></span>
              <span><strong>{{ item.balance }}</strong></span>
              <span><small>{{ item.remark || '-' }}</small></span>
            </div>
          </div>
          <div class="form-actions">
            <Button variant="outline" type="button" :disabled="integralPage <= 1 || integralLoading" @click="changeIntegralPage(-1)">
              上一页
            </Button>
            <Button variant="outline" type="button" :disabled="integralLogs.list.length < integralLogs.pagination.per_page || integralLoading" @click="changeIntegralPage(1)">
              下一页
            </Button>
          </div>
        </template>
        <p v-else>请选择用户后查看积分记录并执行积分发放。</p>
      </article>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import {
  approveSystemUser,
  awardSystemUserIntegral,
  createSystemUser,
  declineSystemUser,
  fetchSystemUserDetail,
  fetchSystemUserIntegralLogs,
  fetchSystemUsers,
  forbidSystemUser,
  recoverSystemUser,
  removeSystemUser,
  saveSystemUser,
  toggleSystemUserIp,
  unForbidSystemUser,
} from '@/api/admin';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import type { SystemUserIntegralLogPayload, SystemUserOverviewPayload } from '@/types';

const payload = ref<SystemUserOverviewPayload | null>(null);
const keyword = ref('');
const currentStatus = ref(1);
const currentForbiddenIp = ref(0);
const selectedUid = ref(0);
const selectedIds = ref<number[]>([]);
const saving = ref(false);
const creating = ref(false);
const awarding = ref(false);
const integralLoading = ref(false);
const integralUid = ref(0);
const integralPage = ref(1);
const integralAmount = ref(0);
const integralLogs = ref<SystemUserIntegralLogPayload>({
  uid: 0,
  list: [],
  pagination: { page: 1, per_page: 10 },
  page_html: '',
});

const userForm = ref({
  uid: 0,
  nick_name: '',
  email: '',
  mobile: '',
  avatar: '',
  signature: '',
  group_id: 0,
  reputation_group_id: 0,
  integral_group_id: 0,
  verified: 'normal',
  sex: 0,
  birthday: '',
  status: 1,
  password: '',
  deal_password: '',
});

const createForm = ref({
  user_name: '',
  nick_name: '',
  password: '',
  email: '',
  mobile: '',
  avatar: '',
  signature: '',
  group_id: 4,
  reputation_group_id: 1,
  integral_group_id: 1,
  status: 1,
});

const meta = computed(() => payload.value?.meta ?? null);
const isAllSelected = computed(() => {
  const total = payload.value?.list.length ?? 0;
  return total > 0 && selectedIds.value.length === total;
});

const selectedUserLabel = computed(() => {
  if (!selectedUid.value) {
    return '当前未选中编辑用户';
  }
  const current = payload.value?.list.find((item) => item.uid === selectedUid.value);
  if (!current) {
    return '当前用户不在列表中';
  }
  return `${current.nick_name || current.user_name} / ${current.group_name}`;
});

function getErrorMessage(error: unknown) {
  return error instanceof Error ? error.message : '请求失败';
}

async function reload() {
  payload.value = await fetchSystemUsers(currentStatus.value, keyword.value, currentForbiddenIp.value);
  const validIds = new Set((payload.value?.list || []).map((item) => item.uid));
  selectedIds.value = selectedIds.value.filter((id) => validIds.has(id));
  if (selectedUid.value && !validIds.has(selectedUid.value)) {
    selectedUid.value = 0;
  }
  if (!selectedUid.value && (payload.value?.list.length || 0) > 0) {
    await editUser(payload.value!.list[0].uid);
  }
}

async function switchTab(status: number, forbiddenIp: number) {
  currentStatus.value = status;
  currentForbiddenIp.value = forbiddenIp;
  selectedUid.value = 0;
  selectedIds.value = [];
  await reload();
}

function toggleSelectAll() {
  const list = payload.value?.list || [];
  if (isAllSelected.value) {
    selectedIds.value = [];
    return;
  }
  selectedIds.value = list.map((item) => item.uid);
}

async function editUser(uid: number) {
  const detail = await fetchSystemUserDetail(uid);
  selectedUid.value = uid;
  userForm.value = {
    uid: detail.uid,
    nick_name: detail.nick_name || '',
    email: detail.email || '',
    mobile: detail.mobile || '',
    avatar: detail.avatar || '',
    signature: detail.signature || '',
    group_id: detail.group_id,
    reputation_group_id: detail.reputation_group_id,
    integral_group_id: detail.integral_group_id,
    verified: detail.verified || 'normal',
    sex: detail.sex,
    birthday: detail.birthday_text || '',
    status: detail.status,
    password: '',
    deal_password: '',
  };
  await loadIntegralLogs(uid, 1);
}

function resetCreateForm() {
  createForm.value = {
    user_name: '',
    nick_name: '',
    password: '',
    email: '',
    mobile: '',
    avatar: '',
    signature: '',
    group_id: 4,
    reputation_group_id: 1,
    integral_group_id: 1,
    status: 1,
  };
}

async function submitCreate() {
  creating.value = true;
  try {
    const result = await createSystemUser(createForm.value);
    resetCreateForm();
    await reload();
    await editUser(Number(result.uid || 0));
  } catch (error) {
    window.alert(getErrorMessage(error));
  } finally {
    creating.value = false;
  }
}

async function submitUser() {
  if (!selectedUid.value) {
    return;
  }

  saving.value = true;
  try {
    await saveSystemUser(userForm.value);
    await reload();
    await editUser(selectedUid.value);
  } catch (error) {
    window.alert(getErrorMessage(error));
  } finally {
    saving.value = false;
  }
}

async function approveUser(uid: number) {
  try {
    await approveSystemUser(uid);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function approveSelected() {
  if (!selectedIds.value.length) {
    return;
  }
  if (!window.confirm(`确认通过选中的 ${selectedIds.value.length} 个用户？`)) {
    return;
  }
  try {
    await approveSystemUser(selectedIds.value);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function declineUser(uid: number) {
  if (!window.confirm('确认拒绝审核该用户？')) {
    return;
  }
  try {
    await declineSystemUser(uid);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function declineSelected() {
  if (!selectedIds.value.length) {
    return;
  }
  if (!window.confirm(`确认拒绝选中的 ${selectedIds.value.length} 个用户？`)) {
    return;
  }
  try {
    await declineSystemUser(selectedIds.value);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function forbidUser(uid: number) {
  const forbiddenReason = window.prompt('请输入封禁原因');
  if (!forbiddenReason) {
    return;
  }
  const forbiddenTime = window.prompt('请输入解封时间，格式：2026-04-30 23:59', '');
  if (!forbiddenTime) {
    return;
  }
  try {
    await forbidSystemUser(uid, forbiddenTime, forbiddenReason);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function forbidSelected() {
  if (!selectedIds.value.length) {
    return;
  }
  const forbiddenReason = window.prompt('请输入批量封禁原因');
  if (!forbiddenReason) {
    return;
  }
  const forbiddenTime = window.prompt('请输入解封时间，格式：2026-04-30 23:59', '');
  if (!forbiddenTime) {
    return;
  }
  try {
    await forbidSystemUser(selectedIds.value, forbiddenTime, forbiddenReason);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function unForbidUser(uid: number) {
  try {
    await unForbidSystemUser(uid);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function unForbidSelected() {
  if (!selectedIds.value.length) {
    return;
  }
  try {
    await unForbidSystemUser(selectedIds.value);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function toggleIp(uid: number, relieve: boolean) {
  const message = relieve ? '确认解封该用户的登录 IP？' : '确认封禁该用户的登录 IP？';
  if (!window.confirm(message)) {
    return;
  }
  try {
    await toggleSystemUserIp(uid, relieve);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function toggleSelectedIp(relieve: boolean) {
  if (!selectedIds.value.length) {
    return;
  }
  const message = relieve ? `确认解封选中的 ${selectedIds.value.length} 个用户 IP？` : `确认封禁选中的 ${selectedIds.value.length} 个用户 IP？`;
  if (!window.confirm(message)) {
    return;
  }
  try {
    await toggleSystemUserIp(selectedIds.value, relieve);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function recoverUser(uid: number) {
  if (!window.confirm('确认恢复该用户？')) {
    return;
  }
  try {
    await recoverSystemUser(uid);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function recoverSelected() {
  if (!selectedIds.value.length) {
    return;
  }
  if (!window.confirm(`确认恢复选中的 ${selectedIds.value.length} 个用户？`)) {
    return;
  }
  try {
    await recoverSystemUser(selectedIds.value);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function removeUser(uid: number, real: boolean) {
  const message = real ? '确认彻底删除该用户？' : '确认删除该用户？';
  if (!window.confirm(message)) {
    return;
  }
  try {
    await removeSystemUser(uid, real);
    if (selectedUid.value === uid) {
      selectedUid.value = 0;
      integralUid.value = 0;
    }
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function removeSelected(real: boolean) {
  if (!selectedIds.value.length) {
    return;
  }
  const message = real
    ? `确认彻底删除选中的 ${selectedIds.value.length} 个用户？`
    : `确认删除选中的 ${selectedIds.value.length} 个用户？`;
  if (!window.confirm(message)) {
    return;
  }
  try {
    await removeSystemUser(selectedIds.value, real);
    if (selectedUid.value && selectedIds.value.includes(selectedUid.value)) {
      selectedUid.value = 0;
      integralUid.value = 0;
    }
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function loadIntegralLogs(uid: number, page = 1) {
  if (!uid) {
    integralUid.value = 0;
    integralLogs.value = { uid: 0, list: [], pagination: { page: 1, per_page: 10 }, page_html: '' };
    return;
  }
  integralLoading.value = true;
  try {
    const data = await fetchSystemUserIntegralLogs(uid, page);
    integralUid.value = uid;
    integralPage.value = data.pagination.page;
    integralLogs.value = data;
  } catch (error) {
    window.alert(getErrorMessage(error));
  } finally {
    integralLoading.value = false;
  }
}

async function openIntegral(uid: number) {
  await loadIntegralLogs(uid, 1);
}

async function changeIntegralPage(offset: number) {
  if (!integralUid.value) {
    return;
  }
  const nextPage = Math.max(1, integralPage.value + offset);
  await loadIntegralLogs(integralUid.value, nextPage);
}

async function submitIntegralAward() {
  if (!integralUid.value || integralAmount.value === 0) {
    return;
  }
  awarding.value = true;
  try {
    await awardSystemUserIntegral(integralUid.value, integralAmount.value);
    integralAmount.value = 0;
    await loadIntegralLogs(integralUid.value, 1);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  } finally {
    awarding.value = false;
  }
}

onMounted(async () => {
  await reload();
  resetCreateForm();
});
</script>
