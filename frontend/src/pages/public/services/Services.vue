<template>
  <div class="services-page bg-light min-vh-100 pb-5">
    <!-- HERO BANNER SECTION -->
    <section class="page-hero">
      <video class="page-hero-video" autoplay muted loop playsinline>
        <source src="/bg-videoo.mp4" type="video/mp4" />
      </video>

      <div class="page-hero-overlay"></div>

      <div class="container position-relative z-2 text-center hero-content">
        <span class="hero-subtitle">Bizning Imkoniyatlar</span>
        <h1 class="hero-title">Tibbiy Xizmat Turlari</h1>
        <p class="hero-desc">
          Sizning salomatligingiz va qulayligingiz uchun eng zamonaviy tibbiy
          texnologiyalar hamda yuqori sifatli shifokorlar nazorati ostida
          professional xizmatlarni taqdim etamiz.
        </p>
      </div>
    </section>

    <!-- Services Grid -->
    <div class="container services-content-wrap">
      <!-- Skeleton loading grid — same col layout as real cards -->
      <div v-if="loading" class="row g-4 justify-content-center">
        <div v-for="n in 6" :key="n" class="col-12 col-md-6 col-lg-4">
          <SkeletonCard variant="overlay" />
        </div>
      </div>

      <div v-else-if="error" class="text-center py-5">
        <i class="bi bi-exclamation-triangle fs-2 text-danger mb-2 d-block"></i>
        <p class="fw-600 mb-3">{{ error }}</p>
        <button
          class="btn btn-outline-primary"
          @click="servicesStore.fetchServices()"
        >
          Qayta urinish
        </button>
      </div>

      <div v-else-if="services.length === 0" class="text-center py-5">
        <i class="bi bi-inbox fs-1 text-secondary mb-2 d-block opacity-50"></i>
        <p class="fw-600 text-secondary">Hozircha xizmatlar mavjud emas</p>
      </div>

      <div v-else class="row g-4 justify-content-center">
        <div
          v-for="service in services"
          :key="service.id"
          class="col-12 col-md-6 col-lg-4"
        >
          <router-link
            :to="'/services/' + service.slug"
            class="text-decoration-none"
          >
            <div class="overlay-card">
              <img
                :src="getPhotoUrl(service.photo, service.name)"
                :alt="service.name"
                class="overlay-img"
              />
              <div class="overlay-gradient"></div>
              <div class="overlay-content">
                <h4 class="overlay-title">{{ service.name }}</h4>
                <p class="overlay-desc">
                  {{
                    service.short_description ||
                    "Xizmat haqida ma'lumot kiritilmagan"
                  }}
                </p>
                <p v-if="service.included_items?.length" class="overlay-meta">
                  <i class="bi bi-list-check me-1"></i
                  >{{ service.included_items.length }} ta xizmat turi
                </p>
                <span class="overlay-btn">
                  Batafsil <i class="bi bi-arrow-right ms-1"></i>
                </span>
              </div>
            </div>
          </router-link>
        </div>
      </div>

      <!-- Emergency Clinic Contact CTA -->
      <div
        class="cta-banner mt-5 p-4 p-md-5 rounded-5 text-white shadow"
        style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%)"
      >
        <div class="row align-items-center g-4">
          <div class="col-lg-8 text-start">
            <h3 class="fw-bold mb-2">
              Qabulga yozilish yoki savollaringiz bormi?
            </h3>
            <p class="mb-0 opacity-90 lead fs-6">
              Bizning mutaxassislarimiz har bir bemorga individual
              yondashadilar. Hoziroq bog'laning va maslahat oling.
            </p>
          </div>
          <div class="col-lg-4 text-lg-end">
            <router-link
              to="/contact"
              class="btn btn-light btn-lg rounded-pill px-5 fw-bold text-primary shadow-sm hover-elevate"
            >
              Bog'lanish <i class="bi bi-telephone-fill ms-2"></i>
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { useServicesStore } from "../../../stores/services";
import SkeletonCard from "../../../components/ui/SkeletonCard.vue";

const servicesStore = useServicesStore();

const services = computed(() => servicesStore.services);
const loading = computed(() => servicesStore.loading);
const error = computed(() => servicesStore.error);

const backendUrl = import.meta.env.VITE_API_URL || "";
const getPhotoUrl = (photo, name) => {
  if (!photo) {
    return (
      "https://ui-avatars.com/api/?name=" +
      encodeURIComponent(name || "Service") +
      "&background=0284c7&color=fff&size=512"
    );
  }
  return photo.startsWith("http") ? photo : backendUrl + photo;
};

onMounted(() => {
  servicesStore.fetchServices();
});
</script>

<style scoped>
.fw-600 {
  font-weight: 600;
}
.fw-900 {
  font-weight: 900;
}

/* ══════════ PAGE HERO (VIDEO HERO) ══════════ */
.page-hero {
  position: relative;
  width: 100%;
  height: 450px; /* Katta ekranlarda 450px */
  min-height: 450px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  color: #fff;
  background-color: #002b87;
}

/* Video markazlashtirish */
.page-hero-video {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 100%;
  height: 100%;
  min-width: 100%;
  min-height: 100%;
  object-fit: cover;
  transform: translate(-50%, -50%);
  z-index: 0;
  pointer-events: none;
}

/* Qorong'u qatlam (Overlay) */
.page-hero-overlay {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    135deg,
    rgba(0, 43, 135, 0.75) 0%,
    rgba(0, 20, 70, 0.8) 100%
  );
  z-index: 1;
  pointer-events: none;
}

.hero-content {
  max-width: 750px;
}

.hero-subtitle {
  font-size: 0.85rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 3px;
  color: #7dd3fc;
  display: inline-block;
  margin-bottom: 12px;
}

.hero-title {
  font-size: clamp(2rem, 5vw, 3rem);
  font-weight: 800;
  font-family: "Outfit", sans-serif;
  color: white;
  margin-bottom: 12px;
}

.hero-desc {
  color: rgba(255, 255, 255, 0.85);
  font-size: 1rem;
  line-height: 1.6;
  margin: 0 auto;
}

/* ══════════ SERVICES CONTENT ══════════ */
.services-content-wrap {
  /* position: relative;
  z-index: 3; */
  background: #f8fafc;
  border-radius: 20px 20px 0 0;
  padding: 35px 10px;
  /* margin-top: -30px;
  padding-top: 32px;
  padding-bottom: 32px; */
}

[data-theme="dark"] .services-content-wrap {
  background: #0f172a;
}

.hover-elevate {
  transition: all 0.25s ease;
}
.hover-elevate:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
}

/* ══════════ OVERLAY CARD ══════════ */
.overlay-card {
  position: relative;
  height: 360px;
  border-radius: 22px;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.overlay-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.overlay-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}
.overlay-card:hover .overlay-img {
  transform: scale(1.08);
}

.overlay-gradient {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    180deg,
    rgba(0, 0, 0, 0) 32%,
    rgba(0, 20, 60, 0.92) 100%
  );
}

.overlay-content {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  padding: 22px;
  color: white;
  text-align: left;
}

.overlay-title {
  font-weight: 800;
  font-size: 1.2rem;
  margin-bottom: 6px;
}

.overlay-desc {
  font-size: 0.85rem;
  opacity: 0.88;
  margin-bottom: 8px;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.overlay-meta {
  font-size: 0.78rem;
  opacity: 0.75;
  margin-bottom: 10px;
}

.overlay-btn {
  display: inline-flex;
  align-items: center;
  font-weight: 700;
  font-size: 0.85rem;
  color: #7dd3fc;
}

/* ── Mobile Responsive ── */
@media (max-width: 768px) {
  .page-hero {
    height: 320px; /* Telefonlarda 320px */
    min-height: 320px;
    padding: 0 16px;
  }

  .hero-title {
    font-size: 1.75rem;
  }

  .hero-desc {
    font-size: 0.88rem;
  }

  .services-content-wrap {
    margin-top: -20px;
  }

  .overlay-card {
    height: 300px;
  }

  .overlay-desc {
    -webkit-line-clamp: 3;
  }
}
</style>
