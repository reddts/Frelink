<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">System / Auth</span>
        <h3>权限节点</h3>
        <p>旧后台 `admin/Auth.php` 已迁入 `adminapi + Vue`，现在可直接在新管理端维护节点树。</p>
      </div>
      <div class="quick-links">
        <button class="primary-button" type="button" @click="startCreate">新增节点</button>
      </div>
    </section>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">节点树</span>
        <div class="menu-table">
          <div class="menu-table-head auth-table-head">
            <span>名称</span>
            <span>控制器 / 方法</span>
            <span>状态</span>
            <span>权限验证</span>
            <span>操作</span>
          </div>

          <div
            v-for="item in flatList"
            :key="item.id"
            class="menu-table-row auth-table-row"
            :class="{ 'is-current-row': selectedId === item.id }"
          >
            <span :style="{ paddingLeft: `${item.depth * 22}px` }">
              <strong>{{ item.title }}</strong>
              <small>{{ item.icon || '无图标' }}</small>
            </span>
            <span>
              <strong>{{ item.name }}</strong>
              <small>{{ item.param || '无附加参数' }}</small>
            </span>
            <span>{{ item.status ? '启用' : '禁用' }}</span>
            <span>{{ item.auth_open ? '是' : '否' }}</span>
            <span class="config-actions">
              <button class="text-button" type="button" @click="editItem(item.id)">编辑</button>
              <button class="text-button" type="button" @click="toggleStatus(item.id)">切换状态</button>
              <button class="text-button danger-button" type="button" @click="removeItem(item.id)">删除</button>
            </span>
          </div>
        </div>
      </article>

      <article class="panel-card">
        <span class="eyebrow">节点编辑器</span>
        <form class="editor-form" @submit.prevent="submitForm">
          <label>
            <span>父级节点</span>
            <select v-model.number="form.pid">
              <option v-for="item in parentOptions" :key="`${item.value}`" :value="Number(item.value)">
                {{ item.label }}
              </option>
            </select>
          </label>
          <label>
            <span>菜单名称</span>
            <input v-model.trim="form.title" placeholder="请输入菜单名称" />
          </label>
          <label>
            <span>控制器 / 方法</span>
            <input v-model.trim="form.name" placeholder="如 admin/Menu/index" />
          </label>
          <label>
            <span>图标</span>
            <input v-model.trim="form.icon" placeholder="如 fa fa-cog" />
          </label>
          <label>
            <span>附加参数</span>
            <input v-model.trim="form.param" placeholder="如 type=button&name=my" />
          </label>
          <label>
            <span>权限验证</span>
            <select v-model.number="form.auth_open">
              <option :value="1">是</option>
              <option :value="0">否</option>
            </select>
          </label>
          <label>
            <span>菜单显示</span>
            <select v-model.number="form.menu">
              <option :value="1">显示</option>
              <option :value="0">隐藏</option>
            </select>
          </label>
          <label>
            <span>节点类型</span>
            <select v-model.number="form.type">
              <option :value="1">菜单</option>
              <option :value="0">权限</option>
            </select>
          </label>
          <label>
            <span>状态</span>
            <select v-model.number="form.status">
              <option :value="1">启用</option>
              <option :value="0">禁用</option>
            </select>
          </label>
          <label>
            <span>排序值</span>
            <input v-model.number="form.sort" type="number" />
          </label>
          <div class="form-actions">
            <button class="primary-button" type="submit" :disabled="saving">
              {{ saving ? '保存中...' : form.id ? '保存节点' : '创建节点' }}
            </button>
            <button class="ghost-button" type="button" @click="startCreate">重置</button>
          </div>
        </form>
      </article>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import {
  deleteSystemAuth,
  fetchSystemAuthDetail,
  fetchSystemAuthMeta,
  fetchSystemAuths,
  saveSystemAuth,
  toggleSystemAuthState,
} from '@/api/admin';
import type { SelectOption, SystemAuthDetail, SystemAuthNode } from '@/types';

type AuthFlatNode = SystemAuthNode & { depth: number };

const payload = ref<{ list: SystemAuthNode[]; parent_options: SelectOption[] } | null>(null);
const meta = ref<{ parent_options: SelectOption[]; detail_template: SystemAuthDetail } | null>(null);
const selectedId = ref(0);
const saving = ref(false);

const form = ref<SystemAuthDetail>({
  id: 0,
  pid: 0,
  icon: '',
  name: '',
  title: '',
  param: '',
  auth_open: 1,
  status: 1,
  sort: 50,
  menu: 1,
  type: 1,
});

const parentOptions = computed(() => payload.value?.parent_options || meta.value?.parent_options || []);

const flatList = computed<AuthFlatNode[]>(() => {
  const result: AuthFlatNode[] = [];
  const walk = (items: SystemAuthNode[], depth = 0) => {
    items.forEach((item) => {
      result.push({ ...item, depth });
      walk(item.children || [], depth + 1);
    });
  };
  walk(payload.value?.list || []);
  return result;
});

function getErrorMessage(error: unknown) {
  return error instanceof Error ? error.message : '请求失败';
}

function resetForm() {
  form.value = {
    ...(meta.value?.detail_template || {
      id: 0,
      pid: 0,
      icon: '',
      name: '',
      title: '',
      param: '',
      auth_open: 1,
      status: 1,
      sort: 50,
      menu: 1,
      type: 1,
    }),
  };
  selectedId.value = 0;
}

async function reload() {
  payload.value = await fetchSystemAuths();
}

async function startCreate() {
  if (!meta.value) {
    meta.value = await fetchSystemAuthMeta();
  }
  resetForm();
}

async function editItem(id: number) {
  const detail = await fetchSystemAuthDetail(id);
  form.value = { ...detail };
  selectedId.value = id;
}

async function submitForm() {
  saving.value = true;
  try {
    const result = await saveSystemAuth(form.value);
    await reload();
    await editItem(Number(result.id || 0));
  } catch (error) {
    window.alert(getErrorMessage(error));
  } finally {
    saving.value = false;
  }
}

async function toggleStatus(id: number) {
  try {
    await toggleSystemAuthState(id);
    await reload();
    if (selectedId.value === id) {
      await editItem(id);
    }
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function removeItem(id: number) {
  if (!window.confirm('确认删除该权限节点？')) {
    return;
  }

  try {
    await deleteSystemAuth(id);
    if (selectedId.value === id) {
      resetForm();
    }
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

onMounted(async () => {
  meta.value = await fetchSystemAuthMeta();
  await reload();
  resetForm();
});
</script>
