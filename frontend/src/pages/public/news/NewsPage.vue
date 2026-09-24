<template>
  <div class="news-page">
    <PageHero
      subtitle="Yangiliklar"
      title="Klinika yangiliklari"
      description="Eng so'nggi va ishonchli tibbiy yangiliklar, tadqiqotlar hamda klinika axborotlari."
    />

    <section class="news-body">
      <div class="container">
        <!-- SKELETON -->
        <template v-if="loading">
          <div class="sk-surface featured-skeleton mb-4 mb-lg-5 d-none d-md-flex">
            <div class="sk featured-skeleton-img"></div>
            <div class="flex-grow-1 p-4 p-lg-5">
              <span class="sk sk-text" style="width: 30%"></span>
              <span class="sk sk-title" style="width: 90%"></span>
              <span class="sk sk-title mb-4" style="width: 60%"></span>
              <span class="sk sk-text"></span>
              <span class="sk sk-text"></span>
              <span class="sk sk-text" style="width: 70%"></span>
            </div>
          </div>
          <div class="row g-3 g-md-4">
            <div v-for="n in 6" :key="n" class="col-12 col-md-6 col-lg-4">
              <SkeletonCard variant="news" />
            </div>
          </div>
        </template>

        <!-- XATO -->
        <div v-else-if="error" class="state-box">
          <i class="bi bi-wifi-off"></i>
          <h4>Yangiliklarni yuklab bo'lmadi</h4>
          <p>Internet aloqasini tekshirib, qaytadan urinib ko'ring.</p>
          <button class="btn btn-clinic-primary" @click="fetchNews">
            <i class="bi bi-arrow-clockwise me-1"></i> Qayta urinish
          </button>
        </div>

        <!-- BO'SH -->
        <div v-else-if="!news.length" class="state-box">
          <i class="bi bi-newspaper"></i>
          <h4>Hozircha yangiliklar yo'q</h4>
          <p>Tez orada bu yerda klinikamiz yangiliklari paydo bo'ladi.</p>
        </div>

        <template v-else>
          <!-- ASOSIY (eng so'nggi) YANGILIK -->
          <router-link
            v-if="featured"
            :to="detailLink(featured)"
            class="featured-card mb-4 mb-lg-5"
          >
            <div class="featured-img">
              <img
                v-if="mediaUrl(featured.image)"
                :src="mediaUrl(featured.image)"
                :alt="featured.title"
                loading="eager"
              />
              <div v-else class="img-placeholder"><i class="bi bi-newspaper"></i></div>
              <span class="featured-badge">
                <i class="bi bi-lightning-charge-fill"></i> So'nggi yangilik
              </span>
            </div>
            <div class="featured-body">
              <div class="news-meta mb-3">
                <span v-if="featured.category?.name" class="news-cat">
                  {{ featured.category.name }}
                </span>
                <span class="news-date">
                  <i class="bi bi-calendar3"></i>
                  {{ formatDate(featured.published_at || featured.created_at) }}
                </span>
              </div>
              <h2 class="featured-title">{{ featured.title }}</h2>
              <p class="featured-excerpt">{{ excerpt(featured, 240) }}</p>
              <span class="read-more">
                Batafsil o'qish <i class="bi bi-arrow-right"></i>
              </span>
            </div>
          </router-link>

          <!-- QOLGAN YANGILIKLAR -->
          <div v-if="rest.length" class="row g-3 g-md-4">
            <div
              v-for="item in rest"
              :key="item.id"
              class="col-12 col-md-6 col-lg-4"
            >
              <router-link :to="detailLink(item)" class="news-card">
                <div class="news-card-img">
                  <img
                    v-if="mediaUrl(item.image)"
                    :src="mediaUrl(item.image)"
                    :alt="item.title"
                    loading="lazy"
                  />
                  <div v-else class="img-placeholder"><i class="bi bi-newspaper"></i></div>
                  <span v-if="item.category?.name" class="news-cat news-cat--floating">
                    {{ item.category.name }}
                  </span>
                </div>
                <div class="news-card-body">
                  <div class="news-meta mb-2">
                    <span class="news-date">
                      <i class="bi bi-calendar3"></i>
                      {{ formatDate(item.published_at || item.created_at) }}
                    </span>
                    <span v-if="item.views" class="news-views">
                      <i class="bi bi-eye"></i> {{ item.views }}
                    </span>
                  </div>
                  <h3 class="news-card-title">{{ item.title }}</h3>
                  <p class="news-card-excerpt">{{ excerpt(item) }}</p>
                  <span class="read-more mt-auto">
                    Batafsil <i class="bi bi-arrow-right"></i>
                  </span>
                </div>
              </router-link>
            </div>

            <!-- "Ko'proq" bosilganda yuklanayotgan kartalar -->
            <template v-if="loadingMore">
              <div v-for="n in 3" :key="'more-' + n" class="col-12 col-md-6 col-lg-4">
                <SkeletonCard variant="news" />
              </div>
            </template>
          </div>

          <!-- KO'PROQ YUKLASH -->
          <div v-if="currentPage < lastPage" class="text-center mt-4 mt-lg-5">
            <button
              class="btn load-more-btn"
              :disabled="loadingMore"
              @click="loadMore"
            >
              <span v-if="loadingMore" class="spinner-border spinner-border-sm me-2"></span>
              Ko'proq yangiliklar
              <i v-if="!loadingMore" class="bi bi-chevron-down ms-1"></i>
            </button>
          </div>
        </template>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import PageHero from "../../../components/PageHero.vue";
import SkeletonCard from "../../../components/ui/SkeletonCard.vue";
import newsService from "../../../services/newsService";
import { formatDate, truncate } from "../../../utils/formatters";
import { mediaUrl, stripHtml } from "../../../utils/media";

const news = ref([]);
const loading = ref(true);
const error = ref(false);
const currentPage = ref(1);
const lastPage = ref(1);
const loadingMore = ref(false);

const featured = computed(() => news.value[0] || null);
const rest = computed(() => news.value.slice(1));

const detailLink = (item) => ({
  name: "news-detail",
  params: { slug: item.slug || item.id },
});

// Kartada HTML teglar ko'rinib qolmasligi uchun faqat toza matn
const excerpt = (item, n = 130) =>
  truncate(item.summary || stripHtml(item.content), n);

const loadPage = async (page) => {
  const res = await newsService.fetchPage(page, 12);
  currentPage.value = res?.current_page || 1;
  lastPage.value = res?.last_page || 1;
  const list = Array.isArray(res?.data) ? res.data : [];
  return list.filter((n) => n && (n.slug || n.id));
};

const fetchNews = async () => {
  loading.value = true;
  error.value = false;
  try {
    news.value = await loadPage(1);
  } catch {
    error.value = true;
  } finally {
    loading.value = false;
  }
};

const loadMore = async () => {
  loadingMore.value = true;
  try {
    const more = await loadPage(currentPage.value + 1);
    const ids = new Set(news.value.map((n) => n.id));
    news.value.push(...more.filter((n) => !ids.has(n.id)));
  } catch {
    // tugma qayta bosilishi mumkin
  } finally {
    loadingMore.value = false;
  }
};

onMounted(fetchNews);
</script>

<style scoped>
.news-page {
  background: var(--clr-bg);
}

.news-body {
  padding: 56px 0 80px;
}

/* ── ASOSIY YANGILIK ── */
.featured-card {
  display: grid;
  grid-template-columns: 1.15fr 1fr;
  background: var(--clr-surface);
  border: 1px solid var(--clr-border);
  border-radius: 24px;
  overflow: hidden;
  text-decoration: none;
  color: inherit;
  box-shadow: var(--shadow-sm);
  transition: transform 0.3s var(--ease-out), box-shadow 0.3s var(--ease-out);
}

.featured-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
}

.featured-img {
  position: relative;
  min-height: 380px;
  overflow: hidden;
  background: var(--clr-faint);
}

.featured-img img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.7s var(--ease-out);
}

.featured-card:hover .featured-img img {
  transform: scale(1.04);
}

.featured-badge {
  position: absolute;
  top: 18px;
  left: 18px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: var(--clr-secondary);
  color: #fff;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.3px;
  padding: 7px 14px;
  border-radius: 50px;
  box-shadow: 0 6px 18px rgba(227, 30, 36, 0.35);
}

.featured-body {
  padding: 44px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  min-width: 0;
}

.featured-title {
  font-family: var(--font-display);
  font-size: clamp(1.4rem, 2.4vw, 2rem);
  font-weight: 800;
  line-height: 1.25;
  color: var(--clr-text);
  margin-bottom: 14px;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.featured-excerpt {
  color: var(--clr-muted);
  font-size: 0.98rem;
  line-height: 1.7;
  margin-bottom: 24px;
  display: -webkit-box;
  -webkit-line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* ── META ── */
.news-meta {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 8px 14px;
  font-size: 0.8rem;
}

.news-date,
.news-views {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--clr-muted);
  font-weight: 600;
}

.news-date i {
  color: var(--clr-secondary);
}

.news-cat {
  display: inline-block;
  background: rgba(0, 43, 135, 0.08);
  color: var(--clr-primary);
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  padding: 5px 12px;
  border-radius: 50px;
}

.news-cat--floating {
  position: absolute;
  top: 14px;
  left: 14px;
  background: rgba(255, 255, 255, 0.95);
  color: var(--clr-primary);
  backdrop-filter: blur(6px);
}

/* ── KARTALAR ── */
.news-card {
  display: flex;
  flex-direction: column;
  height: 100%;
  background: var(--clr-surface);
  border: 1px solid var(--clr-border);
  border-radius: 20px;
  overflow: hidden;
  text-decoration: none;
  color: inherit;
  transition: transform 0.3s var(--ease-out), box-shadow 0.3s var(--ease-out),
    border-color 0.3s ease;
}

.news-card:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-lg);
  border-color: transparent;
}

.news-card-img {
  position: relative;
  height: 220px;
  overflow: hidden;
  background: var(--clr-faint);
}

.news-card-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s var(--ease-out);
}

.news-card:hover .news-card-img img {
  transform: scale(1.06);
}

.img-placeholder {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  color: var(--clr-primary);
  opacity: 0.35;
}

.news-card-body {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  padding: 22px;
}

.news-card-title {
  font-family: var(--font-display);
  font-size: 1.1rem;
  font-weight: 700;
  line-height: 1.4;
  color: var(--clr-text);
  margin-bottom: 10px;
  transition: color 0.2s ease;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.news-card:hover .news-card-title {
  color: var(--clr-primary);
}

.news-card-excerpt {
  color: var(--clr-muted);
  font-size: 0.9rem;
  line-height: 1.6;
  margin-bottom: 18px;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.read-more {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: var(--clr-secondary);
  font-weight: 700;
  font-size: 0.9rem;
}

.read-more i {
  transition: transform 0.25s ease;
}

.news-card:hover .read-more i,
.featured-card:hover .read-more i {
  transform: translateX(5px);
}

/* ── KO'PROQ TUGMASI ── */
.load-more-btn {
  border: 2px solid var(--clr-primary);
  color: var(--clr-primary);
  background: transparent;
  border-radius: 50px;
  font-weight: 700;
  padding: 0.7rem 2rem;
  transition: var(--transition);
}

.load-more-btn:hover:not(:disabled) {
  background: var(--clr-primary);
  color: #fff;
}

/* ── BO'SH / XATO HOLATI ── */
.state-box {
  text-align: center;
  padding: 64px 16px;
  color: var(--clr-muted);
}

.state-box i {
  font-size: 3rem;
  color: var(--clr-primary);
  opacity: 0.5;
}

.state-box h4 {
  color: var(--clr-text);
  font-weight: 700;
  margin: 16px 0 8px;
}

.state-box p {
  margin-bottom: 20px;
}

/* ── SKELETON (asosiy karta) ── */
.featured-skeleton {
  min-height: 380px;
}

.featured-skeleton-img {
  width: 53%;
  border-radius: 0;
}

/* ── DARK MODE ── */
[data-theme="dark"] .news-cat {
  background: rgba(59, 130, 246, 0.15);
  color: #93c5fd;
}

[data-theme="dark"] .news-cat--floating {
  background: rgba(15, 23, 42, 0.85);
  color: #93c5fd;
}

[data-theme="dark"] .featured-title,
[data-theme="dark"] .news-card-title {
  color: #f1f5f9 !important;
}

[data-theme="dark"] .news-card:hover .news-card-title {
  color: #93c5fd !important;
}

[data-theme="dark"] .news-card:hover,
[data-theme="dark"] .featured-card:hover {
  border-color: var(--clr-primary);
}

[data-theme="dark"] .load-more-btn {
  border-color: #60a5fa;
  color: #93c5fd;
}

[data-theme="dark"] .load-more-btn:hover:not(:disabled) {
  background: #3b82f6;
  border-color: #3b82f6;
  color: #fff;
}

/* ── RESPONSIVE ── */
@media (max-width: 991px) {
  .featured-body {
    padding: 32px;
  }
  .featured-img {
    min-height: 320px;
  }
}

@media (max-width: 767px) {
  .news-body {
    padding: 32px 0 56px;
  }
  .featured-card {
    grid-template-columns: 1fr;
    border-radius: 20px;
  }
  .featured-img {
    min-height: 0;
    aspect-ratio: 16 / 10;
  }
  .featured-body {
    padding: 22px;
  }
  .featured-excerpt {
    -webkit-line-clamp: 3;
    margin-bottom: 16px;
  }
  .news-card-img {
    height: 200px;
  }
  .news-card-body {
    padding: 18px;
  }
}
</style>
