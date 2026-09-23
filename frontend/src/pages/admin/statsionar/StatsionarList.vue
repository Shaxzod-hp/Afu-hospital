<template>
  <div class="management-page">
    <div
      class="filter-card glass-panel mb-4 d-flex align-items-center justify-content-between gap-3"
    >
      <div class="search-input flex-grow-1">
        <i class="fas fa-search search-icon"></i>
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Paket nomi bo'yicha qidirish..."
        />
      </div>
      <router-link
        to="/admin/statsionar/create"
        class="btn-primary-glass text-decoration-none"
      >
        <i class="fas fa-plus me-2"></i> Yangi paket
      </router-link>
    </div>

    <!-- State: Loading -->
    <div v-if="loading" class="glass-panel text-center py-5">
      <div class="spinner-border text-primary" role="status"></div>
      <div class="mt-2 text-muted-glass small">Yuklanmoqda...</div>
    </div>

    <!-- State: Error -->
    <div v-else-if="error" class="glass-panel text-center py-5">
      <div class="error-state text-danger">
        <i class="fas fa-exclamation-triangle mb-2 fs-3"></i>
        <p class="m-0 fw-700">{{ error }}</p>
        <button
          class="btn btn-sm btn-outline-danger mt-3"
          @click="fetchPackages"
        >
          <i class="fas fa-redo me-1"></i> Qayta urinish
        </button>
      </div>
    </div>

    <!-- State: Empty -->
    <div
      v-else-if="filteredPackages.length === 0"
      class="glass-panel text-center py-5"
    >
      <div class="empty-state">
        <i class="fas fa-bed mb-3 fs-1 opacity-50"></i>
        <p class="m-0 fw-600">Hozircha statsionar paketlari kiritilmagan</p>
      </div>
    </div>

    <!-- State: Card grid -->
    <div v-else class="packages-grid">
      <div
        v-for="pkg in filteredPackages"
        :key="pkg.id"
        class="package-card glass-panel"
      >
        <!-- Slideshow -->
        <div
          class="package-slideshow"
          @mouseenter="pauseSlide(pkg.id)"
          @mouseleave="resumeSlide(pkg.id)"
        >
          <img
            v-for="(photo, i) in getPhotos(pkg)"
            :key="i"
            :src="getPhotoUrl(photo)"
            :alt="pkg.name"
            class="slide-img"
            :class="{ active: (slideIndex[pkg.id] || 0) === i }"
          />
          <div v-if="getPhotos(pkg).length > 1" class="slide-dots">
            <span
              v-for="(photo, i) in getPhotos(pkg)"
              :key="i"
              class="dot"
              :class="{ active: (slideIndex[pkg.id] || 0) === i }"
              @click="slideIndex[pkg.id] = i"
            ></span>
          </div>
          <button
            class="delete-overlay-btn"
            @click.stop="handleDelete(pkg.id)"
            title="O'chirish"
          >
            <i class="fas fa-trash-alt"></i>
          </button>
        </div>

        <!-- Body -->
        <div class="package-body">
          <h6 class="package-title">{{ pkg.name }}</h6>

          <p v-if="pkg.note" class="package-note">{{ pkg.note }}</p>

          <div
            v-if="pkg.included_items && pkg.included_items.length"
            class="package-items"
          >
            <span
              v-for="(item, i) in pkg.included_items.slice(0, 3)"
              :key="i"
              class="item-chip"
            >
              {{ item }}
            </span>
            <span v-if="pkg.included_items.length > 3" class="item-chip more">
              +{{ pkg.included_items.length - 3 }}
            </span>
          </div>

          <div class="package-footer">
            <span v-if="pkg.price" class="package-price">
              {{ formatPrice(pkg.price) }}
            </span>
            <span v-else class="package-price text-muted-glass"
              >Narx kiritilmagan</span
            >

            <router-link
              :to="`/admin/statsionar/${pkg.id}/edit`"
              class="edit-link"
            >
              <i class="fas fa-pen-nib"></i> Tahrirlash
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onBeforeUnmount } from "vue";
import statsionarService from "../../../services/statsionarService";

const packages = ref([]);
const loading = ref(true);
const error = ref(null);
const searchQuery = ref("");

const backendUrl = import.meta.env.VITE_STORAGE_URL || "";
const slideIndex = reactive({});
const slideTimers = {};
const pausedIds = reactive({});

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

const filteredPackages = computed(() => {
  if (!searchQuery.value) return packages.value;
  const q = searchQuery.value.toLowerCase();
  return packages.value.filter((p) => p.name?.toLowerCase().includes(q));
});

const startSlideTimer = (pkg) => {
  const photos = getPhotos(pkg);
  if (photos.length <= 1) return;
  slideIndex[pkg.id] = slideIndex[pkg.id] || 0;
  slideTimers[pkg.id] = setInterval(() => {
    if (pausedIds[pkg.id]) return;
    slideIndex[pkg.id] = ((slideIndex[pkg.id] || 0) + 1) % photos.length;
  }, 2500);
};

const pauseSlide = (id) => {
  pausedIds[id] = true;
};
const resumeSlide = (id) => {
  pausedIds[id] = false;
};

const clearAllTimers = () => {
  Object.values(slideTimers).forEach((t) => clearInterval(t));
};

const fetchPackages = async () => {
  loading.value = true;
  error.value = null;
  clearAllTimers();
  try {
    const data = await statsionarService.fetchAll();
    packages.value = Array.isArray(data) ? data : data?.data || [];
    packages.value.forEach((pkg) => {
      slideIndex[pkg.id] = 0;
      startSlideTimer(pkg);
    });
  } catch (err) {
    packages.value = [];
    error.value = "Ma'lumotlarni yuklashda xatolik yuz berdi.";
  } finally {
    loading.value = false;
  }
};

onMounted(fetchPackages);
onBeforeUnmount(clearAllTimers);

const handleDelete = async (id) => {
  if (!confirm("Haqiqatan ham o'chirmoqchimisiz?")) return;
  try {
    await statsionarService.remove(id);
    fetchPackages();
  } catch (err) {
    alert(err.response?.data?.message || "O'chirishda xatolik yuz berdi.");
  }
};
</script>

<style scoped>
.fw-600 {
  font-weight: 600;
}
.fw-700 {
  font-weight: 700;
}
.text-muted-glass {
  color: #64748b;
}

.btn-primary-glass {
  background: #0284c7;
  color: white;
  padding: 12px 24px;
  border-radius: 14px;
  font-weight: 700;
  box-shadow: 0 4px 16px rgba(2, 132, 199, 0.25);
  white-space: nowrap;
}

.glass-panel {
  background: rgba(255, 255, 255, 0.45) !important;
  backdrop-filter: blur(20px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.7) !important;
  border-radius: 20px;
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.04);
}

.filter-card {
  padding: 16px 20px;
}

.search-input {
  position: relative;
  display: flex;
  align-items: center;
}
.search-icon {
  position: absolute;
  left: 16px;
  color: #475569;
}

.search-input input {
  width: 100%;
  padding: 12px 16px 12px 46px;
  border: 1px solid rgba(255, 255, 255, 0.6) !important;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.4) !important;
  outline: none;
  color: #0f172a;
  font-weight: 600;
}

.packages-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}

.package-card {
  overflow: hidden;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.package-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(2, 132, 199, 0.15) !important;
}

.package-slideshow {
  position: relative;
  width: 100%;
  height: 180px;
  overflow: hidden;
  background: #f1f5f9;
}

.slide-img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: 0;
  transition: opacity 0.6s ease;
}

.slide-img.active {
  opacity: 1;
}

.slide-dots {
  position: absolute;
  bottom: 10px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 6px;
  z-index: 2;
}

.dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.5);
  cursor: pointer;
  transition: all 0.2s ease;
}

.dot.active {
  background: #ffffff;
  transform: scale(1.3);
}

.delete-overlay-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  width: 34px;
  height: 34px;
  border-radius: 10px;
  border: none;
  background: rgba(15, 23, 42, 0.55);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: all 0.2s ease;
  z-index: 3;
}

.package-card:hover .delete-overlay-btn {
  opacity: 1;
}

.delete-overlay-btn:hover {
  background: #ef4444;
}

.package-body {
  padding: 16px 18px;
}

.package-title {
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 8px;
}

.package-note {
  color: #64748b;
  font-size: 0.85rem;
  margin-bottom: 10px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.package-items {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-bottom: 12px;
}

.item-chip {
  background: rgba(2, 132, 199, 0.1);
  color: #0369a1;
  padding: 4px 10px;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 600;
}

.item-chip.more {
  background: rgba(148, 163, 184, 0.2);
  color: #475569;
}

.package-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  padding-top: 10px;
  border-top: 1px solid rgba(0, 0, 0, 0.06);
}

.package-price {
  font-weight: 700;
  color: #0284c7;
  font-size: 0.95rem;
}

.edit-link {
  color: #475569;
  font-size: 0.82rem;
  font-weight: 600;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 4px;
}

.edit-link:hover {
  color: #0284c7;
}

.empty-state {
  text-align: center;
  color: #64748b;
}
.error-state {
  text-align: center;
}
</style>
