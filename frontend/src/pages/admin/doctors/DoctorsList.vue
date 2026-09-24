<template>
  <div class="management-page">
    <div class="filter-card glass-panel mb-4">
      <div
        class="d-flex align-items-center justify-content-between gap-3 flex-wrap"
      >
        <div class="search-input flex-grow-1">
          <i class="fas fa-search search-icon"></i>
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Shifokor ismi bo'yicha qidirish..."
          />
        </div>

        <select
          v-model="selectedSpecialization"
          class="filter-select"
          @change="fetchDoctors"
        >
          <option value="">Barcha yo'nalishlar</option>
          <option
            v-for="spec in specializations"
            :key="spec.id"
            :value="spec.id"
          >
            {{ spec.name }}
          </option>
        </select>

        <router-link
          to="/admin/specializations"
          class="btn-secondary-glass text-decoration-none"
        >
          <i class="fas fa-stethoscope me-2"></i> Yo'nalish qo'shish
        </router-link>
        <router-link
          to="/admin/doctors/create"
          class="btn-primary-glass text-decoration-none"
        >
          <i class="fas fa-plus me-2"></i> Yangi shifokor
        </router-link>
      </div>
    </div>

    <div class="content-card glass-panel">
      <div class="table-responsive">
        <table class="table premium-table align-middle mb-0">
          <thead>
            <tr>
              <th class="ps-4">Shifokor</th>
              <th>Lavozimi</th>
              <th>Yo'nalish</th>
              <th>Tajriba</th>
              <th>Telefon</th>
              <th class="text-end pe-4">Harakatlar</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="6" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="mt-2 text-muted-glass small">Yuklanmoqda...</div>
              </td>
            </tr>

            <tr v-else-if="error">
              <td colspan="6" class="text-center py-5">
                <div class="error-state text-danger">
                  <i class="fas fa-exclamation-triangle mb-2 fs-3"></i>
                  <p class="m-0 fw-700">{{ error }}</p>
                  <button
                    class="btn btn-sm btn-outline-danger mt-3"
                    @click="fetchDoctors"
                  >
                    <i class="fas fa-redo me-1"></i> Qayta urinish
                  </button>
                </div>
              </td>
            </tr>

            <tr v-else-if="filteredDoctors.length === 0">
              <td colspan="6" class="text-center py-5">
                <div class="empty-state">
                  <i class="fas fa-user-slash mb-3 fs-1 opacity-50"></i>
                  <p class="m-0 fw-600">Hozircha shifokorlar kiritilmagan</p>
                </div>
              </td>
            </tr>

            <tr
              v-else
              v-for="doc in filteredDoctors"
              :key="doc.id"
              class="table-row-glass"
            >
              <td class="ps-4">
                <div class="d-flex align-items-center gap-3">
                  <div class="avatar-wrapper shadow-sm">
                    <img
                      :src="getPhotoUrl(doc.photo, doc.full_name)"
                      alt="doc"
                    />
                  </div>
                  <div class="fw-700 doctor-name">{{ doc.full_name }}</div>
                </div>
              </td>
              <td>
                <div class="text-truncate bio-text" style="max-width: 160px">
                  {{ doc.position || "Kiritilmagan" }}
                </div>
              </td>
              <td>
                <span class="badge-soft-primary">{{
                  doc.specialization?.name || "—"
                }}</span>
              </td>
              <td>
                <div class="small fw-600 exp-text">
                  {{ doc.experience_years || 0 }} yil
                </div>
              </td>
              <td>
                <div class="small text-muted-glass">{{ doc.phone || "—" }}</div>
              </td>
              <td class="text-end pe-4">
                <div class="d-flex justify-content-end gap-2">
                  <router-link
                    :to="`/admin/doctors/${doc.id}/treatment-logs/create`"
                    class="action-btn treatment"
                    title="Bugungi jarayon"
                  >
                    <i class="fas fa-notes-medical"></i>
                  </router-link>
                  <router-link
                    :to="`/admin/doctors/${doc.id}`"
                    class="action-btn view"
                    title="Batafsil ko'rish"
                  >
                    <i class="fas fa-eye"></i>
                  </router-link>
                  <router-link
                    :to="`/admin/doctors/${doc.id}/edit`"
                    class="action-btn edit"
                    title="Tahrirlash"
                  >
                    <i class="fas fa-pen-nib"></i>
                  </router-link>
                  <button
                    class="action-btn delete"
                    @click="handleDelete(doc.id)"
                    title="O'chirish"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import doctorsService from "../../../services/doctorsService";
import specializationsService from "../../../services/specializationsService";

const doctors = ref([]);
const specializations = ref([]);
const loading = ref(true);
const error = ref(null);
const searchQuery = ref("");
const selectedSpecialization = ref("");
const backendUrl = import.meta.env.VITE_STORAGE_URL || "";

const getPhotoUrl = (photo, name) => {
  if (!photo) {
    return (
      "https://ui-avatars.com/api/?name=" +
      name +
      "&background=0284c7&color=fff"
    );
  }
  return photo.startsWith("http") ? photo : backendUrl + photo;
};
const filteredDoctors = computed(() => {
  if (!searchQuery.value) return doctors.value;
  const q = searchQuery.value.toLowerCase();
  return doctors.value.filter((d) => d.full_name?.toLowerCase().includes(q));
});

const fetchDoctors = async () => {
  loading.value = true;
  error.value = null;
  try {
    const data = await doctorsService.fetchAll(
      selectedSpecialization.value || null
    );
    const list =
      data?.data?.data || data?.data || (Array.isArray(data) ? data : []);
    doctors.value = list.filter((d) => d && d.id);
  } catch (err) {
    doctors.value = [];
    error.value = "Shifokorlar ma'lumotlarini yuklashda xatolik yuz berdi.";
  } finally {
    loading.value = false;
  }
};

const fetchSpecializations = async () => {
  try {
    const data = await specializationsService.fetchAll();
    specializations.value = Array.isArray(data) ? data : data?.data || [];
  } catch (err) {
    specializations.value = [];
  }
};

onMounted(() => {
  fetchDoctors();
  fetchSpecializations();
});

const handleDelete = async (id) => {
  if (!confirm("Haqiqatan ham o'chirmoqchimisiz?")) return;
  try {
    await doctorsService.remove(id);
    fetchDoctors();
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
.bio-text {
  color: #475569;
  font-size: 0.9rem;
}

.filter-select {
  padding: 12px 16px;
  border-radius: 14px;
  border: 1px solid rgba(255, 255, 255, 0.4);
  background: rgba(255, 255, 255, 0.25);
  font-weight: 600;
  color: #0f172a;
  outline: none;
}

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
  display: inline-flex;
  align-items: center;
}

.btn-primary-glass:hover {
  background: #0369a1;
  transform: translateY(-2px);
  color: white;
}

.btn-primary-glass:disabled {
  opacity: 0.6;
  transform: none;
  cursor: not-allowed;
}

.btn-secondary-glass {
  background: rgba(148, 163, 184, 0.2);
  color: #475569;
  padding: 10px 20px;
  border-radius: 12px;
  font-weight: 600;
  border: 1px solid rgba(255, 255, 255, 0.4);
  display: inline-flex;
  align-items: center;
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

.content-card {
  overflow: hidden;
}

.premium-table {
  background: transparent !important;
  --bs-table-bg: transparent !important;
}

.premium-table thead th {
  background: rgba(255, 255, 255, 0.15) !important;
  padding: 18px 24px;
  font-size: 0.8rem;
  font-weight: 700;
  color: #334155;
  text-transform: uppercase;
  border-bottom: 1px solid rgba(255, 255, 255, 0.3);
}

.table-row-glass {
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.avatar-wrapper {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  overflow: hidden;
  border: 2px solid rgba(255, 255, 255, 0.8);
}

.avatar-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.badge-soft-primary {
  background: rgba(2, 132, 199, 0.15);
  color: #0369a1;
  padding: 6px 14px;
  border-radius: 10px;
  font-size: 0.85rem;
  font-weight: 700;
}

.action-btn {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  text-decoration: none;
}

.action-btn.view {
  background: rgba(16, 185, 129, 0.15);
  color: #10b981;
}
.action-btn.edit {
  background: rgba(2, 132, 199, 0.15);
  color: #0284c7;
}
.action-btn.delete {
  background: rgba(227, 30, 36, 0.15);
  color: #e31e24;
}
.action-btn.treatment {
  background: rgba(168, 85, 247, 0.15);
  color: #a855f7;
}

.empty-state {
  text-align: center;
  color: #64748b;
}
.error-state {
  text-align: center;
}
</style>
