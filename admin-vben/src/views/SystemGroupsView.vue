<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">System / Groups</span>
        <h3>管理组</h3>
        <p>管理组列表、规则选择和保存删除都已接入独立 `adminapi`，页面结构按管理端统一基线整理。</p>
      </div>
      <div class="toolbar-row">
        <label class="search-inline">
          <span>搜索</span>
          <Input v-model.trim="keyword" placeholder="按组名称筛选" @keydown.enter="reload" />
        </label>
        <Button type="button" @click="startCreate">新增管理组</Button>
      </div>
    </section>

    <section class="stats-grid">
      <article class="stat-card">
        <span class="eyebrow">Groups</span>
        <strong>{{ payload?.list.length || 0 }}</strong>
        <small>当前可维护的系统组总数</small>
      </article>
      <article class="stat-card">
        <span class="eyebrow">Selection</span>
        <strong>{{ selectedGroup?.title || '新建模式' }}</strong>
        <small>{{ selectedGroup ? (selectedGroup.system ? '系统内置组' : '自定义组') : '尚未选择管理组' }}</small>
      </article>
      <article class="stat-card">
        <span class="eyebrow">Rules</span>
        <strong>{{ form.rule_ids.length }}</strong>
        <small>当前编辑器已选权限规则</small>
      </article>
    </section>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">系统组列表</span>
        <div class="quick-links group-list">
          <Button
            v-for="item in payload?.list || []"
            :key="item.id"
            class="group-card-button"
            :variant="selectedId === item.id ? 'default' : 'outline'"
            type="button"
            @click="editGroup(item.id)"
          >
            <span class="group-card-main">
              <strong>{{ item.title }}</strong>
              <small>{{ item.status ? '启用' : '禁用' }} / {{ item.rule_count < 0 ? '全部规则' : `${item.rule_count} 条规则` }}</small>
            </span>
            <span>{{ item.system ? '系统内置' : '自定义' }}</span>
          </Button>
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
            <small>保留旧后台规则树的层级顺序，便于按模块逐步迁移后台模板。</small>
          </div>
          <div class="form-actions">
            <Button type="submit" :disabled="saving">
              {{ saving ? '保存中...' : form.id ? '保存管理组' : '创建管理组' }}
            </Button>
            <Button variant="outline" type="button" @click="startCreate">重置</Button>
            <Button
              v-if="form.id && !form.system"
              variant="destructive"
              type="button"
              @click="removeGroup(form.id)"
            >
              删除当前组
            </Button>
          </div>
        </form>
      </article>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { deleteSystemGroup, fetchSystemGroupDetail, fetchSystemGroupMeta, fetchSystemGroups, saveSystemGroup } from '@/api/admin';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
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

const selectedGroup = computed(() => payload.value?.list.find((item) => item.id === selectedId.value) || null);

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
