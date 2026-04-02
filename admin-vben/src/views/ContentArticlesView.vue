<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">Content / Articles</span>
        <h3>文章管理</h3>
        <p>文章列表和 SEO 保存已迁入新管理端，删除与恢复链路直接走独立 `adminapi`。</p>
      </div>
      <div class="toolbar-row">
        <label class="search-inline">
          <span>搜索</span>
          <input v-model.trim="keyword" placeholder="按文章标题筛选" @keydown.enter="reload" />
        </label>
        <div class="tab-row">
          <button
            v-for="item in payload?.tabs || []"
            :key="item.value"
            class="ghost-button"
            :class="{ 'is-current': currentStatus === item.value }"
            type="button"
            @click="switchStatus(item.value)"
          >
            {{ item.label }}
          </button>
        </div>
      </div>
      <div class="selection-toolbar">
        <span>已选 {{ selectedIds.length }} 篇</span>
        <button class="ghost-button" type="button" @click="toggleSelectAll">
          {{ isAllSelected ? '取消全选' : '全选当前列表' }}
        </button>
        <button
          v-if="currentStatus === 1"
          class="ghost-button danger-button"
          type="button"
          :disabled="!selectedIds.length"
          @click="deleteSelected"
        >
          批量删除
        </button>
        <button
          v-if="currentStatus === 0"
          class="ghost-button"
          type="button"
          :disabled="!selectedIds.length"
          @click="manageSelected('recover')"
        >
          批量恢复
        </button>
        <button
          v-if="currentStatus === 0"
          class="ghost-button danger-button"
          type="button"
          :disabled="!selectedIds.length"
          @click="manageSelected('remove')"
        >
          批量彻底删除
        </button>
      </div>
    </section>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">文章列表</span>
        <div class="config-table content-table">
          <div class="config-table-head content-table-head">
            <span>选择</span>
            <span>标题</span>
            <span>用户</span>
            <span>互动</span>
            <span>时间</span>
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
              <small>#{{ item.id }}</small>
            </span>
            <span>
              <strong>{{ item.user_name || '未知用户' }}</strong>
              <small>{{ item.url_token || '-' }}</small>
            </span>
            <span>
              <strong>评论 {{ item.comment_count }}</strong>
              <small>浏览 {{ item.view_count }}</small>
            </span>
            <span>
              <strong>{{ item.create_time_text }}</strong>
              <small>{{ item.update_time_text }}</small>
            </span>
            <span class="config-actions">
              <button class="text-button" type="button" @click="editItem(item.id)">SEO</button>
              <a v-if="item.preview_url" class="text-button" :href="item.preview_url" target="_blank" rel="noreferrer">
                预览
              </a>
              <a v-if="item.edit_url" class="text-button" :href="item.edit_url" target="_blank" rel="noreferrer">
                发布页
              </a>
              <button
                v-if="currentStatus === 1"
                class="text-button danger-button"
                type="button"
                @click="deleteItem(item.id)"
              >
                删除
              </button>
              <button
                v-if="currentStatus === 0"
                class="text-button"
                type="button"
                @click="manageItem(item.id, 'recover')"
              >
                恢复
              </button>
              <button
                v-if="currentStatus === 0"
                class="text-button danger-button"
                type="button"
                @click="manageItem(item.id, 'remove')"
              >
                彻底删除
              </button>
            </span>
          </div>
        </div>
      </article>

      <article class="panel-card">
        <span class="eyebrow">SEO 编辑器</span>
        <form class="editor-form" @submit.prevent="submitSeo">
          <div v-if="detail" class="inline-links">
            <a v-if="detail.preview_url" class="text-button" :href="detail.preview_url" target="_blank" rel="noreferrer">
              打开前台预览
            </a>
            <a v-if="detail.edit_url" class="text-button" :href="detail.edit_url" target="_blank" rel="noreferrer">
              打开发布页
            </a>
          </div>
          <div v-if="detail?.detail_fields?.length" class="detail-stack">
            <p v-for="field in detail.detail_fields" :key="field.label"><strong>{{ field.label }}：</strong>{{ field.value }}</p>
          </div>
          <label>
            <span>文章标题</span>
            <input :value="detail?.title || ''" disabled />
          </label>
          <label>
            <span>SEO 标题</span>
            <input v-model.trim="seoForm.seo_title" placeholder="请输入 SEO 标题" />
          </label>
          <label>
            <span>SEO 关键词</span>
            <input v-model.trim="seoForm.seo_keywords" placeholder="请输入 SEO 关键词" />
          </label>
          <label>
            <span>SEO 描述</span>
            <textarea v-model="seoForm.seo_description" rows="5" placeholder="请输入 SEO 描述" />
          </label>
          <label>
            <span>正文预览</span>
            <textarea :value="detail?.message || ''" rows="10" disabled />
          </label>
          <div class="form-actions">
            <button class="primary-button" type="submit" :disabled="saving || !seoForm.id">
              {{ saving ? '保存中...' : seoForm.id ? '保存 SEO' : '请选择文章' }}
            </button>
          </div>
        </form>
      </article>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import {
  deleteContentArticle,
  fetchContentArticleDetail,
  fetchContentArticles,
  manageContentArticle,
  saveContentArticleSeo,
} from '@/api/admin';
import type { ContentArticleDetail, ContentArticleOverviewPayload } from '@/types';

const payload = ref<ContentArticleOverviewPayload | null>(null);
const detail = ref<ContentArticleDetail | null>(null);
const keyword = ref('');
const currentStatus = ref(1);
const selectedId = ref(0);
const selectedIds = ref<number[]>([]);
const saving = ref(false);

const seoForm = ref({
  id: 0,
  seo_title: '',
  seo_keywords: '',
  seo_description: '',
});

function getErrorMessage(error: unknown) {
  return error instanceof Error ? error.message : '请求失败';
}

function resetSeoForm() {
  seoForm.value = {
    id: 0,
    seo_title: '',
    seo_keywords: '',
    seo_description: '',
  };
  detail.value = null;
  selectedId.value = 0;
  selectedIds.value = [];
}

const isAllSelected = computed(() => {
  const total = payload.value?.list.length ?? 0;
  return total > 0 && selectedIds.value.length === total;
});

function toggleSelectAll() {
  const list = payload.value?.list || [];
  if (isAllSelected.value) {
    selectedIds.value = [];
    return;
  }
  selectedIds.value = list.map((item) => item.id);
}

async function reload() {
  payload.value = await fetchContentArticles(currentStatus.value, keyword.value);
  const validIds = new Set((payload.value?.list || []).map((item) => item.id));
  selectedIds.value = selectedIds.value.filter((id) => validIds.has(id));
}

async function switchStatus(status: number) {
  currentStatus.value = status;
  resetSeoForm();
  await reload();
}

async function editItem(id: number) {
  detail.value = await fetchContentArticleDetail(id);
  selectedId.value = id;
  seoForm.value = {
    id: detail.value.id,
    seo_title: detail.value.seo_title || '',
    seo_keywords: detail.value.seo_keywords || '',
    seo_description: detail.value.seo_description || '',
  };
}

async function submitSeo() {
  if (!seoForm.value.id) {
    return;
  }

  saving.value = true;
  try {
    await saveContentArticleSeo(seoForm.value);
    await editItem(seoForm.value.id);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  } finally {
    saving.value = false;
  }
}

async function deleteItem(id: number) {
  if (!window.confirm('确认删除该文章？')) {
    return;
  }
  try {
    await deleteContentArticle(id);
    if (selectedId.value === id) {
      resetSeoForm();
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
  if (!window.confirm(`确认删除选中的 ${selectedIds.value.length} 篇文章？`)) {
    return;
  }
  try {
    await deleteContentArticle(selectedIds.value);
    if (selectedId.value && selectedIds.value.includes(selectedId.value)) {
      resetSeoForm();
    }
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function manageItem(id: number, type: 'recover' | 'remove') {
  const tips = type === 'recover' ? '确认恢复该文章？' : '确认彻底删除该文章？';
  if (!window.confirm(tips)) {
    return;
  }
  try {
    await manageContentArticle(id, type);
    if (selectedId.value === id) {
      resetSeoForm();
    }
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function manageSelected(type: 'recover' | 'remove') {
  if (!selectedIds.value.length) {
    return;
  }
  const tips =
    type === 'recover'
      ? `确认恢复选中的 ${selectedIds.value.length} 篇文章？`
      : `确认彻底删除选中的 ${selectedIds.value.length} 篇文章？`;
  if (!window.confirm(tips)) {
    return;
  }
  try {
    await manageContentArticle(selectedIds.value, type);
    if (selectedId.value && selectedIds.value.includes(selectedId.value)) {
      resetSeoForm();
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
