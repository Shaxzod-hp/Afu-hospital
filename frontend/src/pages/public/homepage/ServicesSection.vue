<template>
  <section class="section-pad py-3">
    <div class="container-fluid px-3 px-md-4 px-lg-5">
      <!-- Header -->
      <div
        class="header d-flex flex-row justify-content-between align-items-center gap-2 mb-4 pb-3 border-bottom"
      >
        <h2 class="font-display fw-bold m-0 header-title">Tibbiy xizmatlar</h2>

        <router-link
          to="/services"
          class="header-link text-danger text-decoration-none fw-bold d-flex align-items-center gap-1 flex-shrink-0"
        >
          Barcha xizmatlar
          <i class="bi bi-arrow-up-right"></i>
        </router-link>
      </div>

      <!-- 1. LOADING STATE (Ma'lumot yuklanayotganda) -->
      <div v-if="loading" class="row g-4 justify-content-center">
        <div v-for="n in 4" :key="n" class="col-12 col-sm-6 col-lg-4 col-xl-3">
          <div
            class="skeleton-card rounded-4 p-4 d-flex flex-column justify-content-end"
          >
            <div class="skeleton-line title mb-2"></div>
            <div class="skeleton-line desc mb-2"></div>
            <div class="skeleton-line count mb-3"></div>
            <div class="skeleton-line btn"></div>
          </div>
        </div>
      </div>

      <!-- 2. EMPTY STATE (Xizmatlar bo'lmaganda — Yangiliklar uslubida) -->
      <div v-else-if="!featured.length" class="text-center py-5 text-muted">
        <i class="bi bi-cloud-slash display-4 d-block mb-2 text-secondary"></i>
        <p class="mb-0">Hozircha xizmatlar kiritilmagan</p>
      </div>

      <!-- 3. SERVICES GRID (Ma'lumot kelganda) -->
      <div v-else class="row g-4 justify-content-center">
        <div
          v-for="service in featured"
          :key="service.id || service.name"
          class="col-12 col-sm-6 col-lg-4 col-xl-3"
        >
          <div
            class="service-card-image position-relative rounded-4 overflow-hidden d-flex flex-column justify-content-end p-4 shadow-sm"
            :style="{ backgroundImage: `url(${getServiceImage(service)})` }"
          >
            <!-- Dark Overlay for readable text -->
            <div class="card-overlay"></div>

            <!-- Content Container -->
            <div class="card-content position-relative z-2 text-white">
              <h4 class="fw-bold mb-1 service-title">
                {{ service.title || service.name || "Xizmat turi" }}
              </h4>

              <p class="service-desc text-white-50 mb-2 small">
                {{
                  service.description ||
                  service.short_description ||
                  "Professional xizmatlar va yuqori darajadagi parvarish"
                }}
              </p>

              <div
                class="service-count text-white-50 small mb-3 d-flex align-items-center gap-1"
              >
                <i class="bi bi-list-ul"></i>
                <span>
                  {{ service.sub_services_count || service.items_count || "3" }}
                  ta xizmat turi
                </span>
              </div>

              <!-- Action Button -->
              <router-link
                :to="'/services/' + service.slug"
                class="btn-batafsil text-white text-decoration-none fw-semibold d-inline-flex align-items-center gap-2"
              >
                <span>Batafsil</span>
                <i class="bi bi-arrow-right"></i>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, ref, onMounted } from "vue";
import { useServicesStore } from "../../../stores/services";

const store = useServicesStore();
const backendUrl = import.meta.env.VITE_STORAGE_URL || "";
const loading = ref(true);

const featured = computed(() => {
  const allServices = store.services || store.featured || [];
  if (!allServices.length) return [];

  const keywords = [
    "statsionar",
    "laborator",
    "uzd",
    "fizioterapiya",
    "muolaja",
    "24/7",
  ];

  const filtered = allServices.filter((service) => {
    const title = (service.title || service.name || "").toLowerCase();
    const id = String(service.id || "").toLowerCase();

    return keywords.some((key) => title.includes(key) || id.includes(key));
  });

  return filtered.length >= 4 ? filtered.slice(0, 4) : allServices.slice(0, 4);
});

const getServiceImage = (service) => {
  const img = service.image || service.photo || service.bg_image;
  if (!img) return "/images/default-service.jpg";
  return img.startsWith("http") ? img : backendUrl + img;
};

onMounted(async () => {
  try {
    if (store.fetchServices) {
      await store.fetchServices();
    }
  } catch (error) {
    console.error("Xizmatlarni yuklashda xatolik:", error);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.header-title {
  font-size: 2rem;
  color: #002b87;
}

.header-link {
  font-size: 1.1rem;
}

/* ===========================
   SKELETON LOADING STYLES
=========================== */
.skeleton-card {
  height: 380px;
  background-color: #e9ecef;
  animation: pulse 1.5s infinite ease-in-out;
}

.skeleton-line {
  background-color: #ced4da;
  border-radius: 4px;
}

.skeleton-line.title {
  height: 24px;
  width: 70%;
}
.skeleton-line.desc {
  height: 16px;
  width: 90%;
}
.skeleton-line.count {
  height: 14px;
  width: 40%;
}
.skeleton-line.btn {
  height: 18px;
  width: 30%;
}

@keyframes pulse {
  0% {
    opacity: 0.6;
  }
  50% {
    opacity: 1;
  }
  100% {
    opacity: 0.6;
  }
}

/* ===========================
   IMAGE CARD STYLE
=========================== */
.service-card-image {
  height: 380px;
  background-size: cover;
  background-position: center;
  border-radius: 24px !important;
  transition: transform 0.35s ease, box-shadow 0.35s ease;
  cursor: pointer;
}

.service-card-image:hover {
  transform: translateY(-6px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2) !important;
}

.card-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to top,
    rgba(10, 18, 40, 0.92) 0%,
    rgba(10, 18, 40, 0.4) 55%,
    rgba(0, 0, 0, 0) 100%
  );
  transition: opacity 0.35s ease;
}

.service-card-image:hover .card-overlay {
  background: linear-gradient(
    to top,
    rgba(10, 18, 40, 0.96) 0%,
    rgba(10, 18, 40, 0.5) 65%,
    rgba(0, 0, 0, 0.1) 100%
  );
}

.service-title {
  font-size: 1.35rem;
  letter-spacing: -0.3px;
}

.service-desc {
  line-height: 1.4;
  opacity: 0.85;
}

.service-count {
  font-size: 0.88rem;
}

.btn-batafsil {
  font-size: 0.95rem;
  transition: gap 0.25s ease, color 0.25s ease;
}

.service-card-image:hover .btn-batafsil {
  color: #00b0ff !important;
}

.service-card-image:hover .btn-batafsil i {
  transform: translateX(4px);
}

.btn-batafsil i {
  transition: transform 0.25s ease;
}

/* RESPONSIVE */
@media (max-width: 992px) {
  .service-card-image,
  .skeleton-card {
    height: 340px;
  }
}

@media (max-width: 576px) {
  .header-title {
    font-size: 1rem;
  }
  .service-card-image,
  .skeleton-card {
    height: 300px;
    border-radius: 20px !important;
  }
  .service-title {
    font-size: 1.15rem;
  }
  .header-link {
    font-size: 0.8rem;
  }
}
</style>
