<template>
  <div class="surgery-page min-vh-100">
    <!-- HERO BANNER SECTION -->
    <section class="page-hero">
      <video class="page-hero-video" autoplay muted loop playsinline>
        <source src="/bg-videoo.mp4" type="video/mp4" />
      </video>

      <div class="page-hero-overlay"></div>

      <div class="container position-relative z-2 text-center hero-content">
        <span class="hero-subtitle">Jarrohlik Xizmatlari</span>
        <h1 class="hero-title">Operatsion Bo'limi</h1>
        <p class="hero-desc">
          Yuqori aniqlikdagi zamonaviy uskunalar va tajribali jarrohlar nazorati
          ostida amalga oshiriladigan amaliyotlar.
        </p>
      </div>
    </section>

    <!-- Surgeries Grid -->
    <div class="container services-content-wrap">
      <div
        class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3"
      >
        <div>
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-hospital-fill text-primary me-2"></i>Mavjud
            Operatsiyalar
          </h5>
          <small class="text-muted"
            >Amaliyot turlari, davomiyligi va belgilangan narxlar</small
          >
        </div>
        <span
          class="badge bg-primary-subtle text-primary fw-bold fs-6 px-3 py-2"
        >
          {{ surgeriesList.length }} ta amaliyot
        </span>
      </div>

      <!-- Skeleton loading grid — same col layout as real cards -->
      <div v-if="loading" class="row g-4 justify-content-center">
        <div v-for="n in 6" :key="n" class="col-12 col-md-6 col-lg-4">
          <SkeletonCard variant="overlay" />
        </div>
      </div>

      <div v-else-if="surgeriesList.length === 0" class="text-center py-5">
        <i class="bi bi-inbox fs-1 text-secondary mb-2 d-block opacity-50"></i>
        <p class="fw-600 text-secondary">Hozircha operatsiyalar mavjud emas</p>
      </div>

      <div v-else class="row g-4 justify-content-center">
        <div
          v-for="item in surgeriesList"
          :key="item.id"
          class="col-12 col-md-6 col-lg-4"
        >
          <router-link
            :to="'/surgeries/' + item.slug"
            class="text-decoration-none"
          >
            <div class="overlay-card">
              <img :src="item.image" :alt="item.title" class="overlay-img" />
              <div class="overlay-gradient"></div>
              <div class="overlay-content">
                <h4 class="overlay-title">{{ item.title }}</h4>
                <p class="overlay-desc">{{ item.subtitle }}</p>
                <p v-if="item.duration || item.price" class="overlay-meta">
                  <i class="bi bi-clock me-1"></i
                  ><span v-if="item.duration">{{ item.duration }}</span
                  ><span v-if="item.duration && item.price"> • </span
                  ><span v-if="item.price">{{ item.price }}</span>
                </p>
                <span class="overlay-btn">
                  Batafsil <i class="bi bi-arrow-right ms-1"></i>
                </span>
              </div>
            </div>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import SkeletonCard from "../../../components/ui/SkeletonCard.vue";
import surgeryService from "../../../services/surgeryService";

const loading = ref(false);
const rawSurgeries = ref([]);
const backendUrl = import.meta.env.VITE_STORAGE_URL || "";

const getImageUrl = (s) => {
  const raw = s.image || s.photo || s.image_url || s.photo_url || null;
  if (!raw)
    return (
      "https://ui-avatars.com/api/?name=" +
      encodeURIComponent(s.name || s.title || "Operatsiya") +
      "&background=0284c7&color=fff&size=512"
    );
  return raw.startsWith("http") ? raw : backendUrl + raw;
};

const fetchSurgeries = async () => {
  loading.value = true;
  try {
    const resData = await surgeryService.fetchAllPublic();
    rawSurgeries.value = resData?.data || resData || [];
  } catch (error) {
    console.error("API so'rovida xatolik:", error);
    rawSurgeries.value = [];
  } finally {
    loading.value = false;
  }
};

const surgeriesList = computed(() => {
  return rawSurgeries.value.map((s) => ({
    id: s.id,
    slug: s.slug,
    title: s.name || s.title || "Operatsiya",
    subtitle:
      s.short_description ||
      s.description ||
      "Jarrohlik amaliyoti va tibbiy aralashuv",
    duration: s.duration ? `${s.duration} min` : null,
    price: s.price ? `${s.price} so'm` : null,
    image: getImageUrl(s),
  }));
});

onMounted(() => {
  window.scrollTo({ top: 0, behavior: "smooth" });
  fetchSurgeries();
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

/* ══════════ CONTENT WRAP ══════════ */
.services-content-wrap {
  background: #f8fafc;
  border-radius: 20px 20px 0 0;
  padding: 35px 10px;
}

[data-theme="dark"] .services-content-wrap {
  background: #0f172a;
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
    height: 320px;
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
