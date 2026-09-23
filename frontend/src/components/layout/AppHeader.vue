<template>
  <header
    class="navbar-wrapper w-100 position-fixed top-0 start-0"
    :class="{ 'is-sticky': isScrolled, 'is-transparent': !isScrolled }"
    style="z-index: 1000"
  >
    <nav class="navbar-luxe d-none d-lg-block">
      <div
        class="d-flex align-items-center justify-content-between header-container"
      >
        <router-link
          to="/"
          class="d-flex align-items-center text-decoration-none logo-link"
        >
          <img
            :src="isScrolled && !isDark ? '/logotip.png' : '/logo-white.png'"
            alt="Clinika"
            class="header-logo"
          />
        </router-link>
        <ul
          class="list-unstyled d-flex align-items-center mb-0 flex-grow-1 justify-content-center header-menu"
        >
          <li
            v-for="menu in menuItems"
            :key="menu.path"
            class="nav-item-root position-relative"
          >
            <router-link
              v-if="!menu.hasDropdown"
              :to="menu.path"
              class="nav-link-premium d-flex align-items-center px-2.5 py-2 rounded-3 text-decoration-none fw-semibold"
              :class="{ 'active-link': isRouteActive(menu.path) }"
            >
              {{ menu.name }}
            </router-link>
            <div
              v-else
              class="nav-link-premium d-flex align-items-center gap-1 px-2.5 py-2 rounded-3 fw-semibold dropdown-trigger"
              :class="{ 'active-link': isRouteActive(menu.path) }"
              tabindex="0"
              role="button"
              aria-haspopup="true"
            >
              <span>{{ menu.name }}</span>
              <i class="bi bi-chevron-down dropdown-chevron"></i>
            </div>
            <div
              v-if="menu.hasDropdown"
              class="mega-menu-luxe rounded-4 shadow-lg"
              :class="`mega-menu-${menu.key}`"
            >
              <div class="mega-menu-inner p-4">
                <div
                  class="mega-header mb-4 pb-3 border-bottom d-flex align-items-center justify-content-between"
                >
                  <div class="d-flex align-items-center gap-2.5">
                    <span class="mega-badge rounded-pill px-3 py-1.5 fw-bold">
                      <i :class="getMenuMeta(menu.key).icon"></i>
                    </span>
                    <span class="fw-bold text-dark-theme fs-6">{{
                      getMenuMeta(menu.key).title
                    }}</span>
                  </div>
                  <router-link
                    :to="menu.path"
                    class="mega-view-all text-decoration-none small fw-bold d-flex align-items-center gap-1"
                  >
                    Barchasi <i class="bi bi-arrow-right"></i>
                  </router-link>
                </div>

                <div
                  v-if="getMegaData(menu.key).length === 0"
                  class="text-center text-secondary small py-3"
                >
                  Hozircha ma'lumot mavjud emas
                </div>

                <div v-else class="row g-3" :class="getGridClass(menu.key)">
                  <div
                    v-for="item in getMegaData(menu.key)"
                    :key="item.id || item.slug || item.fullPath"
                  >
                    <router-link
                      :to="
                        item.fullPath ||
                        menu.path + (item.slug ? '/' + item.slug : '')
                      "
                      class="mega-link-item d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none h-100"
                    >
                      <div
                        class="mega-icon-box rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                      >
                        <i :class="item.icon || 'bi bi-heart-pulse-fill'"></i>
                      </div>
                      <div class="overflow-hidden text-start">
                        <p class="mb-1 fw-bold mega-item-title text-truncate">
                          {{ item.title }}
                        </p>
                        <span class="mega-item-sub text-truncate d-block">
                          {{ item.subtitle || "Batafsil ma'lumot" }}
                        </span>
                      </div>
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </li>
        </ul>
        <div class="d-flex align-items-center gap-3">
          <button
            class="border-0 bg-transparent p-0 theme-toggle-btn"
            style="cursor: pointer"
            aria-label="Mavzuni o'zgartirish"
            @click="$emit('toggle-theme')"
          >
            <div class="toggle-track rounded-pill position-relative">
              <div
                class="toggle-thumb position-absolute rounded-circle d-flex align-items-center justify-content-center"
                :class="{ 'is-dark': isDark }"
              >
                <i
                  :class="isDark ? 'bi bi-moon-stars-fill' : 'bi bi-sun-fill'"
                ></i>
              </div>
            </div>
          </button>
          <a
            href="tel:+998781222244"
            aria-label="Qo'ng'iroq qilish: +998 78 122 22 44"
            class="btn btn-danger rounded-pill px-4 py-2 d-flex align-items-center gap-2 call-btn fw-bold shadow-sm"
          >
            <i class="bi bi-headset fs-5"></i>
            <span class="call-btn-text">+998 78 122 22 44</span>
          </a>
        </div>
      </div>
    </nav>
    <nav class="d-lg-none px-3 py-2">
      <div
        class="mobile-navbar-luxe rounded-pill px-3 py-2 d-flex align-items-center justify-content-between shadow-sm"
      >
        <router-link to="/" class="d-flex align-items-center">
          <img
            :src="isScrolled && !isDark ? '/logotip.png' : '/logo-white.png'"
            alt="Clinika"
            height="32"
          />
        </router-link>
        <div class="d-flex align-items-center gap-2">
          <button
            class="mobile-btn-circle rounded-circle border-0 d-flex align-items-center justify-content-center"
            aria-label="Mavzuni o'zgartirish"
            @click="$emit('toggle-theme')"
          >
            <i :class="isDark ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill'"></i>
          </button>
          <button
            class="mobile-btn-circle rounded-circle border-0 d-flex align-items-center justify-content-center"
            aria-label="Menuni ochish"
            @click="openSidebar"
          >
            <i class="bi bi-list fs-4"></i>
          </button>
        </div>
      </div>
    </nav>
  </header>
  <Transition name="fade">
    <div
      v-if="isSidebarOpen"
      class="sidebar-backdrop position-fixed top-0 start-0 w-100 h-100"
      style="z-index: 99998"
      @click="closeSidebar"
    ></div>
  </Transition>

  <Transition name="slide">
    <div
      v-if="isSidebarOpen"
      class="mobile-sidebar-luxe position-fixed top-0 end-0 h-100 d-flex flex-column shadow-lg"
      style="z-index: 99999; width: 85vw; max-width: 320px"
    >
      <div
        class="sidebar-header-luxe d-flex align-items-center justify-content-between px-4 py-3 border-bottom"
      >
        <img src="/logotip.png" alt="Clinika" height="34" />
        <button
          class="btn-close-luxe rounded-circle border-0 d-flex align-items-center justify-content-center"
          @click="closeSidebar"
        >
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <div class="flex-grow-1 overflow-y-auto px-3 py-3">
        <template v-for="menu in menuItems" :key="menu.path">
          <!-- Oddiy havola (dropdownsiz) -->
          <router-link
            v-if="!menu.hasDropdown"
            :to="menu.path"
            class="mobile-main-link d-block px-3 py-2.5 rounded-3 fw-semibold text-decoration-none mb-1"
            :class="{ active: isRouteActive(menu.path) }"
            @click="closeSidebar"
          >
            {{ menu.name }}
          </router-link>

          <!-- Dropdown (accordion) -->
          <div v-else class="mobile-accordion-item mb-1">
            <button
              type="button"
              class="mobile-accordion-trigger w-100 d-flex align-items-center justify-content-between px-3 py-2.5 rounded-3 bg-transparent border-0 fw-semibold"
              :class="{ active: isRouteActive(menu.path) }"
              @click="toggleMobileMenu(menu.key)"
            >
              <span>{{ menu.name }}</span>
              <i
                class="bi chevron-icon"
                :class="
                  activeMobileMenu === menu.key
                    ? 'bi-chevron-up rotated'
                    : 'bi-chevron-down'
                "
              ></i>
            </button>

            <div
              class="mobile-accordion-content"
              :class="{ show: activeMobileMenu === menu.key }"
            >
              <div class="mobile-sub-wrapper ps-2 pe-1 py-1">
                <!-- Dropdown boshidagi Barcha ... tugmasi -->
                <router-link
                  :to="menu.path"
                  class="mobile-sub-link mobile-all-link d-flex align-items-center justify-content-between px-3 py-2 rounded-3 text-decoration-none mb-1 fw-bold"
                  @click="closeSidebar"
                >
                  <span>Barcha {{ menu.name.toLowerCase() }}</span>
                  <i class="bi bi-arrow-right-short fs-5"></i>
                </router-link>

                <!-- Qolgan kontentlar -->
                <router-link
                  v-for="item in getMegaData(menu.key)"
                  :key="item.id || item.slug || item.fullPath"
                  :to="
                    item.fullPath ||
                    menu.path + (item.slug ? '/' + item.slug : '')
                  "
                  class="mobile-sub-link d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-decoration-none mb-1"
                  @click="closeSidebar"
                >
                  <i
                    :class="item.icon || 'bi bi-dot'"
                    class="sub-icon text-primary"
                  ></i>
                  <span class="text-truncate">{{ item.title }}</span>
                </router-link>
              </div>
            </div>
          </div>
        </template>
      </div>

      <div class="p-4 border-top">
        <a
          href="tel:+998781222244"
          class="btn btn-danger w-100 rounded-pill py-2.5 d-flex align-items-center justify-content-center gap-2 fw-bold"
        >
          <i class="bi bi-headset"></i>
          <span>+998 78 122 22 44</span>
        </a>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRoute } from "vue-router";
import { useServicesStore } from "../../stores/services";
import api, { unwrap } from "../../services/api";

defineProps({ isDark: { type: Boolean, default: false } });
defineEmits(["toggle-theme"]);

const route = useRoute();

const isScrolled = ref(false);

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20;
};

onMounted(() => {
  handleScroll();
  window.addEventListener("scroll", handleScroll, { passive: true });
  loadMegaMenuData();
});

onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll);
});

const isRouteActive = (path) => {
  if (path === "/") return route.path === "/";
  return route.path.startsWith(path);
};

const menuItems = [
  { name: "Bosh sahifa", path: "/", key: "home" },
  { name: "Biz haqimizda", path: "/about", key: "about" },
  { name: "Xizmatlar", path: "/services", key: "services", hasDropdown: true },
  { name: "Shifokorlar", path: "/doctors", key: "doctors", hasDropdown: true },
  {
    name: "Operatsion bo'lim",
    path: "/surgeries",
    key: "surgeries",
    hasDropdown: true,
  },
  { name: "Statsionar", path: "/statsionar", key: "statsionar" },
  { name: "Yangiliklar", path: "/news", key: "news" },
  { name: "Bog'lanish", path: "/contact", key: "contact" },
];

const servicesStore = useServicesStore();
const specializations = ref([]);
const surgeries = ref([]);

const loadMegaMenuData = async () => {
  if (servicesStore.services.length === 0) {
    await servicesStore.fetchServices();
  }

  try {
    const data = await api.get("/specializations").then(unwrap);
    specializations.value = Array.isArray(data) ? data : data?.data || [];
  } catch (err) {
    console.error("Specializations xatosi:", err);
    specializations.value = [];
  }

  try {
    const res = await api.get("/surgeries").then(unwrap);
    const rawList = res?.data?.data || res?.data || res || [];
    const list = Array.isArray(rawList) ? rawList : [];

    surgeries.value = list.filter((s) => s && (s.slug || s.id));
  } catch (err) {
    console.error("Surgeries xatosi:", err);
    surgeries.value = [];
  }
};

const megaServices = computed(() =>
  servicesStore.services.map((s) => ({
    id: s.id,
    slug: s.slug,
    title: s.name,
    subtitle: s.short_description || "Batafsil ma'lumot",
    icon: "bi bi-heart-pulse-fill",
    fullPath: "/services/" + (s.slug || s.id),
  }))
);

const megaDoctors = computed(() =>
  specializations.value.map((sp) => ({
    id: sp.id,
    slug: sp.slug,
    title: sp.name,
    subtitle: "Shifokorlarni ko'rish",
    icon: "bi bi-person-badge-fill",
    fullPath: "/doctors?specialization=" + (sp.slug || sp.id),
  }))
);

const megaSurgery = computed(() =>
  surgeries.value.map((s) => ({
    id: s.id,
    slug: s.slug,
    title: s.name,
    subtitle: s.short_description || "Batafsil ma'lumot",
    icon: "bi bi-hospital-fill",
    fullPath: "/surgeries/" + (s.slug || s.id),
  }))
);

const getMegaData = (key) => {
  if (key === "services") return megaServices.value;
  if (key === "doctors") return megaDoctors.value;
  if (key === "surgeries") return megaSurgery.value;
  return [];
};

const getMenuMeta = (key) => {
  if (key === "services")
    return { title: "Tibbiy Xizmatlar", icon: "bi bi-grid-fill text-primary" };
  if (key === "doctors")
    return {
      title: "Shifokorlar va Mutaxassislar",
      icon: "bi bi-people-fill text-primary",
    };
  if (key === "surgeries")
    return {
      title: "Operatsion va Jarrohlik",
      icon: "bi bi-hospital-fill text-primary",
    };
  return { title: "", icon: "" };
};

const getGridClass = (key) => {
  if (key === "doctors") return "row-cols-3";
  if (key === "services") return "row-cols-3";
  if (key === "surgeries") return "row-cols-3";
  return "row-cols-2";
};

const isSidebarOpen = ref(false);
const activeMobileMenu = ref(null);

const toggleMobileMenu = (key) => {
  activeMobileMenu.value = activeMobileMenu.value === key ? null : key;
};
const openSidebar = () => {
  isSidebarOpen.value = true;
  document.body.style.overflow = "hidden";
};
const closeSidebar = () => {
  isSidebarOpen.value = false;
  document.body.style.overflow = "";
};
</script>

<style scoped>
.navbar-wrapper {
  transition: background-color 0.35s ease, box-shadow 0.35s ease,
    backdrop-filter 0.35s ease, padding 0.35s ease;
  will-change: background-color, box-shadow, backdrop-filter;
}

.header-container {
  height: 74px;
  max-width: 1430px;
  width: 100%;
  margin: 0 auto;
  padding: 0 3rem;
  gap: 1rem;
}

.header-menu {
  gap: 1.5rem;
  min-width: 0;
}

.call-btn {
  white-space: nowrap;
}

/* Noutbuklar (992–1399px): menyu siqilmasin, tugma ekrandan chiqmasin */
@media (max-width: 1399.98px) {
  .header-container {
    padding: 0 1.5rem;
  }
  .header-menu {
    gap: 0.5rem;
  }
}

@media (max-width: 1199.98px) {
  .call-btn {
    padding-left: 0.75rem !important;
    padding-right: 0.75rem !important;
  }
  .call-btn-text {
    display: none;
  }
}

.header-logo {
  height: 42px;
  transition: transform 0.25s ease;
}

.logo-link:hover .header-logo {
  transform: scale(1.03);
}

.navbar-wrapper.is-transparent {
  background: transparent !important;
  box-shadow: none !important;
  backdrop-filter: none !important;
}

.navbar-wrapper.is-sticky {
  background: rgba(255, 255, 255, 0.92) !important;
  backdrop-filter: blur(16px) saturate(180%);
  -webkit-backdrop-filter: blur(16px) saturate(180%);
  box-shadow: 0 4px 24px rgba(0, 30, 80, 0.08) !important;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

[data-theme="dark"] .navbar-wrapper.is-sticky {
  background: rgba(15, 23, 42, 0.92) !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.07);
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.45) !important;
}

.nav-link-premium {
  color: rgba(255, 255, 255, 0.95);
  font-size: 0.88rem;
  white-space: nowrap;
  position: relative;
  transition: color 0.25s ease, background-color 0.25s ease;
  padding-bottom: 4px;
  cursor: pointer;
  user-select: none;
}

.dropdown-trigger {
  cursor: pointer;
}

.navbar-wrapper.is-sticky .nav-link-premium {
  color: #1e293b;
}

[data-theme="dark"] .nav-link-premium,
[data-theme="dark"] .navbar-wrapper.is-sticky .nav-link-premium {
  color: #f1f5f9;
}

[data-theme="dark"] .nav-link-premium:hover,
[data-theme="dark"] .navbar-wrapper.is-sticky .nav-link-premium:hover {
  color: #38bdf8;
  background: rgba(56, 189, 248, 0.12);
}

.nav-link-premium::after {
  content: "";
  position: absolute;
  bottom: 2px;
  left: 0;
  right: 0;
  height: 2px;
  border-radius: 2px;
  background-color: #ef4444;
  transform: scaleX(0);
  transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.nav-link-premium:hover::after,
.nav-link-premium.active-link::after {
  transform: scaleX(1);
}

.navbar-wrapper.is-sticky .nav-link-premium.active-link {
  color: #0284c7;
}

[data-theme="dark"] .nav-link-premium.active-link {
  color: #38bdf8;
}

.dropdown-chevron {
  font-size: 0.7rem;
  transition: transform 0.25s ease;
}

.nav-item-root:hover .dropdown-chevron {
  transform: rotate(180deg);
}

.nav-item-root {
  padding-top: 10px;
  padding-bottom: 10px;
}

.mega-menu-luxe {
  position: absolute;
  top: 100%;
  left: 50%;
  transform: translateX(-50%) translateY(10px);
  width: 1200px;
  max-width: 95vw;
  background: #ffffff;
  border: 1px solid rgba(226, 232, 240, 0.8);
  border-radius: 20px;
  opacity: 0;
  visibility: hidden;
  pointer-events: none;
  transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1),
    transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.2s;
  box-shadow: 0 24px 60px rgba(0, 30, 80, 0.12) !important;
  z-index: 10000;
}

.mega-menu-services,
.mega-menu-doctors,
.mega-menu-surgery {
  width: 1000px;
}

.mega-menu-luxe::before {
  content: "";
  position: absolute;
  top: -20px;
  left: 0;
  right: 0;
  height: 20px;
}

.nav-item-root:hover .mega-menu-luxe {
  opacity: 1;
  visibility: visible;
  pointer-events: auto;
  transform: translateX(-50%) translateY(0);
}

[data-theme="dark"] .mega-menu-luxe {
  background: #0f172a;
  border-color: rgba(255, 255, 255, 0.08);
  box-shadow: 0 24px 60px rgba(0, 0, 0, 0.55) !important;
}

.text-dark-theme {
  color: #0f172a;
}

[data-theme="dark"] .text-dark-theme {
  color: #f8fafc;
}

.mega-badge {
  background: rgba(2, 132, 199, 0.1);
  color: #0284c7;
  font-size: 0.9rem;
}

.mega-view-all {
  color: #0284c7;
  transition: color 0.2s;
}

.mega-view-all:hover {
  color: #0369a1;
}

.mega-link-item {
  color: #334155;
  background: rgba(248, 250, 252, 0.6);
  border: 1px solid rgba(0, 0, 0, 0.03);
  transition: background 0.2s ease, transform 0.15s ease, color 0.2s ease,
    border-color 0.2s ease, box-shadow 0.2s ease;
}

[data-theme="dark"] .mega-link-item {
  color: #cbd5e1;
  background: rgba(255, 255, 255, 0.02);
  border-color: rgba(255, 255, 255, 0.05);
}

.mega-link-item:hover {
  background: #ffffff;
  border-color: rgba(2, 132, 199, 0.2);
  transform: translateY(-2px);
  color: #0284c7;
  box-shadow: 0 8px 20px rgba(2, 132, 199, 0.08) !important;
}

[data-theme="dark"] .mega-link-item:hover {
  background: rgba(30, 41, 59, 0.9);
  border-color: rgba(56, 189, 248, 0.3);
  color: #38bdf8;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3) !important;
}

.mega-icon-box {
  width: 44px;
  height: 44px;
  min-width: 44px;
  background: rgba(2, 132, 199, 0.08);
  color: #0284c7;
  font-size: 1.2rem;
  border-radius: 12px;
  transition: background 0.2s, color 0.2s;
}

[data-theme="dark"] .mega-icon-box {
  background: rgba(56, 189, 248, 0.12);
  color: #38bdf8;
}

.mega-link-item:hover .mega-icon-box {
  background: #0284c7;
  color: #ffffff;
}

.mega-item-title {
  font-size: 0.95rem;
  color: #0f172a;
  margin-bottom: 2px;
}

[data-theme="dark"] .mega-item-title {
  color: #f1f5f9;
}

.mega-item-sub {
  font-size: 0.78rem;
  color: #64748b;
  line-height: 1.35;
}

[data-theme="dark"] .mega-item-sub {
  color: #94a3b8;
}

.toggle-track {
  width: 48px;
  height: 26px;
  background: rgba(255, 255, 255, 0.25);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.navbar-wrapper.is-sticky .toggle-track {
  background: rgba(0, 0, 0, 0.08);
  border-color: rgba(0, 0, 0, 0.05);
}

[data-theme="dark"] .toggle-track {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.1);
}

.toggle-thumb {
  top: 2px;
  left: 2px;
  width: 20px;
  height: 20px;
  background: white;
  color: #f59e0b;
  font-size: 11px;
  transition: left 0.2s ease, background 0.2s ease;
}

.toggle-thumb.is-dark {
  left: 24px;
  background: #0f172a;
  color: #fcd34d;
}

.call-btn {
  font-size: 0.88rem;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.call-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(220, 38, 38, 0.3) !important;
}

.mobile-navbar-luxe {
  transition: background-color 0.35s ease, border-color 0.35s ease,
    box-shadow 0.35s ease, backdrop-filter 0.35s ease;
  will-change: background-color, border-color, box-shadow;
}

.mobile-btn-circle {
  width: 36px;
  height: 36px;
  transition: background-color 0.25s ease, color 0.25s ease;
}

.navbar-wrapper.is-transparent .mobile-navbar-luxe {
  background: transparent !important;
  backdrop-filter: none !important;
  -webkit-backdrop-filter: none !important;
  border: 1px solid transparent !important;
  box-shadow: none !important;
}

.navbar-wrapper.is-transparent .mobile-btn-circle {
  background: rgba(255, 255, 255, 0.2);
  color: #ffffff;
}

.navbar-wrapper.is-sticky .mobile-navbar-luxe {
  background: rgba(255, 255, 255, 0.95) !important;
  backdrop-filter: blur(16px) saturate(180%);
  -webkit-backdrop-filter: blur(16px) saturate(180%);
  border: 1px solid rgba(0, 0, 0, 0.08);
  box-shadow: 0 4px 20px rgba(0, 30, 80, 0.08) !important;
}

.navbar-wrapper.is-sticky .mobile-btn-circle {
  background: rgba(0, 0, 0, 0.06);
  color: #1e293b;
}

[data-theme="dark"] .navbar-wrapper.is-sticky .mobile-navbar-luxe {
  background: rgba(15, 23, 42, 0.95) !important;
  border-color: rgba(255, 255, 255, 0.1);
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.45) !important;
}

[data-theme="dark"] .navbar-wrapper.is-sticky .mobile-btn-circle {
  background: rgba(255, 255, 255, 0.1);
  color: #f1f5f9;
}

.sidebar-backdrop {
  background: rgba(10, 20, 40, 0.45);
  backdrop-filter: blur(4px);
}

.mobile-sidebar-luxe {
  background: #ffffff;
}

[data-theme="dark"] .mobile-sidebar-luxe {
  background: #0f172a;
}

.btn-close-luxe {
  width: 34px;
  height: 34px;
  background: rgba(0, 0, 0, 0.05);
  color: #334155;
  transition: background 0.2s;
}

[data-theme="dark"] .btn-close-luxe {
  background: rgba(255, 255, 255, 0.08);
  color: #cbd5e1;
}

/* Mobile Links & Accordion Styles */
.mobile-main-link,
.mobile-accordion-trigger {
  color: #334155;
  font-size: 0.95rem;
  transition: background 0.2s, color 0.2s;
}

.mobile-main-link:hover,
.mobile-accordion-trigger:hover {
  background: rgba(2, 132, 199, 0.05);
}

[data-theme="dark"] .mobile-main-link,
[data-theme="dark"] .mobile-accordion-trigger {
  color: #e2e8f0;
}

[data-theme="dark"] .mobile-main-link:hover,
[data-theme="dark"] .mobile-accordion-trigger:hover {
  background: rgba(56, 189, 248, 0.1);
}

.mobile-main-link.active,
.mobile-accordion-trigger.active {
  color: #0284c7;
  background: rgba(2, 132, 199, 0.08);
  font-weight: 700;
}

[data-theme="dark"] .mobile-main-link.active,
[data-theme="dark"] .mobile-accordion-trigger.active {
  color: #38bdf8;
  background: rgba(56, 189, 248, 0.15);
}

.chevron-icon {
  font-size: 0.8rem;
  transition: transform 0.25s ease;
}

.mobile-accordion-content {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.mobile-accordion-content.show {
  max-height: 450px;
  overflow-y: auto;
}

.mobile-sub-wrapper {
  border-left: 2px solid rgba(2, 132, 199, 0.2);
  margin-left: 12px;
}

[data-theme="dark"] .mobile-sub-wrapper {
  border-left-color: rgba(56, 189, 248, 0.2);
}

.mobile-sub-link {
  color: #475569;
  font-size: 0.88rem;
  transition: background 0.15s, color 0.15s;
}

[data-theme="dark"] .mobile-sub-link {
  color: #94a3b8;
}

.mobile-sub-link:hover {
  background: rgba(2, 132, 199, 0.06);
  color: #0284c7;
}

[data-theme="dark"] .mobile-sub-link:hover {
  background: rgba(56, 189, 248, 0.1);
  color: #38bdf8;
}

.mobile-all-link {
  color: #0284c7;
  background: rgba(2, 132, 199, 0.08);
}

[data-theme="dark"] .mobile-all-link {
  color: #38bdf8;
  background: rgba(56, 189, 248, 0.12);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.slide-enter-from,
.slide-leave-to {
  transform: translateX(100%);
}
</style>
