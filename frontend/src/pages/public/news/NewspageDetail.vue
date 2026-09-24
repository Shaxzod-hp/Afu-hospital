<template>
  <div class="news-detail-page">
    <PageHero
      size="compact"
      subtitle="Yangiliklar"
      :title="news?.title || 'Klinika yangiliklari'"
    />

    <section class="detail-body">
      <div class="container">
        <!-- XATO -->
        <div v-if="error" class="state-box">
          <i class="bi bi-exclamation-triangle"></i>
          <h4>{{ error }}</h4>
          <router-link to="/news" class="btn btn-clinic-primary mt-2">
            <i class="bi bi-arrow-left me-1"></i> Yangiliklarga qaytish
          </router-link>
        </div>

        <div v-else class="row g-4 g-xl-5">
          <!-- ── MAQOLA ── -->
          <div class="col-12 col-lg-8">
            <SkeletonCard v-if="loading" variant="article" class="article-card" />

            <article v-else-if="news" class="article-card">
              <router-link to="/news" class="back-link">
                <i class="bi bi-arrow-left"></i> Barcha yangiliklar
              </router-link>

              <div class="article-meta">
                <span v-if="news.category?.name" class="news-cat">
                  {{ news.category.name }}
                </span>
                <span class="meta-item">
                  <i class="bi bi-calendar3"></i>
                  {{ formatDate(news.published_at || news.created_at) }}
                </span>
                <span class="meta-item">
                  <i class="bi bi-clock"></i> {{ minutes }} daqiqa o'qish
                </span>
                <span v-if="news.views" class="meta-item">
                  <i class="bi bi-eye"></i> {{ news.views }}
                </span>
              </div>

              <figure v-if="mediaUrl(news.image)" class="article-image">
                <img :src="mediaUrl(news.image)" :alt="news.title" />
              </figure>

              <p v-if="news.summary" class="article-summary">
                {{ news.summary }}
              </p>

              <div class="article-content" v-html="safeContent"></div>

              <!-- ULASHISH -->
              <div class="share-bar">
                <span class="share-label">Ulashish:</span>
                <a
                  :href="`https://t.me/share/url?url=${encodedUrl}&text=${encodedTitle}`"
                  target="_blank"
                  rel="noopener"
                  class="share-btn telegram"
                  aria-label="Telegram orqali ulashish"
                >
                  <i class="bi bi-telegram"></i>
                </a>
                <a
                  :href="`https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`"
                  target="_blank"
                  rel="noopener"
                  class="share-btn facebook"
                  aria-label="Facebook orqali ulashish"
                >
                  <i class="bi bi-facebook"></i>
                </a>
                <button
                  type="button"
                  class="share-btn copy"
                  :aria-label="copied ? 'Nusxalandi' : 'Havolani nusxalash'"
                  @click="copyLink"
                >
                  <i :class="copied ? 'bi bi-check2' : 'bi bi-link-45deg'"></i>
                </button>
                <span v-if="copied" class="copied-note">Havola nusxalandi</span>
              </div>
            </article>
          </div>

          <!-- ── YON PANEL: SO'NGGI YANGILIKLAR ── -->
          <aside class="col-12 col-lg-4">
            <div class="side-card">
              <h5 class="side-title">So'nggi yangiliklar</h5>

              <template v-if="latestLoading">
                <div v-for="n in 4" :key="n" class="side-item">
                  <span class="sk side-thumb"></span>
                  <div class="flex-grow-1">
                    <span class="sk sk-text" style="width: 40%"></span>
                    <span class="sk sk-text"></span>
                    <span class="sk sk-text mb-0" style="width: 75%"></span>
                  </div>
                </div>
              </template>

              <template v-else-if="latest.length">
                <router-link
                  v-for="item in latest"
                  :key="item.id"
                  :to="{ name: 'news-detail', params: { slug: item.slug || item.id } }"
                  class="side-item side-link"
                >
                  <div class="side-thumb">
                    <img
                      v-if="mediaUrl(item.image)"
                      :src="mediaUrl(item.image)"
                      :alt="item.title"
                      loading="lazy"
                    />
                    <i v-else class="bi bi-newspaper"></i>
                  </div>
                  <div class="min-w-0">
                    <div class="side-date">
                      {{ formatDate(item.published_at || item.created_at) }}
                    </div>
                    <div class="side-item-title">{{ item.title }}</div>
                  </div>
                </router-link>
              </template>

              <p v-else class="side-empty">Boshqa yangiliklar yo'q.</p>

              <router-link to="/news" class="side-all">
                Barcha yangiliklar <i class="bi bi-arrow-right"></i>
              </router-link>
            </div>
          </aside>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRoute } from "vue-router";
import DOMPurify from "dompurify";
import PageHero from "../../../components/PageHero.vue";
import SkeletonCard from "../../../components/ui/SkeletonCard.vue";
import newsService from "../../../services/newsService";
import { formatDate } from "../../../utils/formatters";
import { mediaUrl, readingTime } from "../../../utils/media";

const route = useRoute();

const news = ref(null);
const loading = ref(true);
const error = ref(null);

const latestRaw = ref([]);
const latestLoading = ref(true);

const slugOrId = computed(() => route.params.slug || route.params.id);

// Yangilik matnidagi <script>, onerror= va h.k. zararli kodni olib tashlaymiz
const safeContent = computed(() =>
  DOMPurify.sanitize(news.value?.content || "")
);

const minutes = computed(() => readingTime(news.value?.content));

// Yon panelda joriy yangilik takrorlanmasin
const latest = computed(() =>
  latestRaw.value
    .filter((n) => String(n.slug || n.id) !== String(slugOrId.value) && n.id !== news.value?.id)
    .slice(0, 4)
);

// route.fullPath ga bog'liq — boshqa yangilikka o'tganda ulashish havolasi ham yangilanadi
const pageUrl = computed(() => window.location.origin + route.fullPath);
const encodedUrl = computed(() => encodeURIComponent(pageUrl.value));
const encodedTitle = computed(() => encodeURIComponent(news.value?.title || ""));

const copied = ref(false);
const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(pageUrl.value);
    copied.value = true;
    setTimeout(() => (copied.value = false), 2000);
  } catch {
    // clipboard ruxsati bo'lmasa jim o'tamiz
  }
};

const loadNews = async () => {
  loading.value = true;
  error.value = null;
  news.value = null;

  if (!slugOrId.value) {
    error.value = "Yangilik manzili noto'g'ri kiritilgan.";
    loading.value = false;
    return;
  }

  try {
    const data = await newsService.fetchOne(slugOrId.value);
    // Laravel response ba'zan data.data yoki to'g'ridan-to'g'ri obyekt bo'ladi
    news.value = data?.data || data;
    window.scrollTo({ top: 0, behavior: "smooth" });
  } catch (err) {
    error.value =
      err?.response?.status === 404
        ? "Bunday yangilik topilmadi."
        : "Yangilikni yuklashda xatolik yuz berdi.";
  } finally {
    loading.value = false;
  }
};

const loadLatest = async () => {
  latestLoading.value = true;
  try {
    const res = await newsService.fetchPage(1, 5);
    latestRaw.value = Array.isArray(res?.data) ? res.data : [];
  } catch {
    latestRaw.value = [];
  } finally {
    latestLoading.value = false;
  }
};

// Route o'zgarganda (boshqa yangilikka o'tganda) qayta yuklash
watch(slugOrId, loadNews);

onMounted(() => {
  loadNews();
  loadLatest();
});
</script>

<style scoped>
.news-detail-page {
  background: var(--clr-bg);
}

.detail-body {
  padding: 48px 0 80px;
}

/* ── MAQOLA KARTASI ── */
.article-card {
  display: block;
  background: var(--clr-surface);
  border: 1px solid var(--clr-border);
  border-radius: 24px;
  padding: 40px;
  box-shadow: var(--shadow-sm);
  min-width: 0;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: var(--clr-primary);
  font-weight: 600;
  font-size: 0.9rem;
  text-decoration: none;
  margin-bottom: 20px;
}

.back-link i {
  transition: transform 0.2s ease;
}

.back-link:hover i {
  transform: translateX(-4px);
}

.article-meta {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 10px 18px;
  padding-bottom: 20px;
  margin-bottom: 24px;
  border-bottom: 1px solid var(--clr-border);
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

.meta-item {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--clr-muted);
  font-size: 0.85rem;
  font-weight: 600;
}

.meta-item i {
  color: var(--clr-secondary);
}

.article-image {
  margin: 0 0 28px;
  border-radius: 18px;
  overflow: hidden;
  background: var(--clr-faint);
}

.article-image img {
  display: block;
  width: 100%;
  max-height: 520px;
  object-fit: cover;
}

.article-summary {
  font-size: 1.12rem;
  font-weight: 600;
  line-height: 1.7;
  color: var(--clr-text);
  border-left: 4px solid var(--clr-secondary);
  background: var(--clr-faint);
  border-radius: 0 12px 12px 0;
  padding: 16px 20px;
  margin-bottom: 28px;
}

/* ── MAQOLA MATNI (admin kiritgan HTML) ── */
.article-content {
  font-size: 1.04rem;
  line-height: 1.85;
  color: var(--clr-text);
  overflow-wrap: anywhere;
}

.article-content :deep(p) {
  margin-bottom: 1.1em;
}

.article-content :deep(h2),
.article-content :deep(h3),
.article-content :deep(h4) {
  font-family: var(--font-display);
  font-weight: 700;
  color: var(--clr-text);
  margin: 1.6em 0 0.6em;
  line-height: 1.3;
}

.article-content :deep(h2) { font-size: 1.5rem; }
.article-content :deep(h3) { font-size: 1.25rem; }

.article-content :deep(a) {
  color: var(--clr-primary);
  text-decoration: underline;
  text-underline-offset: 3px;
}

.article-content :deep(ul),
.article-content :deep(ol) {
  padding-left: 1.4em;
  margin-bottom: 1.1em;
}

.article-content :deep(li) {
  margin-bottom: 0.4em;
}

.article-content :deep(li)::marker {
  color: var(--clr-secondary);
}

.article-content :deep(blockquote) {
  border-left: 4px solid var(--clr-primary);
  padding: 8px 0 8px 18px;
  margin: 1.4em 0;
  color: var(--clr-muted);
  font-style: italic;
}

.article-content :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: 14px;
  margin: 1rem 0;
}

.article-content :deep(iframe),
.article-content :deep(video) {
  max-width: 100%;
  width: 100%;
  aspect-ratio: 16 / 9;
  height: auto;
  border: 0;
  border-radius: 14px;
  margin: 1rem 0;
}

/* Jadval telefonda sahifani cho'zmasin — o'zi scroll bo'ladi */
.article-content :deep(table) {
  display: block;
  max-width: 100%;
  overflow-x: auto;
  border-collapse: collapse;
  margin: 1.2em 0;
}

.article-content :deep(th),
.article-content :deep(td) {
  border: 1px solid var(--clr-border);
  padding: 8px 12px;
}

.article-content :deep(th) {
  background: var(--clr-faint);
}

/* ── ULASHISH ── */
.share-bar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 10px;
  margin-top: 36px;
  padding-top: 22px;
  border-top: 1px solid var(--clr-border);
}

.share-label {
  font-weight: 700;
  color: var(--clr-text);
  margin-right: 4px;
}

.share-btn {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  border: 1px solid var(--clr-border);
  background: var(--clr-faint);
  color: var(--clr-text);
  text-decoration: none;
  transition: var(--transition);
}

.share-btn:hover {
  transform: translateY(-2px);
  color: #fff;
}

.share-btn.telegram:hover { background: #0088cc; border-color: #0088cc; }
.share-btn.facebook:hover { background: #1877f2; border-color: #1877f2; }
.share-btn.copy:hover { background: var(--clr-primary); border-color: var(--clr-primary); }

.copied-note {
  font-size: 0.85rem;
  font-weight: 600;
  color: #16a34a;
}

/* ── YON PANEL ── */
.side-card {
  position: sticky;
  top: 100px;
  background: var(--clr-surface);
  border: 1px solid var(--clr-border);
  border-radius: 20px;
  padding: 24px;
  box-shadow: var(--shadow-sm);
}

.side-title {
  font-family: var(--font-display);
  font-weight: 700;
  color: var(--clr-text);
  padding-bottom: 14px;
  margin-bottom: 8px;
  border-bottom: 2px solid var(--clr-secondary);
  display: inline-block;
}

.side-item {
  display: flex;
  gap: 14px;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid var(--clr-border);
}

.side-link {
  text-decoration: none;
  color: inherit;
}

.side-thumb {
  width: 84px;
  height: 64px;
  flex-shrink: 0;
  border-radius: 12px;
  overflow: hidden;
  background: var(--clr-faint);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--clr-primary);
  opacity: 1;
}

.side-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.side-link:hover .side-thumb img {
  transform: scale(1.08);
}

.min-w-0 {
  min-width: 0;
}

.side-date {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--clr-secondary);
  margin-bottom: 4px;
}

.side-item-title {
  font-size: 0.9rem;
  font-weight: 700;
  line-height: 1.4;
  color: var(--clr-text);
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  transition: color 0.2s ease;
}

.side-link:hover .side-item-title {
  color: var(--clr-primary);
}

.side-empty {
  color: var(--clr-muted);
  padding: 12px 0;
  margin: 0;
}

.side-all {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin-top: 18px;
  color: var(--clr-secondary);
  font-weight: 700;
  font-size: 0.9rem;
  text-decoration: none;
}

.side-all i {
  transition: transform 0.2s ease;
}

.side-all:hover i {
  transform: translateX(4px);
}

/* ── XATO HOLATI ── */
.state-box {
  text-align: center;
  padding: 64px 16px;
}

.state-box i {
  font-size: 3rem;
  color: var(--clr-secondary);
}

.state-box h4 {
  color: var(--clr-text);
  font-weight: 700;
  margin: 16px 0;
}

/* ── DARK MODE ── */
[data-theme="dark"] .news-cat {
  background: rgba(59, 130, 246, 0.15);
  color: #93c5fd;
}

[data-theme="dark"] .back-link,
[data-theme="dark"] .article-content :deep(a),
[data-theme="dark"] .side-link:hover .side-item-title {
  color: #93c5fd;
}

[data-theme="dark"] .article-content {
  color: #cbd5e1;
}

[data-theme="dark"] .article-content :deep(strong),
[data-theme="dark"] .article-content :deep(b) {
  color: #f8fafc;
}

/* ── RESPONSIVE ── */
@media (max-width: 991px) {
  .side-card {
    position: static;
  }
}

@media (max-width: 767px) {
  .detail-body {
    padding: 24px 0 56px;
  }
  .article-card {
    padding: 20px;
    border-radius: 18px;
  }
  .article-image {
    margin-left: -8px;
    margin-right: -8px;
    border-radius: 14px;
  }
  .article-image img {
    max-height: 300px;
  }
  .article-summary {
    font-size: 1rem;
    padding: 12px 16px;
  }
  .article-content {
    font-size: 0.98rem;
    line-height: 1.8;
  }
  .side-card {
    padding: 18px;
  }
}
</style>
