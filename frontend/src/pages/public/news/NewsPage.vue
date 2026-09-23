<template>
  <section class="design-three min-vh-100 pb-5">
    <!-- HERO VIDEO BANNER -->
    <div class="news-hero-container position-relative overflow-hidden">
      <video class="news-hero-video" autoplay muted loop playsinline>
        <source src="/bg-videoo.mp4" type="video/mp4" />
      </video>
      <div class="news-hero-overlay"></div>
      <div class="news-hero-content container text-center">
        <div class="max-w-xl mx-auto text-white animate-fadeUp">
          <h1 class="hero-title fw-black mb-2 mb-md-3">
            <strong class="text-danger">ALFRAGANUS</strong> UNIVERSITY HOSPITAL
          </h1>
          <p class="hero-subtitle text-light opacity-85 d-none d-md-block">
            Eng so'nggi va ishonchli tibbiy yangiliklar, tadqiqotlar hamda
            klinika axborotlari.
          </p>
        </div>
      </div>
    </div>

    <!-- MAIN CONTENT AREA -->
    <div
      class="container-fluid px-3 px-md-4 px-lg-5 py-4 py-lg-5 news-main-wrap"
    >
      <!-- HEADER & SEARCH -->
      <div class="row align-items-center g-3 g-lg-4 mb-3 mb-lg-5">
        <div class="col-12 col-lg-8">
          <h1 class="news-title fw-bold lh-1 mb-0">
            Klinikamizdagi <span>so'nggi</span> yangiliklar.
          </h1>
        </div>
      </div>

      <div class="border-top border-secondary opacity-25 mb-4 mb-lg-5"></div>

      <!-- LOADING -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
      </div>

      <!-- CARD GRID -->
      <div v-else class="row g-3 g-md-4">
        <div
          v-for="item in news"
          :key="item.id"
          class="col-12 col-md-6 col-lg-4"
        >
          <div class="news-card shadow-sm rounded-4 h-100 bg-white">
            <router-link
              :to="{
                name: 'news-detail',
                params: { slug: item.slug || item.id },
              }"
              class="d-block text-decoration-none"
            >
              <div class="news-card-img rounded-top-4 overflow-hidden">
                <img
                  :src="getPhotoUrl(item.image)"
                  :alt="item.title"
                  class="w-100 h-100 object-fit-cover"
                />
              </div>
            </router-link>
            <div
              class="p-3 p-md-4 d-flex flex-column justify-content-between h-auto"
            >
              <div>
                <div class="news-card-date mb-2">
                  {{ formatDate(item.published_at || item.created_at) }}
                </div>
                <h3 class="news-card-title mb-2">{{ item.title }}</h3>
                <p class="news-card-excerpt mb-3">
                  {{ item.summary || truncate(item.content) }}
                </p>
              </div>
              <div>
                <router-link
                  :to="{
                    name: 'news-detail',
                    params: { slug: item.slug || item.id },
                  }"
                  class="news-card-read"
                >
                  O'qish
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- EMPTY STATE -->
      <div v-if="!loading && news.length === 0" class="text-center py-5">
        <i class="bi bi-newspaper display-4 text-muted opacity-50"></i>
        <h4 class="fw-bold mt-3 text-secondary">Yangilik topilmadi</h4>
        <p class="text-muted mb-0">
          Hozircha hech qanday yangilik joylanmagan.
        </p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from "vue";
import newsService from "../../../services/newsService";

const backendUrl = import.meta.env.VITE_API_URL || "";
const news = ref([]);
const loading = ref(true);

const getPhotoUrl = (image) => {
  if (!image) {
    return "https://ui-avatars.com/api/?name=News&background=0284c7&color=fff&size=512";
  }
  return image.startsWith("http") ? image : backendUrl + image;
};

const formatDate = (dateStr) => {
  if (!dateStr) return "";
  return new Date(dateStr).toLocaleDateString("uz-Latn-UZ", {
    year: "numeric",
    month: "long",
    day: "2-digit",
  });
};

const truncate = (html) => {
  if (!html) return "";
  const text = html.replace(/<[^>]*>/g, "");
  return text.length > 120 ? text.slice(0, 120) + "..." : text;
};

const fetchNews = async () => {
  loading.value = true;
  try {
    const data = await newsService.fetchAll();
    const list =
      data?.data?.data || data?.data || (Array.isArray(data) ? data : []);
    news.value = list.filter((n) => n && (n.slug || n.id));
  } catch (err) {
    news.value = [];
  } finally {
    loading.value = false;
  }
};

onMounted(fetchNews);
</script>

<style scoped>
.design-three {
  color: #0f172a;
}

/* HERO VIDEO */
.news-hero-container {
  position: relative;
  width: 100%;
  height: 450px;
  min-height: 380px;
  display: flex;
  align-items: center;
  justify-content: center;
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
  z-index: 0;
  pointer-events: none;
}

.news-hero-overlay {
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

.news-hero-content {
  position: relative;
  z-index: 2;
  padding: 0 15px;
}

.max-w-xl {
  max-width: 720px;
}

.hero-title {
  font-size: clamp(1.5rem, 4vw, 3rem);
  font-weight: 800;
  font-family: "Outfit", sans-serif;
  line-height: 1.2;
}

/* MAIN CONTENT */
.news-main-wrap {
  position: relative;
  z-index: 3;
}

.news-title {
  font-size: clamp(1.75rem, 4vw, 3.5rem);
  letter-spacing: -1px;
  color: #0f172a;
}

.news-title span {
  color: #0284c7;
  font-family: Georgia, serif;
  font-style: italic;
  font-weight: 400;
}

/* CARDS */
.news-card {
  color: inherit;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  overflow: hidden;
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.news-card:hover {
  transform: translateY(-4px);
}

.news-card-img {
  height: 200px;
  background: #f1f5f9;
}

.news-card-img img {
  transition: transform 0.5s ease;
}

.news-card:hover .news-card-img img {
  transform: scale(1.05);
}

.news-card-date {
  color: #dc2626;
  font-size: 0.8rem;
  font-weight: 700;
}

.news-card-title {
  font-size: 1.05rem;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.4;
  transition: color 0.2s ease;
}

.news-card:hover .news-card-title {
  color: #0284c7;
}

.news-card-excerpt {
  color: #64748b;
  font-size: 0.875rem;
  line-height: 1.5;
}

.news-card-read {
  display: inline-block;
  color: #0284c7;
  font-weight: 700;
  font-size: 0.85rem;
  padding: 6px 16px;
  border: 1.5px solid #0284c7;
  border-radius: 8px;
  text-decoration: none;
  transition: all 0.2s ease;
}

.news-card-read:hover {
  background: #0284c7;
  color: white;
}

/* DARK MODE */
[data-theme="dark"] .design-three {
  background: #0f172a !important;
  color: #f1f5f9;
}

[data-theme="dark"] .news-card {
  background: #1e293b !important;
  border-color: rgba(255, 255, 255, 0.08);
}

[data-theme="dark"] .news-title,
[data-theme="dark"] .news-card-title {
  color: #f1f5f9;
}

[data-theme="dark"] .news-card-excerpt {
  color: #94a3b8;
}

[data-theme="dark"] .news-card:hover .news-card-title {
  color: #38bdf8;
}

/* RESPONSIVE */
@media (max-width: 768px) {
  .news-hero-container {
    height: 320px;
    min-height: 260px;
  }
  .news-card-img {
    height: 180px;
  }
}
</style>
