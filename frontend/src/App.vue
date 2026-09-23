  <template>
    <div class="app-wrapper">
      <AppHeader v-if="!isAdminPage" :isDark="theme === 'dark'" @toggle-theme="toggleTheme" />

      <main :class="{ 'admin-layout': isAdminPage, 'public-layout': !isAdminPage }">
        <router-view />
      </main>

      <AppLocation v-if="!isAdminPage && route.path !== '/contact'" />

      <AppFooter v-if="!isAdminPage" />
    </div>
  </template>

  <script setup>
  import { computed, ref, onMounted, watch } from "vue";
  import { useRoute } from "vue-router";

  import AppHeader from "./components/layout/AppHeader.vue";
  import AppFooter from "./components/layout/AppFooter.vue";
  import AppLocation from "./components/layout/AppLocation.vue";

  const route = useRoute();
  const isAdminPage = computed(() => route.path.startsWith("/admin"));

  const theme = ref(localStorage.getItem("theme") || "light");

  const applyTheme = (value) => {
    document.documentElement.setAttribute("data-theme", value);
    localStorage.setItem("theme", value);
  };

  onMounted(() => applyTheme(theme.value));

  watch(theme, (val) => applyTheme(val));

  // toggle function
  const toggleTheme = () => {
    theme.value = theme.value === "light" ? "dark" : "light";
  };
  </script>

  <style scoped>
  .app-wrapper {
    min-height: 100vh;
    background: var(--off-white);
    display: flex;
    flex-direction: column;
    transition: background-color 0.5s ease, color 0.5s ease;
  }

  main {
    flex: 1;
    position: relative;
    z-index: 1;
  }

  .admin-layout {
    background: #f5f7fb;
  }

  .public-layout {
    padding-top: 0;
  }

  /* Page Transitions */
  .page-enter-active,
  .page-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
  }

  .page-enter-from {
    opacity: 0;
    transform: translateY(10px);
  }

  .page-leave-to {
    opacity: 0;
    transform: translateY(-10px);
  }

  /* Custom Scrollbar for Premium Feel */
  ::-webkit-scrollbar {
    width: 8px;
  }

  ::-webkit-scrollbar-track {
    background: var(--gray-100);
  }

  ::-webkit-scrollbar-thumb {
    background: var(--gray-300);
    border-radius: 10px;
  }

  ::-webkit-scrollbar-thumb:hover {
    background: var(--secondary);
  }

  [data-theme="dark"] .app-wrapper {
    background: var(--primary-dark);
  }
  </style>
