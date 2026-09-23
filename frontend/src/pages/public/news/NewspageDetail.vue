<template>
  <div class="news-detail-page min-vh-100 pb-5">
    <!-- LOADING -->
    <div v-if="loading" class="text-center py-5 my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Yuklanmoqda...</span>
      </div>
    </div>

    <!-- ERROR -->
    <div v-else-if="error" class="container py-5 text-center">
      <i class="bi bi-exclamation-triangle fs-1 text-danger mb-3 d-block"></i>
      <p class="text-muted fw-semibold mb-4">{{ error }}</p>
      <router-link to="/news" class="btn btn-primary rounded-pill px-4 py-2">
        Yangiliklarga qaytish
      </router-link>
    </div>

    <!-- CONTENT -->
    <template v-else-if="news">
      <!-- VIDEO HERO -->
      <div class="news-hero-container position-relative overflow-hidden">
        <video class="news-hero-video" autoplay muted loop playsinline>
          <source src="/bg-videoo.mp4" type="video/mp4" />
        </video>
        <div class="news-hero-overlay"></div>
      </div>

      <!-- MAIN CONTENT CARD -->
      <div class="container news-content-wrapper">
        <div class="new-content bg-white shadow rounded-4 p-3 p-sm-4 p-md-5">
          <router-link
            to="/news"
            class="back-link d-inline-flex align-items-center gap-2 mb-3 mb-md-4 text-decoration-none"
          >
            <i class="bi bi-arrow-left"></i> Yangiliklarga qaytish
          </router-link>

          <div class="news-date mb-2">
            {{ formatDate(news.published_at || news.created_at) }}
          </div>

          <h1 class="news-title mb-3 mb-md-4">{{ news.title }}</h1>

          <div
            v-if="news.image"
            class="news-image-wrap mb-4 overflow-hidden rounded-4"
          >
            <img
              :src="getPhotoUrl(news.image)"
              :alt="news.title"
              class="news-image w-100"
            />
          </div>

          <p v-if="news.summary" class="news-summary mb-4">
            {{ news.summary }}
          </p>

          <div class="news-content" v-html="news.content"></div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { useRoute } from "vue-router";
import newsService from "../../../services/newsService";

const route = useRoute();
const backendUrl = import.meta.env.VITE_STORAGE_URL || "";

const news = ref(null);
const loading = ref(true);
const error = ref(null);

const getPhotoUrl = (image) => {
  if (!image) return null;
  return image.startsWith("http") ? image : backendUrl + image;
};

const formatDate = (dateStr) => {
  if (!dateStr) return "";
  const d = new Date(dateStr);
  return d.toLocaleDateString("uz-Latn-UZ", {
    day: "2-digit",
    month: "long",
    year: "numeric",
  });
};

const loadNews = async () => {
  loading.value = true;
  error.value = null;

  // Slug yoki ID ni to'g'ri ajratib olish
  const slugOrId = route.params.slug || route.params.id;

  if (!slugOrId) {
    error.value = "Yangilik manzili noto'g'ri kiritilgan.";
    loading.value = false;
    return;
  }

  try {
    const data = await newsService.fetchOne(slugOrId);
    // Laravel response ba'zan data.data yoki to'g'ridan-to'g'ri obyekt bo'ladi
    news.value = data?.data || data;
  } catch (err) {
    error.value = "Yangilikni yuklashda xatolik yuz berdi.";
  } finally {
    loading.value = false;
  }
};

// Route o'zgarganda (masalan, boshqa yangilikka o'tganda) qayta yuklash
watch(() => route.params.slug || route.params.id, loadNews);

onMounted(loadNews);
</script>

<style scoped>
/* VIDEO HERO */
.news-hero-container {
  position: relative;
  width: 100%;
  height: 380px;
  background-color: #002b87;
}

.news-hero-video {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 100%;
  height: 100%;
  min-width: 100%;
  min-height: 100%;
  object-fit: cover;
  transform: translate(-50%, -50%);
  pointer-events: none;
}

.news-hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    135deg,
    rgba(0, 43, 135, 0.65) 0%,
    rgba(0, 20, 70, 0.75) 100%
  );
}

/* CONTENT WRAPPER */
.news-content-wrapper {
  margin-top: -100px;
  position: relative;
  z-index: 5;
}

.new-content {
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.back-link {
  color: #0284c7;
  font-weight: 600;
  font-size: 0.9rem;
  transition: color 0.2s ease;
}

.back-link:hover {
  color: #0369a1;
}

.news-date {
  color: #dc2626;
  font-size: 0.85rem;
  font-weight: 700;
}

.news-title {
  font-size: clamp(1.4rem, 3.5vw, 2.2rem);
  font-weight: 800;
  color: #0f172a;
  line-height: 1.35;
}

.news-image {
  max-height: 480px;
  object-fit: cover;
}

.news-summary {
  font-size: 1.05rem;
  font-weight: 600;
  color: #475569;
  line-height: 1.6;
  border-left: 4px solid #0284c7;
  padding-left: 1rem;
}

.news-content {
  font-size: 1rem;
  line-height: 1.85;
  color: #334155;
}

.news-content :deep(strong),
.news-content :deep(b) {
  color: #0f172a;
}

.news-content :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: 12px;
  margin: 1rem 0;
}

/* DARK MODE */
[data-theme="dark"] .news-detail-page {
  background: #0f172a !important;
}

[data-theme="dark"] .new-content {
  background-color: #1e293b !important;
  border-color: rgba(255, 255, 255, 0.08);
}

[data-theme="dark"] .news-title {
  color: #f8fafc;
}

[data-theme="dark"] .news-summary {
  color: #cbd5e1;
  border-left-color: #38bdf8;
}

[data-theme="dark"] .news-content {
  color: #cbd5e1;
}

[data-theme="dark"] .news-content :deep(strong),
[data-theme="dark"] .news-content :deep(b) {
  color: #f8fafc;
}

/* RESPONSIVE */
@media (max-width: 768px) {
  .news-hero-container {
    height: 260px;
  }
  .news-content-wrapper {
    margin-top: -60px;
  }
  .news-image {
    max-height: 280px;
  }
}
</style>
