<template>
  <div class="admin-wrapper d-flex min-vh-100">
    <!-- Mobil uchun orqa fon backdrop -->
    <div
      v-if="!isCollapse && isMobile"
      class="sidebar-backdrop"
      @click="isCollapse = true"
    ></div>

    <!-- Glassmorphism Sidebar -->
    <aside class="admin-sidebar" :class="{ collapsed: isCollapse }">
      <!-- Logo Bo'limi -->
      <div class="sidebar-header">
        <router-link to="/admin" class="logo-wrapper">
          <!-- Sidebar holatiga qarab logotip o'zgarishi -->
          <img
            :src="isCollapse ? '/logo-bg-remove.png' : '/logotip.png'"
            :alt="isCollapse ? 'Mini Logo' : 'Logo'"
            class="logo-img"
            :class="{ 'mini-logo': isCollapse }"
          />
        </router-link>
      </div>

      <!-- Navigatsiya Menyusi -->
      <nav class="sidebar-nav">
        <ul class="nav">
          <li class="nav-item" v-for="item in menuItems" :key="item.path">
            <router-link
              :to="item.path"
              class="nav-link-glass"
              :class="{ active: isActive(item) }"
              @click="closeSidebarOnMobile"
            >
              <i :class="item.icon"></i>
              <span v-if="!isCollapse" class="nav-title">{{ item.title }}</span>

              <!-- Sidebar yopiq bo'lganda chiqadigan Tooltip -->
              <div v-if="isCollapse && !isMobile" class="nav-tooltip">
                {{ item.title }}
              </div>
            </router-link>
          </li>
        </ul>
      </nav>

      <!-- Sidebar Footer (Chiqish) -->
      <div class="sidebar-footer">
        <button
          class="logout-btn-glass"
          @click="handleLogout"
          :title="isCollapse ? 'Tizimdan chiqish' : ''"
        >
          <i class="fas fa-sign-out-alt"></i>
          <span v-if="!isCollapse" class="logout-text">Tizimdan chiqish</span>
        </button>
      </div>
    </aside>

    <!-- Asosiy Kontent Bo'limi -->
    <div class="main-container">
      <!-- Topbar -->
      <header class="admin-topbar">
        <div class="topbar-left">
          <button
            class="toggle-btn-glass"
            @click="isCollapse = !isCollapse"
            title="Menyuni ochish/yopish"
          >
            <i class="fas" :class="isCollapse ? 'fa-indent' : 'fa-outdent'"></i>
          </button>
          <h4 class="page-title">{{ currentRouteName }}</h4>
        </div>

        <div class="topbar-right">
          <div class="admin-profile">
            <div class="text-end d-none d-sm-block me-3">
              <div class="admin-name">{{ adminUser.name || "Administrator" }}</div>
              <div class="admin-role">{{ adminUser.email || "Admin" }}</div>
            </div>
            <div class="admin-avatar">
              <img
                :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(adminUser.name || 'Admin')}&background=0284c7&color=fff`"
                alt="Avatar"
              />
            </div>
          </div>
        </div>
      </header>

      <!-- Sahifa Mazmuni (Router View) -->
      <main class="admin-content">
        <div class="content-viewport">
          <router-view />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "../../services/api";

const adminUser = (() => {
  try {
    return JSON.parse(localStorage.getItem("admin_user") || "{}") || {};
  } catch {
    return {};
  }
})();

const isCollapse = ref(false);
const isMobile = ref(false);
const route = useRoute();
const router = useRouter();

// Dinamik admin path prefixini aniqlaymiz (masalan: /admin yoki /custom-admin)
const adminPrefix = `/${import.meta.env.VITE_ADMIN_PATH || "admin"}`;

// Router'dan showInMenu: true bo'lgan bolalarni olib kelib menyu yasaymiz
const menuItems = computed(() => {
  const adminRoute = router.options.routes.find(
    (r) => r.meta && r.meta.requiresAdmin
  );
  if (!adminRoute || !adminRoute.children) return [];

  return adminRoute.children
    .filter((child) => child.meta && child.meta.showInMenu)
    .map((child) => {
      const fullPath =
        child.path === "" ? adminPrefix : `${adminPrefix}/${child.path}`;
      return {
        path: fullPath,
        title: child.meta.title,
        icon: child.meta.icon,
      };
    });
});

// Topbar uchun sahifa sarlavhasi
const currentRouteName = computed(() => {
  return route.meta?.title || "Admin Panel";
});

// Faol (Active) menyuni aniqlash
const isActive = (item) => {
  if (item.path === adminPrefix) {
    return route.path === adminPrefix;
  }
  return route.path.startsWith(item.path);
};

const checkScreenSize = () => {
  isMobile.value = window.innerWidth <= 992;
  if (isMobile.value) {
    isCollapse.value = true;
  }
};

const closeSidebarOnMobile = () => {
  if (isMobile.value) {
    isCollapse.value = true;
  }
};

const handleLogout = async () => {
  if (!confirm("Tizimdan chiqishni xohlaysizmi?")) return;
  try {
    // Tokenni serverda ham bekor qilamiz — aks holda o'g'irlangan token amal qilishda davom etadi
    await api.post("/auth/logout");
  } catch {
    // Token allaqachon yaroqsiz bo'lsa ham chiqishni davom ettiramiz
  }
  localStorage.removeItem("admin_token");
  localStorage.removeItem("admin_user");
  router.push({ name: "admin-login" });
};

onMounted(() => {
  checkScreenSize();
  window.addEventListener("resize", checkScreenSize);
});

onUnmounted(() => {
  window.removeEventListener("resize", checkScreenSize);
});
</script>

<style scoped>
/* =========================
   BACKGROUND & LAYOUT
========================= */
.admin-wrapper {
  background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 40%, #e0e7ff 100%);
  min-height: 100vh;
  color: #1e293b;
  padding: 12px;
  gap: 16px;
  position: relative;
  font-family: "Inter", system-ui, -apple-system, sans-serif;
}

/* =========================
   GLASS SIDEBAR
========================= */
.admin-sidebar {
  width: 250px;
  background: rgba(255, 255, 255, 0.55);
  backdrop-filter: blur(20px) saturate(180%);
  -webkit-backdrop-filter: blur(20px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.7);
  border-radius: 20px;
  display: flex;
  flex-direction: column;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 1000;
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.06);
  flex-shrink: 0;
}

.admin-sidebar.collapsed {
  width: 78px;
}

/* Logo container va almashish animations */
.sidebar-header {
  height: 72px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 12px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.4);
}

.logo-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  text-decoration: none;
}

.logo-img {
  max-width: 100%;
  max-height: 46px;
  object-fit: contain;
  transition: all 0.3s ease;
}

.logo-img.mini-logo {
  max-height: 38px;
  max-width: 38px;
}

.sidebar-nav {
  padding: 14px 0;
  flex-grow: 1;
  overflow-y: auto;
}

.nav {
  display: flex;
  flex-direction: column;
  gap: 6px;
  padding: 0 10px;
  list-style: none;
  margin: 0;
}

.nav-link-glass {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 11px 14px;
  color: #475569;
  text-decoration: none;
  border-radius: 12px;
  transition: all 0.25s ease;
  position: relative;
  font-weight: 600;
  font-size: 0.9rem;
  white-space: nowrap;
}

.admin-sidebar.collapsed .nav-link-glass {
  justify-content: center;
  padding: 11px 0;
}

.nav-link-glass i {
  font-size: 1.15rem;
  width: 22px;
  text-align: center;
  flex-shrink: 0;
}

.nav-title {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.nav-link-glass:hover {
  background: rgba(255, 255, 255, 0.7);
  color: #0284c7;
}

.nav-link-glass.active {
  background: #0284c7 !important;
  color: #ffffff !important;
  box-shadow: 0 6px 16px rgba(2, 132, 199, 0.35);
}

/* Tooltip (Sidebar yopiq bo'lgandagi ma'lumot) */
.nav-tooltip {
  position: absolute;
  left: 100%;
  top: 50%;
  transform: translateY(-50%) translateX(10px);
  background: rgba(15, 23, 42, 0.88);
  backdrop-filter: blur(8px);
  color: #ffffff;
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 0.82rem;
  font-weight: 600;
  white-space: nowrap;
  opacity: 0;
  visibility: hidden;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 1050;
  pointer-events: none;
}

.nav-link-glass:hover .nav-tooltip {
  opacity: 1;
  visibility: visible;
  transform: translateY(-50%) translateX(14px);
}

.sidebar-footer {
  padding: 14px 10px;
  border-top: 1px solid rgba(255, 255, 255, 0.4);
}

.logout-btn-glass {
  width: 100%;
  padding: 11px;
  border: 1px solid rgba(227, 30, 36, 0.2);
  background: rgba(227, 30, 36, 0.08);
  color: #e31e24;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  font-weight: 600;
  font-size: 0.9rem;
  transition: all 0.25s ease;
  white-space: nowrap;
  cursor: pointer;
}

.admin-sidebar.collapsed .logout-btn-glass {
  padding: 11px 0;
}

.logout-btn-glass i {
  font-size: 1.1rem;
  flex-shrink: 0;
}

.logout-btn-glass:hover {
  background: #e31e24;
  color: #ffffff;
  box-shadow: 0 6px 16px rgba(227, 30, 36, 0.25);
}

/* =========================
   MAIN CONTAINER & TOPBAR
========================= */
.main-container {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
  gap: 16px;
}

.admin-topbar {
  height: 72px;
  background: rgba(255, 255, 255, 0.55);
  backdrop-filter: blur(20px) saturate(180%);
  -webkit-backdrop-filter: blur(20px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.7);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 24px;
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.04);
}

.topbar-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.toggle-btn-glass {
  border: 1px solid rgba(255, 255, 255, 0.8);
  background: rgba(255, 255, 255, 0.7);
  color: #475569;
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  cursor: pointer;
}

.toggle-btn-glass:hover {
  background: #ffffff;
  color: #0284c7;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.page-title {
  margin: 0;
  font-size: 1.2rem;
  font-weight: 800;
  color: #0f172a;
}

.admin-profile {
  display: flex;
  align-items: center;
}

.admin-name {
  font-size: 0.9rem;
  font-weight: 700;
  color: #0f172a;
}

.admin-role {
  font-size: 0.75rem;
  font-weight: 600;
  color: #64748b;
}

.admin-avatar {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  overflow: hidden;
  border: 2px solid rgba(255, 255, 255, 0.9);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

.admin-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.admin-content {
  flex-grow: 1;
  overflow-y: auto;
}

.content-viewport {
  max-width: 1400px;
  margin: 0 auto;
}

/* Mobile Backdrop */
.sidebar-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.4);
  backdrop-filter: blur(4px);
  z-index: 999;
}

/* Responsive styles */
@media (max-width: 992px) {
  .admin-wrapper {
    padding: 8px;
    gap: 8px;
  }

  .admin-sidebar {
    position: fixed;
    top: 8px;
    bottom: 8px;
    left: 8px;
    height: calc(100vh - 16px);
    width: 250px !important;
    transform: translateX(0);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
  }

  .admin-sidebar.collapsed {
    transform: translateX(calc(-100% - 20px));
  }
}
</style>
