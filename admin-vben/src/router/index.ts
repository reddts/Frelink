import { createRouter, createWebHashHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useLoadingStore } from '@/stores/loading';

const routes = [
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/LoginView.vue'),
    meta: { public: true, title: '登录' },
  },
  {
    path: '/',
    component: () => import('@/layouts/AdminLayout.vue'),
    children: [
      {
        path: '',
        redirect: '/dashboard',
      },
      {
        path: 'dashboard',
        name: 'dashboard',
        component: () => import('@/views/DashboardView.vue'),
        meta: { title: '仪表盘' },
      },
      {
        path: 'content/articles',
        name: 'content-articles',
        component: () => import('@/views/ContentArticlesView.vue'),
        meta: { title: '文章管理' },
      },
      {
        path: 'content/questions',
        name: 'content-questions',
        component: () => import('@/views/ContentQuestionsView.vue'),
        meta: { title: '问题管理' },
      },
      {
        path: 'content/answers',
        name: 'content-answers',
        component: () => import('@/views/ContentAnswersView.vue'),
        meta: { title: '回答管理' },
      },
      {
        path: 'content/approvals',
        name: 'content-approvals',
        component: () => import('@/views/ContentApprovalsView.vue'),
        meta: { title: '内容审核' },
      },
      {
        path: 'system/auths',
        name: 'system-auths',
        component: () => import('@/views/SystemAuthsView.vue'),
        meta: { title: '权限节点' },
      },
      {
        path: 'system/menus',
        name: 'system-menus',
        component: () => import('@/views/SystemMenusView.vue'),
        meta: { title: '菜单管理' },
      },
      {
        path: 'system/groups',
        name: 'system-groups',
        component: () => import('@/views/SystemGroupsView.vue'),
        meta: { title: '管理组' },
      },
      {
        path: 'system/configs',
        name: 'system-configs',
        component: () => import('@/views/SystemConfigsView.vue'),
        meta: { title: '系统配置' },
      },
      {
        path: 'system/users',
        name: 'system-users',
        component: () => import('@/views/SystemUsersView.vue'),
        meta: { title: '用户管理' },
      },
      {
        path: 'legacy/:segments(.*)*',
        name: 'legacy',
        component: () => import('@/views/LegacyPageView.vue'),
        meta: { title: '迁移占位页' },
      },
      {
        path: '401',
        name: '401',
        component: () => import('@/views/UnauthorizedView.vue'),
        meta: { title: '无权限' },
      },
      {
        path: ':pathMatch(.*)*',
        name: '404',
        component: () => import('@/views/NotFoundView.vue'),
        meta: { title: '页面不存在' },
      },
    ],
  },
];

export const router = createRouter({
  history: createWebHashHistory('/admin-vben/'),
  routes,
});

router.beforeEach(async (to) => {
  const auth = useAuthStore();
  const loading = useLoadingStore();
  loading.start(to.meta.public ? '正在进入页面...' : '正在加载管理端...');

  if (!auth.bootstrapped) {
    await auth.bootstrap();
  }

  if (to.meta.public) {
    if (auth.isLoggedIn && to.path === '/login') {
      return auth.homePath || '/dashboard';
    }
    return true;
  }

  if (!auth.isLoggedIn) {
    return {
      path: '/login',
      query: {
        redirect: to.fullPath,
      },
    };
  }

  return true;
});

router.afterEach((to) => {
  const loading = useLoadingStore();
  const title = to.meta.title ? `${to.meta.title} - ${import.meta.env.VITE_APP_TITLE || 'Frelink Admin'}` : import.meta.env.VITE_APP_TITLE || 'Frelink Admin';
  document.title = title;
  loading.finish();
});

router.onError(() => {
  const loading = useLoadingStore();
  loading.reset();
});
