<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">System / Menus</span>
        <h3>菜单管理</h3>
        <p>这是首个脱离旧 `app/backend` 列表页的管理模块，数据直接来自独立 `adminapi`。</p>
      </div>
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
    </section>

    <article class="panel-card">
      <span class="eyebrow">菜单树</span>
      <div class="menu-table">
        <div class="menu-table-head">
          <span>名称</span>
          <span>链接</span>
          <span>状态</span>
          <span>默认首页</span>
          <span>排序</span>
        </div>

        <div v-for="item in payload?.list || []" :key="item.id">
          <MenuRow :item="item" :depth="0" />
        </div>
      </div>
    </article>
  </div>
</template>

<script setup lang="ts">
import { defineComponent, h, onMounted, ref } from 'vue';
import { fetchSystemMenus } from '@/api/admin';
import type { SystemMenuListPayload, SystemMenuNode } from '@/types';

const payload = ref<SystemMenuListPayload | null>(null);
const currentGroup = ref('nav');

const MenuRow = defineComponent<{
  item: SystemMenuNode;
  depth: number;
}>({
  name: 'MenuRow',
  props: {
    item: {
      type: Object,
      required: true,
    },
    depth: {
      type: Number,
      required: true,
    },
  },
  setup(props) {
    return () =>
      h('div', [
        h(
          'div',
          {
            class: 'menu-table-row',
          },
          [
            h(
              'span',
              {
                style: {
                  paddingLeft: `${props.depth * 22}px`,
                },
              },
              props.item.title,
            ),
            h('span', props.item.name || '-'),
            h('span', props.item.status ? '启用' : '禁用'),
            h('span', props.item.is_home ? '是' : '否'),
            h('span', String(props.item.sort)),
          ],
        ),
        ...(props.item.children || []).map((child) =>
          h(MenuRow, {
            item: child,
            depth: props.depth + 1,
            key: child.id,
          }),
        ),
      ]);
  },
});

async function load(group = 'nav') {
  currentGroup.value = group;
  payload.value = await fetchSystemMenus(group);
}

async function switchGroup(group: string) {
  await load(group);
}

onMounted(async () => {
  await load('nav');
});
</script>
