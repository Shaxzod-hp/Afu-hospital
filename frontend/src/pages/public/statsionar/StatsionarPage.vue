<template>
  <div class="statsionar-page min-vh-100">
    <!-- Hero Section -->
    <section class="page-hero">
      <video class="hero-video" autoplay muted loop playsinline>
        <source src="/bg-videoo.mp4" type="video/mp4" />
      </video>
      <div class="hero-overlay"></div>
      <div class="container position-relative hero-content animate-fadeUp">
        <span class="hero-subtitle">Yotib Davolanish</span>
        <h1 class="hero-title">Statsionar Bo'limi</h1>
        <p class="hero-desc">
          Qulay va zamonaviy palatalarda, malakali tibbiy xodimlar nazorati
          ostida 24 soat davomida yotib davolanish xizmati.
        </p>
      </div>
    </section>

    <div class="container detail-content-wrap">
      <!-- Skeleton placeholders — stacked, same structure as real package blocks -->
      <div v-if="loading">
        <SkeletonCard v-for="n in 3" :key="n" variant="statsionar" />
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-5">
        <i class="bi bi-exclamation-triangle fs-2 text-danger mb-2 d-block"></i>
        <p class="fw-600 mb-3">{{ error }}</p>
        <button
          class="btn btn-outline-primary"
          @click="statsionarStore.fetchPackages()"
        >
          Qayta urinish
        </button>
      </div>

      <template v-else>
        <!-- Stacked Package Blocks -->
        <div v-if="packages.length > 0">
          <div
            v-for="(pkg, idx) in packages"
            :key="pkg.id"
            class="package-block"
          >
            <!-- Photo Gallery -->
            <div class="package-gallery">
              <div class="gallery-top-row">
                <router-link
                  :to="`/statsionar/${pkg.slug}`"
                  class="gallery-item large"
                  v-for="(photo, i) in getPhotos(pkg).slice(0, 2)"
                  :key="'top-' + i"
                >
                  <img :src="getPhotoUrl(photo)" :alt="pkg.name" />
                </router-link>
              </div>

              <div v-if="getPhotos(pkg).length > 2" class="gallery-bottom-row">
                <router-link
                  :to="`/statsionar/${pkg.slug}`"
                  class="gallery-item small"
                  v-for="(photo, i) in getPhotos(pkg).slice(2)"
                  :key="'bottom-' + i"
                >
                  <img :src="getPhotoUrl(photo)" :alt="pkg.name" />
                </router-link>
              </div>
            </div>

            <!-- Package Info -->
            <div class="package-info text-start">
              <router-link
                :to="`/statsionar/${pkg.slug}`"
                class="text-decoration-none"
              >
                <h3 class="fw-bold text-dark mb-2">{{ pkg.name }}</h3>
              </router-link>

              <p v-if="pkg.note" class="text-secondary mb-3">
                {{ pkg.note }}
              </p>

              <div
                v-if="pkg.included_items && pkg.included_items.length"
                class="mb-3 d-flex flex-wrap gap-3"
              >
                <div
                  v-for="(item, i) in pkg.included_items"
                  :key="i"
                  class="d-flex align-items-center gap-2 checkmark-row"
                >
                  <i class="bi bi-check-circle-fill check-icon"></i>
                  <span class="fw-semibold text-dark">{{ item }}</span>
                </div>
              </div>

              <div
                class="d-flex align-items-center justify-content-between pt-2"
              >
                <span v-if="pkg.price" class="fw-bold text-primary fs-5">
                  {{ formatPrice(pkg.price) }}
                </span>
                <span v-else class="text-secondary">Narx so'raladi</span>

                <a
                  href="tel:+998781222244"
                  class="btn btn-outline-primary btn-sm rounded-pill px-4"
                >
                  Bog'lanish
                </a>
              </div>
            </div>

            <hr v-if="idx < packages.length - 1" class="package-divider" />
          </div>
        </div>

        <div v-else class="text-center py-4 mb-4 text-secondary small">
          Hozircha statsionar paketlari kiritilmagan
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { useStatsionarStore } from "../../../stores/statsionar";
import SkeletonCard from "../../../components/ui/SkeletonCard.vue";

const statsionarStore = useStatsionarStore();

const loading = computed(() => statsionarStore.loading);
const error = computed(() => statsionarStore.error);
const packages = computed(() => statsionarStore.packages);

const backendUrl = import.meta.env.VITE_STORAGE_URL || "";

const getPhotos = (pkg) => (Array.isArray(pkg.photos) ? pkg.photos : []);

const getPhotoUrl = (photo) => {
  if (!photo)
    return "https://ui-avatars.com/api/?name=Statsionar&background=0284c7&color=fff";
  return photo.startsWith("http") ? photo : backendUrl + photo;
};

const formatPrice = (price) => {
  const n = Number(price);
  if (isNaN(n)) return "";
  return n.toLocaleString("uz-Latn-UZ") + " so'm";
};

onMounted(async () => {
  window.scrollTo({ top: 0, behavior: "smooth" });
  await statsionarStore.fetchPackages();
});
</script>

<style scoped>
/* ── HERO BANNER SECTION ── */
.page-hero {
  position: relative;
  width: 100%;
  height: 450px;
  min-height: 420px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  text-align: center;
  overflow: hidden;
  background-color: #001440;
}

.hero-video {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 100vw;
  height: 100vh;
  min-width: 100%;
  min-height: 100%;
  object-fit: cover;
  transform: translate(-50%, -50%);
  z-index: 0;
  pointer-events: none;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(
    135deg,
    rgba(0, 43, 135, 0.65) 0%,
    rgba(0, 20, 70, 0.8) 100%
  );
  z-index: 1;
  pointer-events: none;
}

.hero-content {
  z-index: 2;
  padding: 0 15px;
}

.hero-subtitle {
  font-size: 0.85rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 3px;
  color: rgba(255, 255, 255, 0.9);
  display: inline-block;
  margin-bottom: 12px;
}

.hero-title {
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 800;
  font-family: "Outfit", sans-serif;
  margin-bottom: 12px;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.hero-desc {
  color: rgba(255, 255, 255, 0.92);
  font-size: 1.05rem;
  max-width: 650px;
  margin: 0 auto;
  line-height: 1.6;
}

/* ── CONTENT WRAPPER ── */
.detail-content-wrap {
  padding: 40px 0 60px;
}

.fw-600 {
  font-weight: 600;
}

/* ── PACKAGE BLOCK (stacked) ── */
.package-block {
  margin-bottom: 40px;
}

.package-divider {
  border: none;
  border-top: 1px solid rgba(0, 0, 0, 0.1);
  margin: 40px 0;
}

/* ── PHOTO GALLERY ── */
.package-gallery {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 24px;
}

.gallery-top-row {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  height: 420px;
}

.gallery-bottom-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  height: 240px;
}

.gallery-item {
  position: relative;
  display: block;
  overflow: hidden;
  border-radius: 16px;
  background: #f1f5f9;
}

.gallery-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.gallery-item:hover img {
  transform: scale(1.05);
}

/* Only one photo in top row → make it full width */
.gallery-top-row .gallery-item:only-child {
  grid-column: 1 / -1;
}

/* ── PACKAGE INFO ── */
.package-info {
  padding: 0 4px;
}

.checkmark-row {
  padding: 2px 0;
}

.check-icon {
  color: #0284c7;
  font-size: 1rem;
  flex-shrink: 0;
}

/* ── RESPONSIVE DESIGN ── */
@media (max-width: 991px) {
  .page-hero {
    height: 360px;
    min-height: 360px;
  }
  .gallery-top-row {
    height: 320px;
  }
  .gallery-bottom-row {
    height: 180px;
  }
}

@media (max-width: 768px) {
  .page-hero {
    height: 320px;
    min-height: 300px;
  }
  .hero-desc {
    font-size: 0.95rem;
  }
  .gallery-top-row {
    grid-template-columns: 1fr;
    height: auto;
  }
  .gallery-top-row .gallery-item {
    height: 240px;
  }
  .gallery-bottom-row {
    grid-template-columns: repeat(3, 1fr);
    height: 130px;
  }
  .detail-content-wrap {
    padding: 10px;
  }
}

@media (max-width: 576px) {
  .page-hero {
    height: 320px;
    min-height: 260px;
  }
  .hero-subtitle {
    letter-spacing: 2px;
    padding: 5px;
  }
  .gallery-bottom-row {
    grid-template-columns: repeat(2, 1fr);
    height: auto;
  }
  .gallery-bottom-row .gallery-item {
    height: 140px;
  }
}
</style>
