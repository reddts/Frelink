<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">System / Groups</span>
        <h3>管理组</h3>
        <p>管理组列表、规则选择和保存删除都已接入独立 `adminapi`，不再停留在只读查看。</p>
      </div>
      <div class="toolbar-row">
        <label class="search-inline">
          <span>搜索</span>
          <input v-model.trim="keyword" placeholder="按组名称筛选" @keydown.enter="reload" />
        </label>
        <button class="primary-button" type="button" @click="startCreate">新增管理组</button>
      </div>
    </section>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">系统组列表</span>
        <div class="quick-links group-list">
          <button
            v-for="item in payload?.list || []"
            :key="item.id"
            class="ghost-button group-card-button"
            :class="{ 'is-current': selectedId === item.id }"
            type="button"
            @click="editGroup(item.id)"
          >
            <span class="group-card-main">
              <strong>{{ item.title }}</strong>
              <small>{{ item.status ? '启用' : '禁用' }} / {{ item.rule_count < 0 ? '全部规则' : `${item.rule_count} 条规则` }}</small>
            </span>
            <span>{{ item.system ? '系统内置' : '自定义' }}</span>
          </button>
        </div>
      </article>

      <article class="panel-card">
        <span class="eyebrow">管理组编辑器</span>
        <form class="editor-form" @submit.prevent="submitForm">
          <label>
            <span>系统组名称</span>
            <input v-model.trim="form.title" placeholder="请输入系统组名称" />
          </label>
          <label>
            <span>启用状态</span>
            <select v-model.number="form.status">
              <option :value="1">启用</option>
              <option :value="0">禁用</option>
            </select>
          </label>
          <div class="editor-form-group">
            <span>权限规则</span>
            <div class="check-grid">
              <label v-for="item in ruleOptions" :key="item.id" class="check-item">
                <input v-model="form.rule_ids" :value="item.id" type="checkbox" />
                <span>{{ item.label }}</span>
              </label>
            </div>
          </div>
          <div class="form-actions">
            <button class="primary-button" type="submit" :disabled="saving">
              {{ saving ? '保存中...' : form.id ? '保存管理组' : '创建管理组' }}
            </button>
            <button class="ghost-button" type="button" @click="startCreate">重置</button>
            <button
              v-if="form.id && !form.system"
              class="ghost-button danger-button"
              type="button"
              @click="removeGroup(form.id)"
            >
              删除当前组
            </button>
          </div>
        </form>
      </article>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { deleteSystemGroup, fetchSystemGroupDetail, fetchSystemGroupMeta, fetchSystemGroups, saveSystemGroup } from '@/api/admin';
import type { GroupRuleTreeNode, SystemGroupListPayload, SystemGroupMetaPayload } from '@/types';

const payload = ref<SystemGroupListPayload | null>(null);
const meta = ref<SystemGroupMetaPayload | null>(null);
const keyword = ref('');
const selectedId = ref(0);
const saving = ref(false);

const form = ref({
  id: 0,
  title: '',
  status: 1,
  rule_ids: [] as number[],
  system: 0,
});

const ruleOptions = computed(() => {
  const source = meta.value?.rule_tree || [];
  const result: Array<{ id: number; label: string }> = [];
  const walk = (nodes: GroupRuleTreeNode[], depth = 0) => {
    nodes.forEach((node) => {
      result.push({
        id: node.id,
        label: `${'　'.repeat(depth)}${node.text}`,
      });
      walk(node.children || [], depth + 1);
    });
  };
  walk(source);
  return result;
});

function getErrorMessage(error: unknown) {
  return error instanceof Error ? error.message : '请求失败';
}

function resetForm() {
  form.value = {
    id: 0,
    title: '',
    status: 1,
    rule_ids: [],
    system: 0,
  };
  selectedId.value = 0;
}

async function reload() {
  payload.value = await fetchSystemGroups(keyword.value);
}

async function startCreate() {
  if (!meta.value) {
    meta.value = await fetchSystemGroupMeta();
  }
  resetForm();
}

async function editGroup(id: number) {
  const detail = await fetchSystemGroupDetail(id);
  meta.value = {
    rule_tree: detail.rule_tree || [],
  };
  form.value = {
    id: detail.id,
    title: detail.title,
    status: detail.status,
    rule_ids: [...(detail.rule_ids || [])],
    system: detail.system,
  };
  selectedId.value = id;
}

async function submitForm() {
  saving.value = true;
  try {
    const result = await saveSystemGroup(form.value);
    await reload();
    await editGroup(Number(result.id || 0));
  } catch (error) {
    window.alert(getErrorMessage(error));
  } finally {
    saving.value = false;
  }
}

async function removeGroup(id: number) {
  if (!window.confirm('确认删除当前管理组？')) {
    return;
  }

  try {
    await deleteSystemGroup(id);
    resetForm();
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

onMounted(async () => {
  meta.value = await fetchSystemGroupMeta();
  await reload();
  resetForm();
});
</script>
