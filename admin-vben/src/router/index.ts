import { createRouter, createWebHashHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

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
  const title = to.meta.title ? `${to.meta.title} - ${import.meta.env.VITE_APP_TITLE || 'Frelink Admin'}` : import.meta.env.VITE_APP_TITLE || 'Frelink Admin';
  document.title = title;
});
