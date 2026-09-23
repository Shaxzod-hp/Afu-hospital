<template>
  <section class="about-page container-fluid px-3 px-md-5 py-4 py-md-5">
    <!-- TOP SECTION -->
    <div
      class="d-flex flex-row justify-content-between align-items-center gap-2 mb-4 mb-md-5 pb-3 border-bottom"
    >
      <div>
        <h1 class="name m-0">Klinika haqida</h1>
      </div>

      <router-link
        to="/about"
        class="text-danger text-decoration-none fw-bold text-danger-hover fs-5 shrink-none"
      >
        Batafsil
        <i class="bi bi-arrow-up-right"></i>
      </router-link>
    </div>

    <!-- GRID SECTION -->
    <div class="row g-3 g-md-4">
      <!-- LEFT: Premium Hero Card -->
      <div class="col-lg-5">
        <div
          class="main-card p-4 p-md-5 h-100 position-relative overflow-hidden d-flex flex-column justify-content-between shadow-lg border-0 rounded-4"
        >
          <div class="circle top-circle"></div>
          <div class="circle bottom-circle"></div>

          <div class="position-relative z-1 my-auto py-2">
            <h2 class="fw-bold text-white mb-3 main-title lh-sm">
              Alfraganus University Hospital
            </h2>
            <p class="text-white-50 main-desc fw-light lh-base mb-4">
              Zamonaviy texnologiyalar bilan jihozlangan ko‘p tarmoqli klinika.
              Tajribali mutaxassislar va 24/7 xizmat siz uchun ishlaydi.
              Sog‘ligingiz biz uchun muhim.
            </p>
          </div>

          <div class="position-relative z-1 mt-2">
            <router-link
              to="/about"
              class="btn btn-danger text-white rounded-pill px-4 py-2 fw-bold shadow-sm hover-scale btn-mobile"
            >
              Batafsil ma'lumot
            </router-link>
          </div>
        </div>
      </div>

      <!-- RIGHT: Statistics Grid (Bazadan keluvchi ma'lumotlar bilan) -->
      <div class="col-lg-7">
        <div class="row g-2 g-md-3">
          <!-- Card 1: Tibbiy Xizmatlar -->
          <div class="col-sm-4 col-6">
            <div
              class="stat-card p-3 p-md-4 text-center h-100 rounded-4 shadow-sm d-flex flex-column justify-content-center"
            >
              <div
                class="icon-wrapper mx-auto mb-2 mb-md-3 d-flex align-items-center justify-content-center rounded-circle shadow-sm"
              >
                <i class="bi bi-heart-pulse-fill fs-4 fs-md-3"></i>
              </div>
              <h4 class="fw-black m-0 stat-num">
                {{ animatedStats.services }}+
              </h4>
              <p class="text-muted fw-semibold mt-1 mb-0 stat-text">
                Tibbiy Xizmatlar
              </p>
            </div>
          </div>

          <!-- Card 2: Mutaxassisliklar / Yo'nalishlar (Specializations dan) -->
          <div class="col-sm-4 col-6">
            <div
              class="stat-card p-3 p-md-4 text-center h-100 rounded-4 shadow-sm d-flex flex-column justify-content-center"
            >
              <div
                class="icon-wrapper mx-auto mb-2 mb-md-3 d-flex align-items-center justify-content-center rounded-circle shadow-sm"
              >
                <i class="bi bi-grid-1x2-fill fs-4 fs-md-3"></i>
              </div>
              <h4 class="fw-black m-0 stat-num">
                {{ animatedStats.specializations }}
              </h4>
              <p class="text-muted fw-semibold mt-1 mb-0 stat-text">
                Yo'nalishlar
              </p>
            </div>
          </div>

          <!-- Card 3: Operatsiyalar / Jarrohlik -->
          <div class="col-sm-4 col-6">
            <div
              class="stat-card p-3 p-md-4 text-center h-100 rounded-4 shadow-sm d-flex flex-column justify-content-center"
            >
              <div
                class="icon-wrapper mx-auto mb-2 mb-md-3 d-flex align-items-center justify-content-center rounded-circle shadow-sm"
              >
                <i class="bi bi-activity fs-4 fs-md-3"></i>
              </div>
              <h4 class="fw-black m-0 stat-num">
                {{ animatedStats.surgeries }}+
              </h4>
              <p class="text-muted fw-semibold mt-1 mb-0 stat-text">
                Operatsiyalar
              </p>
            </div>
          </div>

          <!-- Card 4: Mamnun Bemorlar (Shifokor maslahati o'rniga) -->
          <div class="col-sm-4 col-6">
            <div
              class="stat-card p-3 p-md-4 text-center h-100 rounded-4 shadow-sm d-flex flex-column justify-content-center"
            >
              <div
                class="icon-wrapper mx-auto mb-2 mb-md-3 d-flex align-items-center justify-content-center rounded-circle shadow-sm"
              >
                <i class="bi bi-emoji-smile-fill fs-4 fs-md-3"></i>
              </div>
              <h4 class="fw-black m-0 stat-num">%98</h4>
              <p class="text-muted fw-semibold mt-1 mb-0 stat-text">
                Mamnun Bemorlar
              </p>
            </div>
          </div>

          <!-- Card 5: Bo'limlar -->
          <div class="col-sm-4 col-6">
            <div
              class="stat-card p-3 p-md-4 text-center h-100 rounded-4 shadow-sm d-flex flex-column justify-content-center"
            >
              <div
                class="icon-wrapper mx-auto mb-2 mb-md-3 d-flex align-items-center justify-content-center rounded-circle shadow-sm"
              >
                <i class="bi bi-building-fill fs-4 fs-md-3"></i>
              </div>
              <h4 class="fw-black m-0 stat-num">
                {{ animatedStats.departments }}
              </h4>
              <p class="text-muted fw-semibold mt-1 mb-0 stat-text">
                Bo'limlar
              </p>
            </div>
          </div>

          <!-- Card 6: Tashkil topgan yili -->
          <div class="col-sm-4 col-6">
            <div
              class="stat-card p-3 p-md-4 text-center h-100 rounded-4 shadow-sm d-flex flex-column justify-content-center"
            >
              <div
                class="icon-wrapper mx-auto mb-2 mb-md-3 d-flex align-items-center justify-content-center rounded-circle shadow-sm"
              >
                <i class="bi bi-calendar-check-fill fs-4 fs-md-3"></i>
              </div>
              <h4 class="fw-black m-0 stat-num">2023</h4>
              <p class="text-muted fw-semibold mt-1 mb-0 stat-text">
                Tashkil Topgan
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { reactive, onMounted } from "vue";
import api, { unwrap } from "../../../services/api";

const stats = reactive({
  services: 0,
  specializations: 0,
  surgeries: 0,
  departments: 0,
});

const animatedStats = reactive({
  services: 0,
  specializations: 0,
  surgeries: 0,
  departments: 0,
});

const runCounter = (key, targetValue, duration = 1500) => {
  if (!targetValue || targetValue <= 0) {
    animatedStats[key] = targetValue;
    return;
  }
  let startTimestamp = null;
  const step = (timestamp) => {
    if (!startTimestamp) startTimestamp = timestamp;
    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
    const easeProgress = 1 - Math.pow(1 - progress, 3);
    animatedStats[key] = Math.floor(easeProgress * targetValue);
    if (progress < 1) {
      window.requestAnimationFrame(step);
    } else {
      animatedStats[key] = targetValue;
    }
  };
  window.requestAnimationFrame(step);
};

const countOf = (data) => {
  if (Array.isArray(data)) return data.length;
  if (data && typeof data === "object") return data.total ?? data.length ?? 0;
  return 0;
};

const fetchStatistics = async () => {
  try {
    const [servicesData, specsData, surgeriesData] = await Promise.all([
      api
        .get("/services")
        .then(unwrap)
        .catch(() => []),
      api
        .get("/specializations")
        .then(unwrap)
        .catch(() => []),
      api
        .get("/surgeries")
        .then(unwrap)
        .catch(() => []),
    ]);

    stats.services = countOf(servicesData);
    stats.specializations = countOf(specsData);
    stats.surgeries = countOf(surgeriesData);
    stats.departments = 15; // Agar bo'limlar uchun alohida API bo'lsa shunga moslang

    runCounter("services", stats.services);
    runCounter("specializations", stats.specializations);
    runCounter("surgeries", stats.surgeries);
    runCounter("departments", stats.departments);
  } catch (error) {
    console.error("Statistika ma'lumotlarini yuklashda xatolik:", error);
    animatedStats.services = 25000;
    animatedStats.specializations = 15;
    animatedStats.surgeries = 10000;
    animatedStats.departments = 15;
  }
};

onMounted(() => {
  fetchStatistics();
});
</script>

<style scoped>
.about-page {
  background-color: var(--clr-bg);
  transition: background-color 0.3s ease;
}

.name {
  font-family: "Outfit", sans-serif;
  font-weight: 600;
  color: var(--clr-primary);
  font-size: clamp(1.5rem, 4vw, 2.8rem);
  letter-spacing: -1px;
}

.shrink-none {
  flex-shrink: 0;
}

.main-card {
  background: linear-gradient(
    135deg,
    var(--clr-primary) 0%,
    var(--clr-footer, #001950) 100%
  );
  min-height: 400px;
}

.main-title {
  font-size: clamp(1.35rem, 3.5vw, 2.2rem);
}

.main-desc {
  font-size: clamp(0.95rem, 2vw, 1.25rem);
}

/* Stat Card - Dark mode va light mode uchun tokenlar bilan mukammal moslashuv */
.stat-card {
  background-color: var(--clr-surface) !important;
  border: 1px solid var(--clr-border) !important;
  transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
  cursor: pointer;
}

.icon-wrapper {
  width: 54px;
  height: 54px;
  background-color: var(--clr-faint) !important;
  color: var(--clr-primary);
  transition: all 0.3s ease;
}

.stat-num {
  font-family: "Outfit", sans-serif;
  color: var(--clr-primary) !important;
  font-weight: 800;
  font-size: clamp(1.35rem, 3vw, 2rem);
}

.stat-text {
  font-size: clamp(0.75rem, 1.5vw, 0.875rem);
  color: var(--clr-muted) !important;
}

/* Hover Effektlari */
.stat-card:hover {
  transform: translateY(-5px);
  border-color: var(--clr-secondary) !important;
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08) !important;
}

.stat-card:hover .icon-wrapper {
  background-color: var(--clr-secondary) !important;
  color: white !important;
}

.hover-scale:hover {
  transform: scale(1.02);
  transition: transform 0.2s ease;
}

/* Glow doiralar */
.circle {
  position: absolute;
  border-radius: 50%;
  background: radial-gradient(
    circle,
    rgba(255, 255, 255, 0.1) 0%,
    rgba(255, 255, 255, 0) 70%
  );
  pointer-events: none;
}

.top-circle {
  width: 300px;
  height: 300px;
  top: -100px;
  right: -100px;
}

.bottom-circle {
  width: 350px;
  height: 350px;
  left: -150px;
  bottom: -150px;
}

/* Maxsus Mobil Moslashuvchanlik */
@media (max-width: 576px) {
  .main-card {
    min-height: auto;
  }
  .btn-mobile {
    width: 100%;
    text-align: center;
    padding: 10px 0;
  }
  .icon-wrapper {
    width: 44px;
    height: 44px;
  }
  .top-circle,
  .bottom-circle {
    display: none;
  }
  .shrink-none {
    font-size: 15px !important;
  }
  .name {
    font-size: 20px;
  }
}
</style>
