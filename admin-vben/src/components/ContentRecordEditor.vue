<template>
  <form class="editor-form" @submit.prevent="$emit('submit')">
    <ContentDetailPanel :links="links" :detail-fields="detailFields" :flags="flags" />
    <label v-if="showTitleField">
      <span>{{ titleLabel }}</span>
      <input v-model.trim="form.title" :placeholder="titlePlaceholder" :readonly="readonlyTitle || readonly" />
    </label>
    <label>
      <span>{{ bodyLabel }}</span>
      <textarea v-model="form.body" :rows="bodyRows" :placeholder="bodyPlaceholder" :readonly="readonlyBody || readonly" />
    </label>
    <label v-if="showSeoFields">
      <span>SEO 标题</span>
      <input v-model.trim="form.seo_title" placeholder="请输入 SEO 标题" :readonly="readonly" />
    </label>
    <label v-if="showSeoFields">
      <span>SEO 关键词</span>
      <input v-model.trim="form.seo_keywords" placeholder="请输入 SEO 关键词" :readonly="readonly" />
    </label>
    <label v-if="showSeoFields">
      <span>SEO 描述</span>
      <textarea v-model="form.seo_description" rows="5" placeholder="请输入 SEO 描述" :readonly="readonly" />
    </label>
    <div v-if="showActions" class="form-actions">
      <button class="primary-button" type="submit" :disabled="saving || !form.id">
        {{ saving ? '保存中...' : form.id ? submitLabel : emptyLabel }}
      </button>
    </div>
  </form>
</template>

<script setup lang="ts">
import ContentDetailPanel from '@/components/ContentDetailPanel.vue';
import type { ContentRecordFormState, DetailFieldItem } from '@/types';

interface EditorLinkItem {
  label: string;
  href: string;
}

defineEmits<{
  submit: [];
}>();

withDefaults(defineProps<{
  form: ContentRecordFormState;
  links?: EditorLinkItem[];
  detailFields?: DetailFieldItem[];
  flags?: string[];
  titleLabel?: string;
  titlePlaceholder?: string;
  bodyLabel?: string;
  bodyPlaceholder?: string;
  bodyRows?: number;
  submitLabel?: string;
  emptyLabel?: string;
  saving?: boolean;
  showTitleField?: boolean;
  showSeoFields?: boolean;
  showActions?: boolean;
  readonly?: boolean;
  readonlyTitle?: boolean;
  readonlyBody?: boolean;
}>(), {
  links: () => [],
  detailFields: () => [],
  flags: () => [],
  titleLabel: '标题',
  titlePlaceholder: '请输入标题',
  bodyLabel: '正文',
  bodyPlaceholder: '请输入正文',
  bodyRows: 12,
  submitLabel: '保存内容',
  emptyLabel: '请选择内容',
  saving: false,
  showTitleField: true,
  showSeoFields: true,
  showActions: true,
  readonly: false,
  readonlyTitle: false,
  readonlyBody: false,
});
</script>
