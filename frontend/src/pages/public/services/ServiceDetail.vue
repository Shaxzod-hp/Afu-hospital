<template>
  <div class="service-detail-page bg-light min-vh-100">
    <!-- Hero Banner Video -->
    <section class="page-hero">
      <video class="page-hero-video" autoplay muted loop playsinline>
        <source src="/bg-videoo.mp4" type="video/mp4" />
      </video>

      <div class="page-hero-overlay"></div>

      <div class="container position-relative z-2 text-center hero-content">
        <span class="hero-subtitle">Tibbiy Xizmatlar</span>
        <h1 class="hero-title">{{ service?.name || "Xizmat tafsilotlari" }}</h1>
        <p class="hero-desc">
          Toshkentdagi yetakchi shifokorlar va zamonaviy tibbiyot
          texnologiyalari ko'magida sog'lig'ingizni qayta tiklang.
        </p>
      </div>
    </section>

    <div class="container detail-content-wrap">
      <!-- Xizmatlarga qaytish tugmasi -->
      <div class="mb-3">
        <router-link
          to="/services"
          class="btn btn-outline-secondary w-100 rounded-pill btn-sm px-3 d-inline-flex justify-content-center align-items-center gap-2 hover-back-btn"
        >
          <i class="bi bi-arrow-left"></i>
          <span>Xizmatlar ro'yxatiga qaytish</span>
        </router-link>
      </div>

      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Yuklanmoqda...</span>
        </div>
      </div>

      <div v-else-if="service" class="row g-4 text-start">
        <!-- Main Content -->
        <div class="col-lg-8">
          <!-- Photo banner -->
          <div class="photo-banner mb-4">
            <img
              :src="getPhotoUrl(service.photo, service.name)"
              :alt="service.name"
            />
          </div>

          <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4">
            <h1 class="fw-bold mb-3 text-dark fs-2">{{ service.name }}</h1>
            <p class="text-secondary fs-6 lh-base mb-4">
              {{
                service.short_description ||
                "Xizmat haqida ma'lumot kiritilmagan."
              }}
            </p>

            <!-- Price list (checkmark style) -->
            <div v-if="service.included_items && service.included_items.length">
              <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                Xizmat narxlari
              </h5>
              <div class="d-flex flex-column gap-2">
                <div
                  v-for="(item, idx) in service.included_items"
                  :key="idx"
                  class="price-row d-flex align-items-center justify-content-between gap-3"
                >
                  <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill check-icon"></i>
                    <span class="fw-semibold text-dark price-title">{{
                      item.name
                    }}</span>
                  </span>
                  <span class="fw-bold text-primary text-nowrap price-value">
                    {{ formatPrice(item.price) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sticky Booking Card -->
        <div class="col-lg-4">
          <div class="booking-card sticky-top" style="top: 100px; z-index: 10">
            <div class="booking-header">
              <i class="bi bi-calendar2-check-fill"></i>
              <h5 class="m-0">Qabulga yozilish</h5>
            </div>

            <p class="text-secondary small mb-4">
              Ushbu xizmat bo'yicha to'liq malumot olish uchun band qilish
              tugmasini bosing va arizani to'ldiring.
            </p>

            <router-link
              :to="{ path: '/contact', query: { specialty: service.name } }"
              class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm hover-elevate"
            >
              Band qilish <i class="bi bi-calendar-check ms-1"></i>
            </router-link>

            <div class="booking-features">
              <div class="feature-item">
                <i class="bi bi-shield-check"></i>
                <span>Xavfsiz va ishonchli xizmat</span>
              </div>
              <div class="feature-item">
                <i class="bi bi-clock-history"></i>
                <span>Qulay vaqt tanlash imkoni</span>
              </div>
              <div class="feature-item">
                <i class="bi bi-person-check"></i>
                <span>Malakali mutaxassislar</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-5">
        <h3 class="text-secondary">Xizmat topilmadi</h3>
        <router-link
          to="/services"
          class="btn btn-primary mt-3 rounded-pill px-4"
        >
          Xizmatlar ro'yxatiga qaytish
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from "vue";
import { useRoute } from "vue-router";
import { useServicesStore } from "../../../stores/services";

const route = useRoute();
const servicesStore = useServicesStore();

const service = ref(null);
const loading = ref(true);

const backendUrl = import.meta.env.VITE_STORAGE_URL || "";

const getPhotoUrl = (photo, name) => {
  if (!photo) {
    return (
      "https://ui-avatars.com/api/?name=" +
      encodeURIComponent(name || "Service") +
      "&background=0284c7&color=fff&size=800"
    );
  }
  return photo.startsWith("http") ? photo : backendUrl + photo;
};

const formatPrice = (price) => {
  const n = Number(price);
  if (isNaN(n)) return "";
  return n.toLocaleString("uz-Latn-UZ") + " so'm";
};

const loadService = async () => {
  loading.value = true;
  const slug = route.params.slug;

  if (servicesStore.services.length === 0) {
    await servicesStore.fetchServices();
  }

  // Assuming getById in store supports matching by slug or we need to match by slug
  const found = servicesStore.services.find(
    (s) => String(s.slug) === String(slug)
  );
  service.value = found || null;
  loading.value = false;
};

watch(
  () => route.params.slug,
  () => {
    loadService();
  }
);

onMounted(() => {
  window.scrollTo({ top: 0, behavior: "smooth" });
  loadService();
});
</script>

<style scoped>
/* ══════════ PAGE HERO (VIDEO HERO) ══════════ */
.page-hero {
  position: relative;
  width: 100%;
  height: 450px; /* Desktop o'lcham */
  min-height: 450px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  color: #fff;
  background-color: #002b87;
}

/* Video position va alignment */
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

/* Dark Overlay */
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
  max-width: 800px;
  padding: 0 15px;
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

/* ── CONTENT WRAP ── */
.detail-content-wrap {
  /* position: relative;
  z-index: 3; */
  background: #f8fafc;
  border-radius: 20px 20px 0 0;
  padding: 10px;
  /* margin-top: -30px; */
  /* padding: 32px 15px 50px; */
}

[data-theme="dark"] .detail-content-wrap {
  background: #0f172a;
}

.hover-back-btn {
  transition: all 0.2s ease;
  background-color: #ffffff;
}

.hover-back-btn:hover {
  background-color: #0d6efd;
  color: #ffffff !important;
  border-color: #0d6efd;
}

/* ── Photo banner ── */
.photo-banner {
  width: 100%;
  height: 320px;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 10px 28px rgba(0, 0, 0, 0.08);
}

.photo-banner img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* ── Price rows ── */
.price-row {
  background: #f8fafc;
  border-radius: 14px;
  padding: 14px 18px;
  border: 1px solid rgba(0, 0, 0, 0.03);
  transition: all 0.2s ease;
}

.price-row:hover {
  background: #ffffff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.check-icon {
  color: #0284c7;
  font-size: 1.1rem;
  flex-shrink: 0;
}

.price-title {
  font-size: 0.95rem;
  line-height: 1.3;
}

.price-value {
  font-size: 0.95rem;
}

/* ── Booking card ── */
.booking-card {
  background: #ffffff;
  border-radius: 24px;
  padding: 26px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
}

.booking-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding-bottom: 16px;
  margin-bottom: 16px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}

.booking-header i {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: rgba(2, 132, 199, 0.1);
  color: #0284c7;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
}

.booking-header h5 {
  font-weight: 800;
  color: #0f172a;
}

.hover-elevate {
  transition: all 0.25s ease;
}

.hover-elevate:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(37, 99, 235, 0.2) !important;
}

.booking-features {
  margin-top: 22px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.82rem;
  color: #64748b;
  font-weight: 600;
}

.feature-item i {
  color: #10b981;
  font-size: 1rem;
}

/* ── Tablet (≤991px) ── */
@media (max-width: 991px) {
  .photo-banner {
    height: 260px;
  }
}

/* ── Mobile (≤768px) ── */
@media (max-width: 768px) {
  .page-hero {
    height: 320px; /* Mobile o'lcham */
    min-height: 320px;
  }

  .hero-title {
    font-size: 1.75rem;
  }

  .hero-desc {
    font-size: 0.88rem;
  }

  .detail-content-wrap {
    padding: 18px 12px 32px;
  }

  .photo-banner {
    height: 190px;
    border-radius: 18px;
  }

  .card.p-4.p-md-5 {
    padding: 1.25rem !important;
  }

  .price-row {
    padding: 12px 14px;
  }

  .price-title {
    font-size: 0.85rem;
  }

  .price-value {
    font-size: 0.9rem;
  }

  .booking-card {
    padding: 20px;
    margin-top: 4px;
  }
}
</style>
