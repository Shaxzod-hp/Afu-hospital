<template>
  <div class="doctor-detail-page">
    <!-- Header / Navigation Bar -->
    <div
      class="glass-panel p-3 mb-4 d-flex align-items-center justify-content-between"
    >
      <div class="d-flex align-items-center gap-3">
        <button class="btn-back-glass" @click="$router.back()">
          <i class="fas fa-arrow-left"></i>
        </button>
        <h4 class="mb-0 fw-700 text-dark-slate">Shifokor Ma'lumotlari</h4>
      </div>
      <div class="d-flex gap-2" v-if="doctor">
        <router-link
          :to="`/admin/doctors/${doctor.id}/edit`"
          class="btn-warning-glass text-decoration-none"
        >
          <i class="fas fa-pen-nib me-2"></i> Tahrirlash
        </router-link>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="glass-panel p-5 text-center">
      <div class="spinner-border text-primary" role="status"></div>
      <div class="mt-2 text-muted-glass">Ma'lumotlar yuklanmoqda...</div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="glass-panel p-5 text-center">
      <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
      <h5 class="fw-700 text-dark-slate">{{ error }}</h5>
      <button
        class="btn btn-sm btn-outline-danger mt-3"
        @click="fetchDoctorDetail"
      >
        <i class="fas fa-redo me-1"></i> Qayta urinish
      </button>
    </div>

    <!-- Main Content -->
    <div v-else-if="doctor" class="row g-4">
      <!-- Left Column: Main Profile Card -->
      <div class="col-lg-4">
        <div class="glass-panel p-4 text-center h-100">
          <div class="avatar-large-wrapper mx-auto mb-3 shadow">
            <img
              :src="getPhotoUrl(doctor.photo, doctor.full_name)"
              :alt="doctor.full_name"
            />
          </div>
          <h5 class="fw-800 text-dark-slate mb-1">{{ doctor.full_name }}</h5>
          <span class="badge-soft-primary mb-3 d-inline-block">
            {{ doctor.specialization?.name || "Yo'nalish kiritilmagan" }}
          </span>

          <div class="border-top-glass pt-3 mt-2 text-start">
            <div class="info-item mb-2" v-if="doctor.position">
              <i class="fas fa-id-badge text-primary me-2"></i>
              <span><strong>Lavozimi:</strong> {{ doctor.position }}</span>
            </div>
            <div class="info-item mb-2">
              <i class="fas fa-briefcase text-primary me-2"></i>
              <span
                ><strong>Tajriba:</strong>
                {{ doctor.experience_years || 0 }} yil</span
              >
            </div>
            <div class="info-item mb-2" v-if="doctor.phone">
              <i class="fas fa-phone text-primary me-2"></i>
              <span><strong>Telefon:</strong> {{ doctor.phone }}</span>
            </div>
            <div class="info-item mb-2" v-if="doctor.working_hours">
              <i class="fas fa-clock text-primary me-2"></i>
              <span
                ><strong>Ish vaqti:</strong> {{ doctor.working_hours }}</span
              >
            </div>
            <div
              class="info-item d-flex gap-3 mt-2"
              v-if="doctor.instagram || doctor.telegram || doctor.whatsapp"
            >
              <a
                v-if="doctor.instagram"
                :href="doctor.instagram"
                target="_blank"
                class="social-icon"
                ><i class="fab fa-instagram"></i
              ></a>
              <a
                v-if="doctor.telegram"
                :href="doctor.telegram"
                target="_blank"
                class="social-icon"
                ><i class="fab fa-telegram"></i
              ></a>
              <a
                v-if="doctor.whatsapp"
                :href="doctor.whatsapp"
                target="_blank"
                class="social-icon"
                ><i class="fab fa-whatsapp"></i
              ></a>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Extended Details -->
      <div class="col-lg-8">
        <div class="glass-panel p-4 h-100">
          <h6 class="fw-700 text-dark-slate mb-3 border-bottom-glass pb-2">
            <i class="fas fa-user-md me-2 text-primary"></i> Shifokor haqida
          </h6>
          <p class="bio-text mb-4">
            {{
              doctor.bio ||
              "Ushbu shifokor haqida qisqacha ma'lumot kiritilmagan."
            }}
          </p>

          <h6 class="fw-700 text-dark-slate mb-3 border-bottom-glass pb-2">
            <i class="fas fa-graduation-cap me-2 text-primary"></i> Ta'lim va
            tajriba
          </h6>
          <div class="row g-3 mb-4">
            <div class="col-sm-6">
              <div class="meta-card p-3 rounded">
                <div class="text-muted-glass small">Ta'lim</div>
                <div class="fw-700 text-dark-slate">
                  {{ doctor.education || "Kiritilmagan" }}
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="meta-card p-3 rounded">
                <div class="text-muted-glass small">Oldingi ish joyi</div>
                <div class="fw-700 text-dark-slate">
                  {{ doctor.previous_workplace || "Kiritilmagan" }}
                </div>
              </div>
            </div>
          </div>

          <h6 class="fw-700 text-dark-slate mb-3 border-bottom-glass pb-2">
            <i class="fas fa-info-circle me-2 text-primary"></i> Tizim
            Ma'lumotlari
          </h6>
          <div class="row g-3">
            <div class="col-sm-6">
              <div class="meta-card p-3 rounded">
                <div class="text-muted-glass small">Qo'shilgan sana</div>
                <div class="fw-700 text-dark-slate">
                  {{ formatDate(doctor.created_at) }}
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="meta-card p-3 rounded">
                <div class="text-muted-glass small">Status</div>
                <div>
                  <span class="badge-soft-success">Faol</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import doctorsService from "../../../services/doctorsService";

const route = useRoute();
const doctor = ref(null);
const loading = ref(true);
const error = ref("");

const backendUrl = import.meta.env.VITE_API_URL || "";

const getPhotoUrl = (photo, name) => {
  if (!photo) {
    return (
      "https://ui-avatars.com/api/?name=" +
      encodeURIComponent(name || "Doctor") +
      "&background=0284c7&color=fff"
    );
  }
  return photo.startsWith("http") ? photo : backendUrl + photo;
};

const formatDate = (dateStr) => {
  if (!dateStr) return "Noma'lum";
  return new Date(dateStr).toLocaleDateString("uz-Latn-UZ");
};

const fetchDoctorDetail = async () => {
  loading.value = true;
  error.value = "";
  try {
    const data = await doctorsService.fetchOne(route.params.id);
    doctor.value = data?.data || data;
  } catch (err) {
    doctor.value = null;
    error.value = "Shifokor ma'lumotlarini yuklashda xatolik yuz berdi.";
  } finally {
    loading.value = false;
  }
};

onMounted(fetchDoctorDetail);
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

.text-dark-slate {
  color: #0f172a;
}
.text-muted-glass {
  color: #64748b;
}
.bio-text {
  color: #334155;
  line-height: 1.6;
}

.glass-panel {
  background: rgba(255, 255, 255, 0.25) !important;
  backdrop-filter: blur(16px) saturate(180%);
  -webkit-backdrop-filter: blur(16px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.4) !important;
  border-radius: 20px;
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.04);
}

.border-top-glass {
  border-top: 1px solid rgba(255, 255, 255, 0.4);
}
.border-bottom-glass {
  border-bottom: 1px solid rgba(255, 255, 255, 0.4);
}

.avatar-large-wrapper {
  width: 120px;
  height: 120px;
  border-radius: 24px;
  overflow: hidden;
  border: 3px solid rgba(255, 255, 255, 0.9);
}

.avatar-large-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.badge-soft-primary {
  background: rgba(2, 132, 199, 0.15);
  color: #0369a1;
  padding: 6px 16px;
  border-radius: 12px;
  font-weight: 700;
}

.badge-soft-success {
  background: rgba(16, 185, 129, 0.15);
  color: #059669;
  padding: 4px 12px;
  border-radius: 8px;
  font-weight: 700;
  font-size: 0.85rem;
}

.meta-card {
  background: rgba(255, 255, 255, 0.3);
  border: 1px solid rgba(255, 255, 255, 0.4);
}

.social-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(2, 132, 199, 0.1);
  color: #0284c7;
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  transition: all 0.2s;
}
.social-icon:hover {
  background: #0284c7;
  color: white;
}

.btn-back-glass {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.5);
  background: rgba(255, 255, 255, 0.3);
  color: #0f172a;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.btn-back-glass:hover {
  background: rgba(255, 255, 255, 0.6);
  transform: translateX(-2px);
}

.btn-warning-glass {
  background: #f59e0b;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 12px;
  font-weight: 700;
  transition: all 0.2s;
  box-shadow: 0 4px 16px rgba(245, 158, 11, 0.25);
  display: inline-flex;
  align-items: center;
}

.btn-warning-glass:hover {
  background: #d97706;
  color: white;
  transform: translateY(-2px);
}
</style>
