<template>
  <section class="section-pad py-5">
    <div class="mx-3 mx-lg-5">
      <!-- Header -->
      <div
        class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom flex-nowrap gap-2"
      >
        <div>
          <h2 class="name font-display fw-bold m-0">Klinika Yangiliklari</h2>
        </div>
        <router-link
          to="/news"
          class="all-news-link text-decoration-none text-danger fw-bold text-nowrap d-flex align-items-center gap-1"
        >
          <span>Barcha yangiliklar</span>
          <i class="bi bi-arrow-up-right"></i>
        </router-link>
      </div>

      <!-- Swiper Slider -->
      <div class="position-relative px-0 px-md-4">
        <Swiper
          v-if="news && news.length"
          :modules="[Navigation, Pagination, Autoplay]"
          :slides-per-view="1"
          :space-between="16"
          :loop="true"
          :autoplay="{
            delay: 4000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
          }"
          :navigation="{ prevEl: '.news-prev', nextEl: '.news-next' }"
          :pagination="{ clickable: true, el: '.news-pagination' }"
          :breakpoints="{
            576: { slidesPerView: 2, spaceBetween: 20 },
            992: { slidesPerView: 3, spaceBetween: 24 },
            1200: { slidesPerView: 4, spaceBetween: 24 },
          }"
          class="news-swiper py-2 position-relative"
        >
          <SwiperSlide
            v-for="item in news"
            :key="item.id"
            class="h-auto d-flex justify-content-center"
          >
            <div
              class="news-card h-100 d-flex flex-column bg-white rounded-4 overflow-hidden border position-relative"
            >
              <!-- Image -->
              <div
                class="news-img-wrap position-relative overflow-hidden bg-secondary-subtle"
              >
                <img
                  v-if="mediaUrl(item.image)"
                  :src="mediaUrl(item.image)"
                  :alt="item.title"
                  loading="lazy"
                  class="img-cover w-100 h-100 object-fit-cover card-img-zoom"
                />
                <div v-else class="img-placeholder">
                  <i class="bi bi-newspaper"></i>
                </div>
                <div class="position-absolute bottom-0 start-0 m-3 z-2">
                  <span
                    class="date-badge rounded-pill px-3 py-2 small fw-medium d-inline-flex align-items-center"
                  >
                    <i class="bi bi-calendar3 text-danger me-1"></i>
                    {{
                      formatDate(
                        item.published_at || item.created_at || item.date
                      )
                    }}
                  </span>
                </div>
              </div>

              <!-- Body -->
              <div class="p-4 d-flex flex-column flex-grow-1">
                <h5 class="fw-bold text-dark text-clamp-2 mb-2 fs-6">
                  {{ item.title }}
                </h5>
                <p class="text-secondary small text-clamp-3 flex-grow-1 mb-3">
                  {{ excerpt(item) }}
                </p>
                <!-- SLUG BO'YICHA ROUTER LINK -->
                <router-link
                  :to="'/news/' + (item.slug || item.id)"
                  class="read-link d-inline-flex align-items-center gap-2 fw-semibold text-decoration-none small mt-auto link-hover"
                >
                  <span>Batafsil ko'rish</span>
                  <i class="bi bi-arrow-right"></i>
                </router-link>
              </div>
            </div>
          </SwiperSlide>
        </Swiper>

        <!-- Skeleton Loading State -->
        <div v-else-if="store.loading || !loaded" class="row g-3 g-lg-4">
          <div
            v-for="n in 4"
            :key="n"
            class="col-12 col-sm-6 col-lg-4 col-xl-3"
            :class="{ 'd-none d-sm-block': n === 2, 'd-none d-lg-block': n === 3, 'd-none d-xl-block': n === 4 }"
          >
            <SkeletonCard variant="news" />
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-5 text-muted">
          <i
            class="bi bi-cloud-slash display-4 d-block mb-2 text-secondary"
          ></i>
          <p class="mb-0">Hozircha yangiliklar yo'q</p>
        </div>

        <!-- Custom Navigation Buttons -->
        <button v-show="news.length" class="news-prev news-nav-btn d-flex" aria-label="Oldingi">
          <i class="bi bi-chevron-left"></i>
        </button>
        <button v-show="news.length" class="news-next news-nav-btn d-flex" aria-label="Keyingi">
          <i class="bi bi-chevron-right"></i>
        </button>

        <!-- Custom Pagination -->
        <div
          class="news-pagination d-flex justify-content-center gap-2 mt-4"
        ></div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, Pagination, Autoplay } from "swiper/modules";

import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";

import { useNewsStore } from "../../../stores/news";
import SkeletonCard from "../../../components/ui/SkeletonCard.vue";
import { formatDate, truncate } from "../../../utils/formatters";
import { mediaUrl, stripHtml } from "../../../utils/media";

const store = useNewsStore();

const news = computed(() => {
  const list = store.news || [];
  return list.slice(0, 8);
});

// Kartada HTML teglar (<p>, <strong>) matn bo'lib ko'rinib qolmasin
const excerpt = (item) =>
  truncate(item.summary || item.excerpt || stripHtml(item.content), 140) ||
  "Yangilik haqida batafsil ma'lumot...";

// Birinchi yuklanishgacha "bo'sh" holat emas, skeleton ko'rinsin
const loaded = ref(false);

onMounted(async () => {
  await store.fetchNews();
  loaded.value = true;
});
</script>

<style scoped>
.news-img-wrap {
  height: 210px;
}

.news-img-wrap .img-cover {
  transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.news-card:hover .img-cover {
  transform: scale(1.06);
}

.img-placeholder {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.6rem;
  color: var(--clr-primary);
  opacity: 0.35;
  background: var(--clr-faint);
}

.date-badge {
  background: var(--clr-surface);
  color: var(--clr-text);
  border: 1px solid var(--clr-border);
}

.date-badge i {
  color: var(--clr-secondary);
}

.read-link {
  color: var(--clr-secondary);
}

.news-card {
  height: 420px !important;
  width: 100% !important;
  max-width: 360px;
  background: var(--clr-surface) !important;
  border-color: var(--clr-border) !important;
  box-shadow: none !important;
  transition: all 0.3s ease;
}

.news-card:hover {
  border-color: var(--clr-primary) !important;
  box-shadow: none !important;
}

.news-nav-btn {
  position: absolute;
  top: 45%;
  transform: translateY(-50%);
  z-index: 10;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: 1px solid var(--clr-border);
  background: var(--clr-surface);
  color: var(--clr-text);
  font-size: 0.85rem;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.25s ease;
}

.news-prev {
  left: 2px;
}

.news-next {
  right: 2px;
}

@media (min-width: 992px) {
  .news-nav-btn {
    width: 44px;
    height: 44px;
    font-size: 0.95rem;
  }
  .news-prev {
    left: -22px;
  }
  .news-next {
    right: -22px;
  }
}

.news-nav-btn:hover {
  background: var(--clr-primary);
  color: white;
  border-color: var(--clr-primary);
}

[data-theme="dark"] .name {
  color: #f8fafc !important;
}

.name {
  color: var(--primary-premium, #002b87) !important;
  font-size: 1.75rem;
}

.text-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.text-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.link-hover i {
  transition: transform 0.2s ease;
}

.link-hover:hover i {
  transform: translateX(4px);
}

@media (max-width: 768px) {
  .name {
    font-size: 1.25rem !important;
  }
  .all-news-link {
    font-size: 0.85rem !important;
  }
  .news-img-wrap {
    height: 180px;
  }
}
</style>
