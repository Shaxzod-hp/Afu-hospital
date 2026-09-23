<template>
  <div class="surgery-detail-page min-vh-100 text-start">
    <!-- Video Hero Banner -->
    <section class="page-hero">
      <video class="page-hero-video" autoplay muted loop playsinline>
        <source src="/bg-videoo.mp4" type="video/mp4" />
      </video>
      <div class="page-hero-overlay"></div>

      <div class="container position-relative z-2 text-center hero-content">
        <span class="hero-subtitle">Jarrohlik Amaliyotlari</span>
        <h1 class="hero-title">{{ surgery?.title || "Operatsion Bo'limi" }}</h1>
        <p class="hero-desc" v-if="surgery?.description">
          {{ surgery.description.slice(0, 120) }}...
        </p>
      </div>
    </section>

    <!-- Main Content Wrapper -->
    <div class="container detail-content-wrap">
      <!-- Skeleton placeholder — detail variant mirrors the two-column layout -->
      <div v-if="loading">
        <SkeletonCard variant="detail" />
      </div>

      <!-- Surgery Content -->
      <div
        v-else-if="surgery"
        class="row g-4 g-lg-5 text-start align-items-stretch"
      >
        <!-- Left Column: Image & Stats -->
        <div class="col-lg-6 d-flex flex-column">
          <div class="card-media-wrap mb-4">
            <img
              :src="surgery.image"
              class="surgery-main-img"
              :alt="surgery.title"
            />
          </div>

          <div class="stats-row d-flex gap-3 mt-auto">
            <div class="stat-box flex-grow-1 p-3 text-center rounded-4">
              <i
                class="bi bi-clock-history text-primary fs-3 mb-1 d-inline-block"
              ></i>
              <h6 class="fw-bold mb-1">Davomiyligi</h6>
              <p class="text-secondary small mb-0">
                {{ surgery.duration || "60-120 min" }}
              </p>
            </div>
            <div class="stat-box flex-grow-1 p-3 text-center rounded-4">
              <i
                class="bi bi-shield-check text-success fs-3 mb-1 d-inline-block"
              ></i>
              <h6 class="fw-bold mb-1">Xavfsizlik</h6>
              <p class="text-secondary small mb-0">Yuqori daraja</p>
            </div>
            <div class="stat-box flex-grow-1 p-3 text-center rounded-4">
              <i
                class="bi bi-activity text-danger fs-3 mb-1 d-inline-block"
              ></i>
              <h6 class="fw-bold mb-1">Reabilitatsiya</h6>
              <p class="text-secondary small mb-0">
                {{ surgery.recovery || "3-7 kun" }}
              </p>
            </div>
          </div>
        </div>

        <!-- Right Column: Details & Pricing -->
        <div class="col-lg-6">
          <div class="detail-card p-4 p-md-5 h-100 rounded-4">
            <span
              class="badge bg-primary-subtle text-primary mb-3 rounded-pill px-3 py-2 fw-bold text-uppercase tracking-wider"
            >
              Jarrohlik Amaliyoti
            </span>

            <h2 class="fw-bold text-dark mb-3">{{ surgery.title }}</h2>

            <p class="text-secondary fs-6 lh-base mb-4">
              {{ surgery.description }}
            </p>

            <p
              class="text-muted small lh-lg mb-4"
              v-if="surgery.fullDescription"
            >
              {{ surgery.fullDescription }}
            </p>

            <div
              v-if="(surgery.items || surgery.features)?.length"
              class="mb-4"
            >
              <h5
                class="fw-bold text-dark mb-3 ps-2 border-start border-4 border-primary"
              >
                Asosiy Xizmatlar va Imkoniyatlar
              </h5>
              <div class="row g-2">
                <div
                  class="col-12 col-md-6"
                  v-for="(feature, index) in surgery.items || surgery.features"
                  :key="index"
                >
                  <div
                    class="feature-item p-3 d-flex align-items-center gap-3 rounded-3"
                  >
                    <div class="icon-circle bg-primary text-white">
                      <i class="bi bi-check-lg"></i>
                    </div>
                    <span class="fw-semibold text-dark small">{{
                      feature
                    }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Price & Booking Card -->
            <div
              class="price-booking-card p-4 rounded-4 mt-auto d-flex align-items-center justify-content-between flex-wrap gap-3"
            >
              <div>
                <p class="text-muted small mb-1">Taxminiy operatsiya narxi:</p>

                <!-- Agar price obyekt bo'lsa va min/max mavjud bo'lsa -->
                <h4
                  class="fw-bold text-primary mb-0"
                  v-if="
                    typeof surgery?.price === 'object' &&
                    surgery?.price !== null
                  "
                >
                  {{ surgery.price.min?.toLocaleString() }} -
                  {{ surgery.price.max?.toLocaleString() }} so'm
                </h4>

                <!-- Agar price matn yoki raqam bo'lsa -->
                <h4
                  class="fw-bold text-primary mb-0"
                  v-else-if="surgery?.price"
                >
                  {{ surgery.price }}
                </h4>

                <!-- Narx belgilanmagan bo'lsa -->
                <h4 class="fw-bold text-primary mb-0" v-else>
                  Konsultatsiyadan so'ng
                </h4>
              </div>

              <router-link
                :to="{ path: '/contact', query: { specialty: surgery?.title } }"
                class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm hover-elevate"
              >
                Band qilish <i class="bi bi-arrow-right ms-1"></i>
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- Not Found State -->
      <div v-else class="text-center py-5">
        <i
          class="bi bi-exclamation-circle fs-1 text-secondary mb-2 d-block opacity-50"
        ></i>
        <h4 class="text-secondary fw-bold">
          Operatsiya ma'lumotlari topilmadi
        </h4>
        <router-link
          to="/surgeries"
          class="btn btn-primary mt-3 px-4 rounded-pill"
        >
          Bo'limga qaytish
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { useRoute } from "vue-router";
import SkeletonCard from "../../../components/ui/SkeletonCard.vue";
import surgeryService from "../../../services/surgeryService";

const route = useRoute();
const loading = ref(true);
const surgery = ref(null);
const backendUrl = import.meta.env.VITE_API_URL || "";

const getImageUrl = (s) => {
  const raw = s.image || s.photo || s.image_url || s.photo_url || null;
  if (!raw) {
    return (
      "https://ui-avatars.com/api/?name=" +
      encodeURIComponent(s.name || s.title || "Operatsiya") +
      "&background=0284c7&color=fff&size=512"
    );
  }
  return raw.startsWith("http") ? raw : backendUrl + raw;
};

const fetchSurgeryDetail = async () => {
  loading.value = true;
  const slug = route.params.slug;

  try {
    const resData = await surgeryService.fetchOnePublic(slug);
    const data = resData?.data || resData;

    if (data) {
      surgery.value = {
        id: data.id,
        title: data.name || data.title || "Operatsiya",
        description: data.description || data.short_description || "",
        fullDescription: data.full_description || data.details || "",
        duration: data.duration ? `${data.duration} min` : null,
        recovery: data.recovery_time || data.recovery || null,
        price:
          typeof data.price === "object" && data.price !== null
            ? data.price
            : data.price
            ? `${Number(data.price).toLocaleString()} so'm`
            : null,
        image: getImageUrl(data),
        features: data.features || data.items || [],
      };
    }
  } catch (error) {
    console.error("Operatsiyani yuklashda xatolik:", error);
    surgery.value = null;
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  window.scrollTo({ top: 0, behavior: "smooth" });
  fetchSurgeryDetail();
});

watch(() => route.params.slug, fetchSurgeryDetail);
</script>

<style scoped>
/* ══════════ HERO BANNER ══════════ */
.page-hero {
  position: relative;
  width: 100%;
  height: 450px;
  min-height: 450px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  color: #fff;
  background-color: #002b87;
}

.page-hero-video {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  z-index: 0;
  pointer-events: none;
}

.page-hero-overlay {
  position: absolute;
  inset: 0;
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

/* ══════════ CONTENT WRAP ══════════ */
.detail-content-wrap {
  background: #f8fafc;
  border-radius: 20px 20px 0 0;
  padding: 35px 15px 60px;
}

[data-theme="dark"] .detail-content-wrap {
  background: #0f172a;
}

/* ══════════ MEDIA & STATS ══════════ */
.card-media-wrap {
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
  background: #fff;
  height: 380px;
}

.surgery-main-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.stat-box {
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.05);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
  transition: transform 0.25s ease;
}

.stat-box:hover {
  transform: translateY(-3px);
}

/* ══════════ CARD & FEATURES ══════════ */
.detail-card {
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.06);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
  display: flex;
  flex-direction: column;
}

.feature-item {
  background: #f8fafc;
  border: 1px solid rgba(0, 0, 0, 0.03);
  transition: all 0.2s ease;
}

.feature-item:hover {
  background: #ffffff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  transform: translateX(3px);
}

.icon-circle {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
  flex-shrink: 0;
}

.price-booking-card {
  background: #f1f5f9;
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.hover-elevate {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-elevate:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(2, 132, 199, 0.3) !important;
}

/* ══════════ RESPONSIVE ══════════ */
@media (max-width: 991px) {
  .page-hero {
    height: 350px;
    min-height: 350px;
  }
  .card-media-wrap {
    height: 300px;
  }
}

@media (max-width: 768px) {
  .page-hero {
    height: 320px;
    min-height: 320px;
  }
  .hero-title {
    font-size: 1.75rem;
  }
  .detail-content-wrap {
    padding-top: 25px;
  }
  .stats-row {
    margin-bottom: 20px;
  }
}
</style>
