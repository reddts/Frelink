<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">System / Groups</span>
        <h3>管理组</h3>
        <p>管理组列表已接入独立 adminapi，权限规则继续沿用旧权限节点语义。</p>
      </div>
      <label class="search-inline">
        <span>搜索</span>
        <input v-model.trim="keyword" placeholder="按组名称筛选" @keydown.enter="reload" />
      </label>
    </section>

    <section class="panel-grid">
      <article class="panel-card">
        <span class="eyebrow">系统组列表</span>
        <div class="quick-links group-list">
          <button
            v-for="item in payload?.list || []"
            :key="item.id"
            class="ghost-button group-card-button"
            :class="{ 'is-current': selectedId === item.id }"
            type="button"
            @click="selectGroup(item.id)"
          >
            <strong>{{ item.title }}</strong>
            <span>{{ item.status ? '启用' : '禁用' }} / {{ item.rule_count < 0 ? '全部规则' : `${item.rule_count} 条规则` }}</span>
          </button>
        </div>
      </article>

      <article class="panel-card">
        <span class="eyebrow">权限详情</span>
        <template v-if="detail">
          <h4>{{ detail.title }}</h4>
          <p>状态：{{ detail.status ? '启用' : '禁用' }}</p>
          <p>系统内置：{{ detail.system ? '是' : '否' }}</p>
          <div class="rule-tree">
            <RuleNode v-for="node in detail.rule_tree || []" :key="node.id" :node="node" />
          </div>
        </template>
        <p v-else>请选择左侧系统组查看权限树。</p>
      </article>
    </section>
  </div>
</template>

<script setup lang="ts">
import { defineComponent, h, onMounted, ref } from 'vue';
import { fetchSystemGroupDetail, fetchSystemGroups } from '@/api/admin';
import type { GroupRuleTreeNode, SystemGroupDetail, SystemGroupListPayload } from '@/types';

const payload = ref<SystemGroupListPayload | null>(null);
const detail = ref<SystemGroupDetail | null>(null);
const selectedId = ref<number>(0);
const keyword = ref('');

const RuleNode = defineComponent<{
  node: GroupRuleTreeNode;
}>({
  name: 'RuleNode',
  props: {
    node: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    return () =>
      h('div', { class: 'rule-node' }, [
        h('div', { class: 'rule-row' }, [
          h('span', props.node.state?.selected ? '✓' : '○'),
          h('span', props.node.text),
        ]),
        ...(props.node.children || []).map((child) =>
          h(RuleNode, {
            node: child,
            key: child.id,
          }),
        ),
      ]);
  },
});

async function reload() {
  payload.value = await fetchSystemGroups(keyword.value);
  if (!selectedId.value && payload.value.list.length > 0) {
    await selectGroup(payload.value.list[0].id);
  }
}

async function selectGroup(id: number) {
  selectedId.value = id;
  detail.value = await fetchSystemGroupDetail(id);
}

onMounted(async () => {
  await reload();
});
</script>
