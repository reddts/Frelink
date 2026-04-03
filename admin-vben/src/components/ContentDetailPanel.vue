<template>
  <div class="content-detail-panel">
    <div v-if="summaryFields.length" class="detail-stack">
      <p v-for="field in summaryFields" :key="`summary-${field.label}`">
        <strong>{{ field.label }}：</strong>{{ field.value }}
      </p>
    </div>
    <div v-if="links.length" class="inline-links">
      <a
        v-for="link in links"
        :key="`${link.label}-${link.href}`"
        class="text-button"
        :href="link.href"
        target="_blank"
        rel="noreferrer"
      >
        {{ link.label }}
      </a>
    </div>
    <div v-if="detailFields.length" class="detail-stack">
      <p v-for="field in detailFields" :key="`detail-${field.label}`">
        <strong>{{ field.label }}：</strong>{{ field.value }}
      </p>
    </div>
    <ContentFlags v-if="flags.length" :flags="flags" />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import ContentFlags from '@/components/ContentFlags.vue';
import type { DetailFieldItem } from '@/types';

interface DetailLinkItem {
  label: string;
  href: string;
}

const props = withDefaults(defineProps<{
  summaryFields?: DetailFieldItem[];
  detailFields?: DetailFieldItem[];
  flags?: string[];
  links?: DetailLinkItem[];
}>(), {
  summaryFields: () => [],
  detailFields: () => [],
  flags: () => [],
  links: () => [],
});

const summaryFields = computed(() => props.summaryFields.filter((field) => field.value));
const detailFields = computed(() => props.detailFields.filter((field) => field.value));
const flags = computed(() => props.flags.filter(Boolean));
const links = computed(() => props.links.filter((link) => link.href));
</script>
