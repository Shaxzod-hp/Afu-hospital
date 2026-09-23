<template>
  <div class="management-page">
    <!-- Top Filter Bar -->
    <div
      class="filter-card glass-panel mb-4 d-flex align-items-center justify-content-between gap-3"
    >
      <div class="search-input flex-grow-1">
        <i class="fas fa-search search-icon"></i>
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Sarlavha bo'yicha qidirish..."
        />
      </div>

      <!-- Alohida sahifaga o'tuvchi tugma -->
      <router-link
        to="/admin/news/create"
        class="btn-primary-glass text-decoration-none d-inline-flex align-items-center"
      >
        <i class="fas fa-plus me-2"></i> Yangi qo'shish
      </router-link>
    </div>

    <!-- State 1: Loading -->
    <div v-if="loading" class="text-center py-5 glass-panel">
      <div class="spinner-border text-primary" role="status"></div>
      <div class="mt-2 text-muted-glass small fw-600">
        Yangiliklar yuklanmoqda...
      </div>
    </div>

    <!-- State 2: Error -->
    <div v-else-if="error" class="text-center py-5 glass-panel">
      <div class="error-state text-danger">
        <i class="fas fa-exclamation-triangle mb-2 fs-3"></i>
        <p class="m-0 fw-700">{{ error }}</p>
        <button
          class="btn btn-sm btn-outline-danger mt-3 rounded-3"
          @click="fetchNews"
        >
          <i class="fas fa-redo me-1"></i> Qayta urinish
        </button>
      </div>
    </div>

    <!-- State 3: Empty -->
    <div
      v-else-if="filteredNews.length === 0"
      class="text-center py-5 glass-panel"
    >
      <div class="empty-state">
        <i class="fas fa-newspaper mb-3 fs-1 opacity-50"></i>
        <p class="m-0 fw-600">Hozircha yangiliklar kiritilmagan</p>
      </div>
    </div>

    <!-- State 4: Cards Grid (Services style) -->
    <div v-else class="row g-4">
      <div
        v-for="item in filteredNews"
        :key="item.id"
        class="col-12 col-md-6 col-xl-4"
      >
        <div
          class="news-admin-card glass-panel h-100 d-flex flex-column overflow-hidden position-relative"
        >
          <!-- Image Container -->
          <div class="card-img-wrap position-relative">
            <img
              :src="getImageUrl(item.image)"
              :alt="item.title"
              class="card-img"
            />
            <div class="card-badge-pos">
              <span
                :class="['badge-soft', getCategoryClass(item.category?.name)]"
              >
                {{ item.category?.name || "Yangilik" }}
              </span>
            </div>
          </div>

          <!-- Body Container -->
          <div class="p-4 d-flex flex-column flex-grow-1">
            <div class="news-date small fw-600 mb-2">
              <i class="far fa-calendar-alt me-1 text-primary"></i>
              {{
                formatDate(item.published_at || item.created_at || item.date)
              }}
            </div>

            <h5 class="fw-800 news-title text-clamp-2 mb-2">
              {{ item.title }}
            </h5>

            <p class="text-muted-glass small text-clamp-3 mb-4 flex-grow-1">
              {{
                item.summary ||
                item.body ||
                item.content ||
                "Tavsif mavjud emas"
              }}
            </p>

            <!-- Card Actions -->
            <div
              class="d-flex align-items-center justify-content-between pt-3 border-top border-light-subtle mt-auto"
            >
              <router-link
                :to="`/admin/news/${item.id}/edit`"
                class="action-btn edit-btn d-flex align-items-center gap-2 text-decoration-none"
                title="Tahrirlash"
              >
                <i class="fas fa-pen-nib"></i>
                <span class="small fw-700">Tahrirlash</span>
              </router-link>

              <button
                class="action-btn delete-btn d-flex align-items-center gap-2"
                @click="handleDelete(item.id)"
                title="O'chirish"
              >
                <i class="fas fa-trash-alt"></i>
                <span class="small fw-700">O'chirish</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import api from "../../../services/api";

const news = ref([]);
const loading = ref(true);
const error = ref(null);
const searchQuery = ref("");
const backendUrl = import.meta.env.VITE_STORAGE_URL || "";

const fetchNews = async () => {
  loading.value = true;
  error.value = null;
  try {
    const res = await api.get("/admin/news");
    const data = res.data?.data || res.data || [];
    news.value = Array.isArray(data) ? data : [];
  } catch (err) {
    news.value = [];
    error.value = "Yangiliklar ma'lumotlarini yuklashda xatolik yuz berdi.";
  } finally {
    loading.value = false;
  }
};

onMounted(fetchNews);

const filteredNews = computed(() => {
  if (!searchQuery.value) return news.value;
  const q = searchQuery.value.toLowerCase();
  return news.value.filter((n) => n.title?.toLowerCase().includes(q));
});

const getImageUrl = (path) => {
  if (!path)
    return "https://ui-avatars.com/api/?name=News&background=0284c7&color=fff";
  if (path.startsWith("http://") || path.startsWith("https://")) return path;
  return `${backendUrl}${path.startsWith("/") ? "" : "/"}${path}`;
};

const formatDate = (dateStr) => {
  if (!dateStr) return "Bugun";
  return dateStr.split("T")[0];
};

const getCategoryClass = (cat) => {
  switch (cat) {
    case "Aksiya":
      return "cat-promo";
    case "Tadbir":
      return "cat-event";
    default:
      return "cat-news";
  }
};

const handleDelete = async (id) => {
  if (!confirm("Haqiqatan ham ushbu yangilikni o'chirmoqchimisiz?")) return;
  try {
    await api.delete(`/admin/news/${id}`);
    fetchNews();
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
.fw-800 {
  font-weight: 800;
}
.text-muted-glass {
  color: #64748b;
}

/* Filter & Header Bar */
.btn-primary-glass {
  background: #0284c7;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 14px;
  font-weight: 700;
  transition: all 0.25s ease;
  box-shadow: 0 4px 16px rgba(2, 132, 199, 0.25);
  white-space: nowrap;
}

.btn-primary-glass:hover {
  background: #0369a1;
  color: white;
  transform: translateY(-2px);
}

.glass-panel {
  background: rgba(255, 255, 255, 0.45) !important;
  backdrop-filter: blur(16px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.6) !important;
  border-radius: 20px;
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
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
  color: #64748b;
}

.search-input input {
  width: 100%;
  padding: 12px 16px 12px 46px;
  border: 1px solid rgba(255, 255, 255, 0.6) !important;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.35) !important;
  outline: none;
  color: #0f172a;
  font-weight: 600;
}

/* Card Styles */
.news-admin-card {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.news-admin-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 32px rgba(0, 0, 0, 0.08) !important;
}

.card-img-wrap {
  height: 200px;
  width: 100%;
  overflow: hidden;
  background: #cbd5e1;
}

.card-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.news-admin-card:hover .card-img {
  transform: scale(1.05);
}

.card-badge-pos {
  position: absolute;
  top: 14px;
  left: 14px;
}

.news-title {
  color: #0f172a;
  font-size: 1.15rem;
  line-height: 1.4;
}

.news-date {
  color: #64748b;
}

/* Badges */
.badge-soft {
  padding: 6px 14px;
  border-radius: 10px;
  font-size: 0.8rem;
  font-weight: 700;
  backdrop-filter: blur(8px);
}

.cat-news {
  background: rgba(2, 132, 199, 0.85);
  color: #ffffff;
}
.cat-promo {
  background: rgba(245, 158, 11, 0.85);
  color: #ffffff;
}
.cat-event {
  background: rgba(16, 185, 129, 0.85);
  color: #ffffff;
}

/* Actions */
.action-btn {
  padding: 8px 16px;
  border-radius: 10px;
  border: none;
  transition: all 0.2s ease;
}

.edit-btn {
  background: rgba(2, 132, 199, 0.12);
  color: #0284c7;
}

.edit-btn:hover {
  background: #0284c7;
  color: white;
}

.delete-btn {
  background: rgba(239, 68, 68, 0.12);
  color: #ef4444;
}

.delete-btn:hover {
  background: #ef4444;
  color: white;
}

/* Text clamp */
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

.empty-state,
.error-state {
  color: #64748b;
}
</style>
