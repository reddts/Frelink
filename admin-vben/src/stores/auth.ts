import { computed, ref } from 'vue';
import { defineStore } from 'pinia';
import { fetchAdminMe, fetchAdminMenu, loginAdmin, logoutAdmin } from '@/api/admin';
import type { AdminBootstrapPayload, AdminMenuItem, AdminProfile } from '@/types';

export const useAuthStore = defineStore('admin-auth', () => {
  const user = ref<AdminProfile | null>(null);
  const permissions = ref<string[]>([]);
  const menus = ref<AdminMenuItem[]>([]);
  const homePath = ref('/dashboard');
  const bootstrapped = ref(false);

  const isLoggedIn = computed(() => Boolean(user.value?.uid));

  function applyBootstrap(payload: AdminBootstrapPayload | { user: AdminProfile; permissions: string[]; menus?: AdminMenuItem[]; home?: { path: string } }) {
    user.value = payload.user;
    permissions.value = payload.permissions;
    menus.value = payload.menus ?? menus.value;
    homePath.value = payload.home?.path || '/dashboard';
    bootstrapped.value = true;
  }

  async function bootstrap() {
    try {
      const [profile, menuPayload] = await Promise.all([fetchAdminMe(), fetchAdminMenu()]);
      applyBootstrap({
        user: profile.user,
        permissions: profile.permissions,
        menus: menuPayload.menus,
        home: menuPayload.home,
      });
      return true;
    } catch (error) {
      reset();
      bootstrapped.value = true;
      return false;
    }
  }

  async function login(username: string, password: string) {
    const payload = await loginAdmin({ username, password });
    applyBootstrap(payload);
  }

  async function logout() {
    try {
      await logoutAdmin();
    } finally {
      reset();
      bootstrapped.value = true;
    }
  }

  function reset() {
    user.value = null;
    permissions.value = [];
    menus.value = [];
    homePath.value = '/dashboard';
  }

  function findMenuByPath(path: string) {
    const walk = (items: AdminMenuItem[]): AdminMenuItem | null => {
      for (const item of items) {
        if (item.path === path) {
          return item;
        }
        const matched = walk(item.children || []);
        if (matched) {
          return matched;
        }
      }
      return null;
    };

    return walk(menus.value);
  }

  return {
    user,
    permissions,
    menus,
    homePath,
    bootstrapped,
    isLoggedIn,
    bootstrap,
    login,
    logout,
    findMenuByPath,
  };
});
