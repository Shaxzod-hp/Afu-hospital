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
          placeholder="Operatsiya nomi bo'yicha qidirish..."
        />
      </div>
      <router-link
        to="/admin/surgeries/create"
        class="btn-primary-glass text-decoration-none"
      >
        <i class="fas fa-plus me-2"></i> Yangi operatsiya
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
          @click="fetchSurgeries"
        >
          <i class="fas fa-redo me-1"></i> Qayta urinish
        </button>
      </div>
    </div>

    <!-- State: Empty -->
    <div
      v-else-if="filteredSurgeries.length === 0"
      class="glass-panel text-center py-5"
    >
      <div class="empty-state">
        <i class="fas fa-briefcase-medical mb-3 fs-1 opacity-50"></i>
        <p class="m-0 fw-600">Hozircha operatsiyalar kiritilmagan</p>
      </div>
    </div>

    <!-- State: Card grid -->
    <div v-else class="packages-grid">
      <div
        v-for="surgery in filteredSurgeries"
        :key="surgery.id"
        class="package-card glass-panel"
      >
        <!-- Photo -->
        <div class="package-slideshow">
          <img
            :src="getPhotoUrl(surgery.photo, surgery.name)"
            :alt="surgery.name"
            class="slide-img active"
          />
          <button
            class="delete-overlay-btn"
            @click.stop="handleDelete(surgery.id)"
            title="O'chirish"
          >
            <i class="fas fa-trash-alt"></i>
          </button>
        </div>

        <!-- Body -->
        <div class="package-body">
          <h6 class="package-title">{{ surgery.name }}</h6>

          <p v-if="surgery.short_description" class="package-note">
            {{ surgery.short_description }}
          </p>

          <div
            v-if="surgery.included_items && surgery.included_items.length"
            class="package-items"
          >
            <span
              v-for="(item, i) in surgery.included_items.slice(0, 3)"
              :key="i"
              class="item-chip"
            >
              {{ item.name }}
            </span>
            <span
              v-if="surgery.included_items.length > 3"
              class="item-chip more"
            >
              +{{ surgery.included_items.length - 3 }}
            </span>
          </div>

          <div class="package-footer">
            <span v-if="priceRange(surgery)" class="package-price">
              {{ priceRange(surgery) }}
            </span>
            <span v-else class="package-price text-muted-glass">
              Narx kiritilmagan
            </span>

            <router-link
              :to="`/admin/surgeries/${surgery.id}/edit`"
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
import { ref, computed, onMounted } from "vue";
import surgeryService from "../../../services/surgeryService";

const surgeries = ref([]);
const loading = ref(true);
const error = ref(null);
const searchQuery = ref("");

const backendUrl = import.meta.env.VITE_STORAGE_URL || "";

const getPhotoUrl = (photo, name) => {
  if (!photo) {
    return (
      "https://ui-avatars.com/api/?name=" +
      encodeURIComponent(name || "Surgery") +
      "&background=0284c7&color=fff&size=256"
    );
  }
  return photo.startsWith("http") ? photo : backendUrl + photo;
};

const priceRange = (surgery) => {
  const items = surgery.included_items || [];
  if (items.length === 0) return null;
  const prices = items.map((i) => Number(i.price)).filter((p) => !isNaN(p));
  if (prices.length === 0) return null;
  const min = Math.min(...prices);
  const max = Math.max(...prices);
  const fmt = (n) => n.toLocaleString("uz-Latn-UZ");
  return min === max ? `${fmt(min)} so'm` : `${fmt(min)} — ${fmt(max)} so'm`;
};

const filteredSurgeries = computed(() => {
  if (!searchQuery.value) return surgeries.value;
  const q = searchQuery.value.toLowerCase();
  return surgeries.value.filter((s) => s.name?.toLowerCase().includes(q));
});

const fetchSurgeries = async () => {
  loading.value = true;
  error.value = null;
  try {
    const data = await surgeryService.fetchAll();
    const list =
      data?.data?.data || data?.data || (Array.isArray(data) ? data : []);
    surgeries.value = list.filter((s) => s && s.id);
  } catch (err) {
    surgeries.value = [];
    error.value = "Operatsiyalar ma'lumotlarini yuklashda xatolik yuz berdi.";
  } finally {
    loading.value = false;
  }
};

onMounted(fetchSurgeries);

const handleDelete = async (id) => {
  if (!confirm("Haqiqatan ham o'chirmoqchimisiz?")) return;
  try {
    await surgeryService.remove(id);
    fetchSurgeries();
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
