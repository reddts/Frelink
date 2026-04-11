<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">Content / Category</span>
        <h3>分类管理</h3>
        <p>已迁移分类管理链路，支持类型筛选、树形列表浏览、详情编辑与批量删除。</p>
      </div>
      <div class="toolbar-row">
        <label class="search-inline">
          <span>搜索</span>
          <Input v-model.trim="keyword" placeholder="按分类名称筛选" @keydown.enter="reload" />
        </label>
        <div class="tab-row">
          <Button
            v-for="item in payload?.tabs || []"
            :key="item.value || 'all'"
            type="button"
            :variant="currentType === item.value ? 'default' : 'outline'"
            size="sm"
            @click="switchType(item.value)"
          >
            {{ item.label }}
          </Button>
        </div>
      </div>
      <div class="selection-toolbar">
        <span>已选 {{ selectedIds.length }} 个分类</span>
        <Button variant="outline" size="sm" type="button" @click="toggleSelectAll">
          {{ isAllSelected ? '取消全选' : '全选当前列表' }}
        </Button>
        <Button
          type="button"
          variant="destructive"
          size="sm"
          :disabled="!selectedIds.length"
          @click="deleteSelected"
        >
          批量删除
        </Button>
      </div>
    </section>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">分类列表</span>
        <div class="config-table content-table">
          <div class="config-table-head content-table-head">
            <span>选择</span>
            <span>分类</span>
            <span>结构</span>
            <span>状态</span>
            <span>操作</span>
          </div>
          <div
            v-for="item in payload?.list || []"
            :key="item.id"
            class="config-table-row content-table-row"
            :class="{ 'is-current': selectedId === item.id }"
          >
            <span>
              <label class="table-check">
                <input v-model="selectedIds" :value="item.id" type="checkbox" />
                <small>选中</small>
              </label>
            </span>
            <span>
              <strong>{{ item.title }}</strong>
              <small>#{{ item.id }} / {{ item.url_token || '-' }}</small>
              <ContentFlags :flags="item.flags" />
            </span>
            <span>
              <strong>{{ item.type_label }}</strong>
              <small>{{ item.parent_title || '无父级' }}</small>
            </span>
            <span>
              <strong>{{ item.status === 1 ? '正常' : '禁用' }}</strong>
              <small>排序 {{ item.sort }}</small>
            </span>
            <span class="config-actions">
              <button class="text-button" type="button" @click="editItem(item.id)">编辑</button>
              <button class="text-button danger-button" type="button" @click="deleteItem(item.id)">删除</button>
            </span>
          </div>
        </div>
      </article>

      <article class="panel-card">
        <span class="eyebrow">分类编辑器</span>
        <ContentRecordEditor
          :form="editorForm"
          :detail-fields="detail?.detail_fields || []"
          :flags="detail?.flags || []"
          title-label="分类名称"
          title-placeholder="请输入分类名称"
          body-label="分类描述"
          body-placeholder="请输入分类描述"
          submit-label="保存分类"
          empty-label="请选择分类"
          :show-seo-fields="false"
          :saving="saving"
          @submit="submitCategory"
        />
        <div v-if="detail" class="category-form-grid">
          <label class="field-item">
            <span>分类图标</span>
            <Input v-model.trim="state.icon" placeholder="填写图标地址" />
          </label>
          <label class="field-item">
            <span>分类类型</span>
            <select v-model="state.type" :disabled="state.pid > 0">
              <option v-for="item in typeOptions" :key="String(item.value)" :value="String(item.value)">
                {{ item.label }}
              </option>
            </select>
          </label>
          <label class="field-item">
            <span>父级ID</span>
            <input v-model.number="state.pid" type="number" min="0" />
          </label>
          <label class="field-item">
            <span>排序值</span>
            <input v-model.number="state.sort" type="number" />
          </label>
          <label class="field-item">
            <span>状态</span>
            <select v-model.number="state.status">
              <option :value="1">正常</option>
              <option :value="0">禁用</option>
            </select>
          </label>
        </div>
      </article>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import ContentFlags from '@/components/ContentFlags.vue';
import ContentRecordEditor from '@/components/ContentRecordEditor.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import {
  deleteContentCategory,
  fetchContentCategories,
  fetchContentCategoryDetail,
  saveContentCategory,
} from '@/api/admin';
import type { ContentCategoryDetail, ContentCategoryOverviewPayload, ContentRecordFormState } from '@/types';

const payload = ref<ContentCategoryOverviewPayload | null>(null);
const detail = ref<ContentCategoryDetail | null>(null);
const keyword = ref('');
const currentType = ref('');
const selectedId = ref(0);
const selectedIds = ref<number[]>([]);
const saving = ref(false);

const state = ref({
  icon: '',
  type: 'common',
  pid: 0,
  sort: 0,
  status: 1,
});

const editorForm = ref<ContentRecordFormState>({
  id: 0,
  title: '',
  body: '',
  seo_title: '',
  seo_keywords: '',
  seo_description: '',
});

const typeOptions = computed(() => detail.value?.type_options || payload.value?.type_options || []);

const isAllSelected = computed(() => {
  const total = payload.value?.list.length ?? 0;
  return total > 0 && selectedIds.value.length === total;
});

function getErrorMessage(error: unknown) {
  return error instanceof Error ? error.message : '请求失败';
}

function resetForm() {
  editorForm.value = {
    id: 0,
    title: '',
    body: '',
    seo_title: '',
    seo_keywords: '',
    seo_description: '',
  };
  state.value = {
    icon: '',
    type: 'common',
    pid: 0,
    sort: 0,
    status: 1,
  };
  detail.value = null;
  selectedId.value = 0;
  selectedIds.value = [];
}

function toggleSelectAll() {
  const list = payload.value?.list || [];
  if (isAllSelected.value) {
    selectedIds.value = [];
    return;
  }
  selectedIds.value = list.map((item) => item.id);
}

async function reload() {
  payload.value = await fetchContentCategories(currentType.value, keyword.value);
  const validIds = new Set((payload.value?.list || []).map((item) => item.id));
  selectedIds.value = selectedIds.value.filter((id) => validIds.has(id));
}

async function switchType(type: string) {
  currentType.value = type;
  resetForm();
  await reload();
}

async function editItem(id: number) {
  detail.value = await fetchContentCategoryDetail(id);
  selectedId.value = id;
  editorForm.value = {
    id: detail.value.id,
    title: detail.value.raw_title || detail.value.title || '',
    body: detail.value.description || '',
    seo_title: '',
    seo_keywords: '',
    seo_description: '',
  };
  state.value = {
    icon: detail.value.icon || '',
    type: detail.value.type || 'common',
    pid: detail.value.pid || 0,
    sort: detail.value.sort || 0,
    status: detail.value.status,
  };
}

async function submitCategory() {
  if (!editorForm.value.id) {
    return;
  }

  saving.value = true;
  try {
    await saveContentCategory({
      id: editorForm.value.id,
      title: editorForm.value.title,
      description: editorForm.value.body,
      icon: state.value.icon,
      type: state.value.type,
      pid: state.value.pid,
      sort: state.value.sort,
      status: state.value.status,
    });
    await editItem(editorForm.value.id);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  } finally {
    saving.value = false;
  }
}

async function deleteItem(id: number) {
  if (!window.confirm('确认删除该分类？如果存在子分类会被拦截。')) {
    return;
  }

  try {
    await deleteContentCategory(id);
    if (selectedId.value === id) {
      resetForm();
    }
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function deleteSelected() {
  if (!selectedIds.value.length) {
    return;
  }

  if (!window.confirm(`确认删除选中的 ${selectedIds.value.length} 个分类？如果包含父分类会被拦截。`)) {
    return;
  }

  try {
    await deleteContentCategory(selectedIds.value);
    if (selectedId.value && selectedIds.value.includes(selectedId.value)) {
      resetForm();
    }
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

onMounted(async () => {
  await reload();
});
</script>

<style scoped>
.category-form-grid {
  margin-top: 12px;
  display: grid;
  gap: 10px;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
}

.field-item {
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 13px;
  color: #cbd5e1;
}

.field-item select,
.field-item input {
  border: 1px solid rgba(148, 163, 184, 0.35);
  background: rgba(15, 23, 42, 0.45);
  color: #e2e8f0;
  border-radius: 8px;
  padding: 8px 10px;
}
</style>
