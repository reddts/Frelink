<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">Content / Questions</span>
        <h3>问题管理</h3>
        <p>问题列表、SEO 和回收操作已迁入新管理端，旧页开始退到兜底入口。</p>
      </div>
      <div class="toolbar-row">
        <label class="search-inline">
          <span>搜索</span>
          <input v-model.trim="keyword" placeholder="按问题标题筛选" @keydown.enter="reload" />
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
    </section>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">问题列表</span>
        <div class="config-table content-table">
          <div class="config-table-head content-table-head">
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
              <strong>{{ item.title }}</strong>
              <small>#{{ item.id }}</small>
            </span>
            <span>
              <strong>{{ item.user_name || '未知用户' }}</strong>
              <small>{{ item.url_token || '-' }}</small>
            </span>
            <span>
              <strong>回答 {{ item.answer_count }}</strong>
              <small>评论 {{ item.comment_count }} / 浏览 {{ item.view_count }}</small>
            </span>
            <span>
              <strong>{{ item.create_time_text }}</strong>
              <small>{{ item.update_time_text }}</small>
            </span>
            <span class="config-actions">
              <button class="text-button" type="button" @click="editItem(item.id)">SEO</button>
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
          <label>
            <span>问题标题</span>
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
            <span>问题详情预览</span>
            <textarea :value="detail?.detail || ''" rows="10" disabled />
          </label>
          <div class="form-actions">
            <button class="primary-button" type="submit" :disabled="saving || !seoForm.id">
              {{ saving ? '保存中...' : seoForm.id ? '保存 SEO' : '请选择问题' }}
            </button>
          </div>
        </form>
      </article>
    </section>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import {
  deleteContentQuestion,
  fetchContentQuestionDetail,
  fetchContentQuestions,
  manageContentQuestion,
  saveContentQuestionSeo,
} from '@/api/admin';
import type { ContentQuestionDetail, ContentQuestionOverviewPayload } from '@/types';

const payload = ref<ContentQuestionOverviewPayload | null>(null);
const detail = ref<ContentQuestionDetail | null>(null);
const keyword = ref('');
const currentStatus = ref(1);
const selectedId = ref(0);
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
}

async function reload() {
  payload.value = await fetchContentQuestions(currentStatus.value, keyword.value);
}

async function switchStatus(status: number) {
  currentStatus.value = status;
  resetSeoForm();
  await reload();
}

async function editItem(id: number) {
  detail.value = await fetchContentQuestionDetail(id);
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
    await saveContentQuestionSeo(seoForm.value);
    await editItem(seoForm.value.id);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  } finally {
    saving.value = false;
  }
}

async function deleteItem(id: number) {
  if (!window.confirm('确认删除该问题？')) {
    return;
  }
  try {
    await deleteContentQuestion(id);
    if (selectedId.value === id) {
      resetSeoForm();
    }
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function manageItem(id: number, type: 'recover' | 'remove') {
  const tips = type === 'recover' ? '确认恢复该问题？' : '确认彻底删除该问题？';
  if (!window.confirm(tips)) {
    return;
  }
  try {
    await manageContentQuestion(id, type);
    if (selectedId.value === id) {
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
