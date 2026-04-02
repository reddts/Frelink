<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">System / Menus</span>
        <h3>菜单管理</h3>
        <p>菜单树现在支持分组切换、编辑、删除和状态切换，旧后台不再是唯一入口。</p>
      </div>
      <div class="toolbar-row">
        <div class="tab-row">
          <button
            v-for="item in payload?.groups || []"
            :key="item.value"
            class="ghost-button"
            :class="{ 'is-current': currentGroup === item.value }"
            type="button"
            @click="switchGroup(item.value)"
          >
            {{ item.label }}
          </button>
        </div>
        <button class="primary-button" type="button" @click="startCreate">新增菜单</button>
      </div>
    </section>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">菜单树</span>
        <div class="menu-table">
          <div class="menu-table-head">
            <span>名称</span>
            <span>链接</span>
            <span>状态</span>
            <span>默认首页</span>
            <span>操作</span>
          </div>

          <div
            v-for="item in flatList"
            :key="item.id"
            class="menu-table-row"
            :class="{ 'is-current-row': selectedId === item.id }"
          >
            <span :style="{ paddingLeft: `${item.depth * 22}px` }">
              <strong>{{ item.title }}</strong>
              <small>{{ item.icon || '无图标' }}</small>
            </span>
            <span>
              <strong>{{ item.name || '-' }}</strong>
              <small>{{ item.type === 1 ? '站内链接' : '站外链接' }}</small>
            </span>
            <span>{{ item.status ? '启用' : '禁用' }}</span>
            <span>{{ item.is_home ? '是' : '否' }}</span>
            <span class="config-actions">
              <button class="text-button" type="button" @click="editItem(item.id)">编辑</button>
              <button class="text-button" type="button" @click="toggleField(item.id, 'status')">切换状态</button>
              <button class="text-button" type="button" @click="toggleField(item.id, 'is_home')">首页切换</button>
              <button class="text-button danger-button" type="button" @click="removeItem(item.id)">删除</button>
            </span>
          </div>
        </div>
      </article>

      <article class="panel-card">
        <span class="eyebrow">菜单编辑器</span>
        <form class="editor-form" @submit.prevent="submitForm">
          <label>
            <span>菜单分组</span>
            <select v-model="form.group">
              <option value="nav">主导航</option>
              <option value="footer">底部导航</option>
            </select>
          </label>
          <label>
            <span>父级标题</span>
            <select v-model.number="form.pid">
              <option v-for="item in payload?.parent_options || []" :key="`${item.value}`" :value="Number(item.value)">
                {{ item.label }}
              </option>
            </select>
          </label>
          <label>
            <span>导航名称</span>
            <input v-model.trim="form.title" placeholder="请输入导航名称" />
          </label>
          <label>
            <span>导航链接</span>
            <input v-model.trim="form.name" placeholder="如 member/MenuRule/index 或完整 URL" />
          </label>
          <label>
            <span>图标</span>
            <input v-model.trim="form.icon" placeholder="如 fa fa-home" />
          </label>
          <label>
            <span>附加参数</span>
            <input v-model.trim="form.param" placeholder="如 type=button&name=my" />
          </label>
          <label>
            <span>链接类型</span>
            <select v-model.number="form.type">
              <option :value="1">站内</option>
              <option :value="2">站外</option>
            </select>
          </label>
          <label>
            <span>登录显示</span>
            <select v-model.number="form.auth_open">
              <option :value="1">是</option>
              <option :value="0">否</option>
            </select>
          </label>
          <label>
            <span>默认首页</span>
            <select v-model.number="form.is_home">
              <option :value="1">是</option>
              <option :value="0">否</option>
            </select>
          </label>
          <label>
            <span>菜单状态</span>
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
              {{ saving ? '保存中...' : form.id ? '保存菜单' : '创建菜单' }}
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
import { deleteSystemMenu, fetchSystemMenuDetail, fetchSystemMenus, saveSystemMenu, toggleSystemMenuState } from '@/api/admin';
import type { SystemMenuListPayload, SystemMenuNode } from '@/types';

type FlatMenuNode = SystemMenuNode & { depth: number };

const payload = ref<SystemMenuListPayload | null>(null);
const currentGroup = ref('nav');
const selectedId = ref(0);
const saving = ref(false);

const form = ref<SystemMenuNode>({
  id: 0,
  pid: 0,
  group: 'nav',
  title: '',
  icon: '',
  name: '',
  type: 1,
  is_home: 0,
  param: '',
  auth_open: 0,
  status: 1,
  sort: 50,
  children: [],
});

const flatList = computed<FlatMenuNode[]>(() => {
  const result: FlatMenuNode[] = [];
  const walk = (items: SystemMenuNode[], depth = 0) => {
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
    id: 0,
    pid: 0,
    group: currentGroup.value,
    title: '',
    icon: '',
    name: '',
    type: 1,
    is_home: 0,
    param: '',
    auth_open: 0,
    status: 1,
    sort: 50,
    children: [],
  };
  selectedId.value = 0;
}

async function load(group = 'nav') {
  currentGroup.value = group;
  payload.value = await fetchSystemMenus(group);
}

async function switchGroup(group: string) {
  await load(group);
  resetForm();
}

async function startCreate() {
  resetForm();
}

async function editItem(id: number) {
  const detail = await fetchSystemMenuDetail(id);
  form.value = {
    ...detail,
    children: [],
  };
  selectedId.value = id;
}

async function submitForm() {
  saving.value = true;
  try {
    const result = await saveSystemMenu(form.value);
    await load(form.value.group);
    await editItem(Number(result.id || 0));
  } catch (error) {
    window.alert(getErrorMessage(error));
  } finally {
    saving.value = false;
  }
}

async function toggleField(id: number, field: 'status' | 'is_home') {
  try {
    await toggleSystemMenuState(id, field);
    await load(currentGroup.value);
    if (selectedId.value === id) {
      await editItem(id);
    }
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function removeItem(id: number) {
  if (!window.confirm('确认删除该菜单？')) {
    return;
  }

  try {
    await deleteSystemMenu(id);
    if (selectedId.value === id) {
      resetForm();
    }
    await load(currentGroup.value);
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

onMounted(async () => {
  await load('nav');
  resetForm();
});
</script>
