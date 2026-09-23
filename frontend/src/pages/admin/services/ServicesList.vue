<template>
  <div class="management-page">
    <!-- Filter Bar -->
    <div
      class="filter-card glass-panel mb-4 d-flex align-items-center justify-content-between gap-3"
    >
      <div class="search-input flex-grow-1">
        <i class="fas fa-search search-icon"></i>
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Xizmat nomi yoki tavsifi bo'yicha qidirish..."
        />
      </div>
      <router-link
        to="/admin/services/create"
        class="btn-primary-glass text-decoration-none"
      >
        <i class="fas fa-plus me-2"></i> Yangi xizmat
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
          @click="fetchServices"
        >
          <i class="fas fa-redo me-1"></i> Qayta urinish
        </button>
      </div>
    </div>

    <!-- State: Empty -->
    <div
      v-else-if="filteredServices.length === 0"
      class="glass-panel text-center py-5"
    >
      <div class="empty-state">
        <i class="fas fa-concierge-bell mb-3 fs-1 opacity-50"></i>
        <p class="m-0 fw-600">Hozircha xizmatlar kiritilmagan</p>
      </div>
    </div>

    <!-- State: Card grid -->
    <div v-else class="services-grid">
      <router-link
        v-for="service in filteredServices"
        :key="service.id"
        :to="`/admin/services/${service.id}/edit`"
        class="service-card glass-panel text-decoration-none"
      >
        <div class="service-card-img-wrapper">
          <img
            :src="getPhotoUrl(service.photo, service.name)"
            :alt="service.name"
          />
          <button
            class="delete-overlay-btn"
            @click.prevent.stop="handleDelete(service.id)"
            title="O'chirish"
          >
            <i class="fas fa-trash-alt"></i>
          </button>
        </div>

        <div class="service-card-body">
          <h6 class="service-card-title">{{ service.name }}</h6>
          <p class="service-card-desc">
            {{ service.short_description || "Tavsif kiritilmagan" }}
          </p>

          <div class="service-card-footer">
            <span class="badge-soft-primary">
              <i class="fas fa-list-ul me-1"></i>
              {{ (service.included_items || []).length }} band
            </span>
            <span v-if="priceRange(service)" class="badge-soft-success">
              {{ priceRange(service) }}
            </span>
          </div>
        </div>
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import servicesService from "../../../services/servicesService";

const services = ref([]);
const loading = ref(true);
const error = ref(null);
const searchQuery = ref("");

const backendUrl = import.meta.env.VITE_API_URL || "";

const getPhotoUrl = (photo, name) => {
  if (!photo) {
    return (
      "https://ui-avatars.com/api/?name=" +
      encodeURIComponent(name || "Service") +
      "&background=0284c7&color=fff&size=256"
    );
  }
  return photo.startsWith("http") ? photo : backendUrl + photo;
};

const priceRange = (service) => {
  const items = service.included_items || [];
  if (items.length === 0) return null;
  const prices = items.map((i) => Number(i.price)).filter((p) => !isNaN(p));
  if (prices.length === 0) return null;
  const min = Math.min(...prices);
  const max = Math.max(...prices);
  const fmt = (n) => n.toLocaleString("uz-Latn-UZ");
  return min === max ? `${fmt(min)} so'm` : `${fmt(min)} — ${fmt(max)} so'm`;
};

const filteredServices = computed(() => {
  if (!searchQuery.value) return services.value;
  const q = searchQuery.value.toLowerCase();
  return services.value.filter(
    (s) =>
      s.name?.toLowerCase().includes(q) ||
      s.short_description?.toLowerCase().includes(q)
  );
});

const fetchServices = async () => {
  loading.value = true;
  error.value = null;
  try {
    const data = await servicesService.fetchAll();
    services.value = Array.isArray(data) ? data : data?.data || [];
  } catch (err) {
    services.value = [];
    error.value = "Xizmatlar ma'lumotlarini yuklashda xatolik yuz berdi.";
  } finally {
    loading.value = false;
  }
};

onMounted(fetchServices);

const handleDelete = async (id) => {
  if (!confirm("Haqiqatan ham o'chirmoqchimisiz?")) return;
  try {
    await servicesService.remove(id);
    fetchServices();
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
  transition: all 0.25s ease;
  box-shadow: 0 4px 16px rgba(2, 132, 199, 0.25);
  white-space: nowrap;
}

.glass-panel {
  background: rgba(255, 255, 255, 0.2) !important;
  backdrop-filter: blur(16px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.4) !important;
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
  border: 1px solid rgba(255, 255, 255, 0.4) !important;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.25) !important;
  outline: none;
  color: #0f172a;
  font-weight: 600;
}

/* Card grid */
.services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 20px;
}

.service-card {
  display: flex;
  flex-direction: column;
  overflow: hidden;
  color: inherit;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.service-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(2, 132, 199, 0.15) !important;
}

.service-card-img-wrapper {
  position: relative;
  width: 100%;
  height: 160px;
  overflow: hidden;
}

.service-card-img-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
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
}

.service-card:hover .delete-overlay-btn {
  opacity: 1;
}

.delete-overlay-btn:hover {
  background: #ef4444;
}

.service-card-body {
  padding: 16px 18px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.service-card-title {
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 6px;
}

.service-card-desc {
  color: #64748b;
  font-size: 0.85rem;
  margin-bottom: 14px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  flex: 1;
}

.service-card-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  flex-wrap: wrap;
}

.badge-soft-primary {
  background: rgba(2, 132, 199, 0.15);
  color: #0369a1;
  padding: 5px 12px;
  border-radius: 10px;
  font-size: 0.78rem;
  font-weight: 700;
}

.badge-soft-success {
  background: rgba(16, 185, 129, 0.15);
  color: #047857;
  padding: 5px 12px;
  border-radius: 10px;
  font-size: 0.78rem;
  font-weight: 700;
}

.empty-state {
  text-align: center;
  color: #64748b;
}
.error-state {
  text-align: center;
}
</style>
