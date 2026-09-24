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
  import { computed, ref, watch } from "vue";
  import { useRoute } from "vue-router";

  import AppHeader from "./components/layout/AppHeader.vue";
  import AppFooter from "./components/layout/AppFooter.vue";
  import AppLocation from "./components/layout/AppLocation.vue";

  const route = useRoute();
  const isAdminPage = computed(() => route.path.startsWith("/admin"));

  const theme = ref(localStorage.getItem("theme") || "light");

  const applyTheme = () => {
    // Admin panel dark rejimga moslanmagan — u yerda doim light
    const value = isAdminPage.value ? "light" : theme.value;
    // Bootstrap 5.3 ning o'z dark rejimi ham yoqilsin (forma, jadval, dropdown ranglari)
    document.documentElement.setAttribute("data-bs-theme", value);
    document.documentElement.setAttribute("data-theme", value);
  };

  // onMounted emas — birinchi chizishdan oldin qo'llanadi, oq "miltillash" bo'lmaydi
  applyTheme();

  watch([theme, isAdminPage], applyTheme);
  watch(theme, (val) => localStorage.setItem("theme", val));

  // toggle function
  const toggleTheme = () => {
    theme.value = theme.value === "light" ? "dark" : "light";
  };
  </script>

  <style scoped>
  .app-wrapper {
    min-height: 100vh;
    background: var(--clr-bg);
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
    background: var(--clr-faint);
  }

  ::-webkit-scrollbar-thumb {
    background: var(--clr-border);
    border-radius: 10px;
  }

  ::-webkit-scrollbar-thumb:hover {
    background: var(--clr-muted);
  }

  [data-theme="dark"] .app-wrapper {
    background: var(--clr-bg);
  }
  </style>
