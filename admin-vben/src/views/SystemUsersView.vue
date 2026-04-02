<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">System / Users</span>
        <h3>用户管理</h3>
        <p>用户列表和单用户编辑已经开始从旧后台 `member/Users.php` 迁入 `adminapi + Vue`。</p>
      </div>
      <label class="search-inline">
        <span>搜索</span>
        <input v-model.trim="keyword" placeholder="用户名 / 昵称 / 邮箱 / 手机" @keydown.enter="reload" />
      </label>
    </section>

    <article class="panel-card">
      <span class="eyebrow">状态筛选</span>
      <div class="tab-row">
        <button
          v-for="item in payload?.tabs || []"
          :key="`${item.value}-${item.forbidden_ip}`"
          class="ghost-button"
          :class="{ 'is-current': currentStatus === item.value && currentForbiddenIp === item.forbidden_ip }"
          type="button"
          @click="switchTab(item.value, item.forbidden_ip)"
        >
          {{ item.label }}
        </button>
      </div>
    </article>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">用户列表</span>
        <div class="config-table user-table">
          <div class="config-table-head">
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
              <button class="text-button" type="button" @click="editUser(item.uid)">编辑</button>
            </span>
          </div>
        </div>
      </article>

      <article class="panel-card">
        <span class="eyebrow">用户编辑器</span>
        <form class="editor-form" @submit.prevent="submitUser">
          <label>
            <span>用户昵称</span>
            <input v-model.trim="userForm.nick_name" placeholder="请输入用户昵称" />
          </label>
          <label>
            <span>邮箱</span>
            <input v-model.trim="userForm.email" placeholder="请输入邮箱" />
          </label>
          <label>
            <span>手机</span>
            <input v-model.trim="userForm.mobile" placeholder="请输入手机号" />
          </label>
          <label>
            <span>头像地址</span>
            <input v-model.trim="userForm.avatar" placeholder="请输入头像地址" />
          </label>
          <label>
            <span>个人签名</span>
            <textarea v-model="userForm.signature" rows="4" placeholder="请输入个人签名" />
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
            <input v-model="userForm.birthday" type="date" />
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
            <input v-model="userForm.password" type="password" placeholder="留空表示不修改" />
          </label>
          <label>
            <span>新交易密码</span>
            <input v-model="userForm.deal_password" type="password" placeholder="留空表示不修改" />
          </label>
          <div class="form-actions">
            <button class="primary-button" type="submit" :disabled="saving">
              {{ saving ? '保存中...' : selectedUid ? '保存用户' : '请选择用户' }}
            </button>
          </div>
        </form>
      </article>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { fetchSystemUserDetail, fetchSystemUsers, saveSystemUser } from '@/api/admin';
import type { SystemUserOverviewPayload } from '@/types';

const payload = ref<SystemUserOverviewPayload | null>(null);
const keyword = ref('');
const currentStatus = ref(1);
const currentForbiddenIp = ref(0);
const selectedUid = ref(0);
const saving = ref(false);

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

const meta = computed(() => payload.value?.meta ?? null);

function getErrorMessage(error: unknown) {
  return error instanceof Error ? error.message : '请求失败';
}

async function reload() {
  payload.value = await fetchSystemUsers(currentStatus.value, keyword.value, currentForbiddenIp.value);
  if (!selectedUid.value && payload.value.list.length > 0) {
    await editUser(payload.value.list[0].uid);
  }
}

async function switchTab(status: number, forbiddenIp: number) {
  currentStatus.value = status;
  currentForbiddenIp.value = forbiddenIp;
  selectedUid.value = 0;
  await reload();
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

onMounted(async () => {
  await reload();
});
</script>
