<template>
  <div class="dashboard-page">
    <!-- Stat Cards Row -->
    <div class="row g-4 mb-4">
      <div
        class="col-12 col-sm-6 col-lg-3"
        v-for="stat in statCards"
        :key="stat.title"
      >
        <div class="stat-card-glass" :style="{ '--stat-color': stat.color }">
          <div class="stat-icon-wrap">
            <i :class="stat.icon"></i>
          </div>
          <div class="stat-info">
            <div class="stat-label">{{ stat.title }}</div>
            <div class="stat-value">
              <span
                v-if="statsLoading"
                class="spinner-border spinner-border-sm"
              ></span>
              <span v-else>{{ stat.value }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <!-- Recent Contacts Glass Card -->
      <div class="col-12">
        <div class="content-card-glass h-100">
          <div class="card-header-glass">
            <div class="header-left">
              <div class="header-icon-glass text-primary">
                <i class="fas fa-inbox"></i>
              </div>
              <div>
                <h5 class="mb-0 fw-800 text-dark">So'nggi murojaatlar</h5>
                <p class="mb-0 text-secondary small">
                  Oxirgi kelib tushgan arizalar
                </p>
              </div>
            </div>
            <router-link to="/admin/contacts" class="btn-more-glass">
              Barchasi <i class="fas fa-chevron-right ms-2"></i>
            </router-link>
          </div>

          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table glass-table align-middle mb-0">
                <thead>
                  <tr>
                    <th class="ps-4">Foydalanuvchi</th>
                    <th>Aloqa</th>
                    <th>Mutaxassislik</th>
                    <th>Sana</th>
                    <th class="pe-4 text-end">Holat</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- State 1: Loading -->
                  <tr v-if="loadingContacts">
                    <td colspan="5" class="text-center py-5">
                      <div
                        class="spinner-border text-primary"
                        role="status"
                      ></div>
                      <div class="mt-2 text-muted-glass small">
                        Murojaatlar yuklanmoqda...
                      </div>
                    </td>
                  </tr>

                  <!-- State 2: Error -->
                  <tr v-else-if="contactsError">
                    <td colspan="5" class="text-center py-5">
                      <div class="error-state text-danger">
                        <i class="fas fa-exclamation-triangle mb-2 fs-3"></i>
                        <p class="m-0 fw-700">{{ contactsError }}</p>
                        <button
                          class="btn btn-sm btn-outline-danger mt-3"
                          @click="fetchDashboardData"
                        >
                          <i class="fas fa-redo me-1"></i> Qayta urinish
                        </button>
                      </div>
                    </td>
                  </tr>

                  <!-- State 3: Empty -->
                  <tr v-else-if="recentContacts.length === 0">
                    <td colspan="5" class="text-center py-5">
                      <div class="empty-state text-secondary">
                        <i class="fas fa-inbox mb-3 fs-1 opacity-50"></i>
                        <p class="m-0 fw-600">
                          Hozircha murojaatlar kiritilmagan
                        </p>
                      </div>
                    </td>
                  </tr>

                  <!-- State 4: Data list -->
                  <tr v-else v-for="c in recentContacts" :key="c.id">
                    <td class="ps-4">
                      <div class="d-flex align-items-center gap-3">
                        <div class="user-avatar-glass">
                          {{ c.name ? c.name.charAt(0) : "U" }}
                        </div>
                        <div class="fw-700 text-dark">{{ c.name }}</div>
                      </div>
                    </td>
                    <td>
                      <div class="text-secondary small fw-600">
                        {{ c.phone }}
                      </div>
                    </td>
                    <td>
                      <span class="badge-glass">{{
                        c.specialty || "Noma'lum"
                      }}</span>
                    </td>
                    <td>
                      <div class="text-secondary small fw-600">
                        {{ formatDate(c.created_at) }}
                      </div>
                    </td>
                    <td class="pe-4 text-end">
                      <span
                        :class="[
                          'status-pill-glass',
                          c.read ? 'status-read' : 'status-new',
                        ]"
                      >
                        {{ c.read ? "O'qildi" : "Yangi" }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import api from "../../services/api";

const statsLoading = ref(true);
const loadingContacts = ref(true);
const contactsError = ref(null);

const stats = ref({
  specializations: 0,
  doctors: 0,
  news: 0,
  services: 0,
  contacts: 0,
});

const recentContacts = ref([]);

const statCards = computed(() => [
  {
    title: "Mutaxassisliklar",
    value: stats.value.specializations,
    icon: "fas fa-stethoscope",
    color: "#0284c7",
  },
  {
    title: "Shifokorlar",
    value: stats.value.doctors,
    icon: "fas fa-user-md",
    color: "#10b981",
  },
  {
    title: "Yangiliklar",
    value: stats.value.news,
    icon: "fas fa-newspaper",
    color: "#8b5cf6",
  },
  {
    title: "Murojaatlar",
    value: stats.value.contacts,
    icon: "fas fa-envelope-open-text",
    color: "#f59e0b",
  },
]);

const formatDate = (d) => {
  if (!d) return "";
  return (
    new Date(d).toLocaleDateString("uz-Latn-UZ") +
    " " +
    new Date(d).toLocaleTimeString("uz-Latn-UZ", {
      hour: "2-digit",
      minute: "2-digit",
    })
  );
};

const fetchDashboardData = async () => {
  statsLoading.value = true;
  loadingContacts.value = true;
  contactsError.value = null;

  // 1. Fetch Real Specializations API
  try {
    const resSpec = await api.get("/admin/specializations");
    const specList = Array.isArray(resSpec.data?.data)
      ? resSpec.data.data
      : Array.isArray(resSpec.data)
      ? resSpec.data
      : [];
    stats.value.specializations = specList.length;
  } catch (err) {
    stats.value.specializations = 0;
  } finally {
    statsLoading.value = false;
  }

  // 2. Fetch Contacts API
  try {
    const resContacts = await api.get("/admin/contacts");
    const cList = Array.isArray(resContacts.data?.data)
      ? resContacts.data.data
      : Array.isArray(resContacts.data)
      ? resContacts.data
      : [];
    recentContacts.value = cList.slice(0, 5);
    stats.value.contacts = cList.length;
  } catch (err) {
    recentContacts.value = [];
    contactsError.value = "Murojaatlarni yuklashda xatolik yuz berdi.";
  } finally {
    loadingContacts.value = false;
  }
};

onMounted(fetchDashboardData);
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

.stat-card-glass {
  background: rgba(255, 255, 255, 0.45);
  backdrop-filter: blur(20px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.7);
  padding: 22px;
  border-radius: 22px;
  display: flex;
  align-items: center;
  gap: 18px;
  box-shadow: 0 8px 24px rgba(31, 38, 135, 0.04);
}

.stat-icon-wrap {
  width: 58px;
  height: 58px;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.7);
  border: 1px solid rgba(255, 255, 255, 0.9);
  color: var(--stat-color);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
}

.stat-label {
  font-size: 0.75rem;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  margin-bottom: 2px;
}

.stat-value {
  font-size: 1.7rem;
  font-weight: 800;
  color: #0f172a;
}

.content-card-glass {
  background: rgba(255, 255, 255, 0.45);
  backdrop-filter: blur(20px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.7);
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(31, 38, 135, 0.04);
}

.card-header-glass {
  padding: 22px 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(255, 255, 255, 0.6);
}

.header-left {
  display: flex;
  align-items: center;
  gap: 14px;
}

.header-icon-glass {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  background: rgba(2, 132, 199, 0.12);
  border: 1px solid rgba(2, 132, 199, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
}

.btn-more-glass {
  padding: 8px 16px;
  color: #0284c7;
  font-weight: 700;
  font-size: 0.88rem;
  text-decoration: none;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.8);
}

.glass-table {
  background: transparent !important;
}

.glass-table thead th {
  background: rgba(255, 255, 255, 0.3) !important;
  padding: 16px 24px;
  font-size: 0.75rem;
  font-weight: 700;
  color: #475569;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.6);
}

.glass-table tbody td {
  padding: 16px 24px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.4);
}

.user-avatar-glass {
  width: 38px;
  height: 38px;
  border-radius: 12px;
  background: rgba(2, 132, 199, 0.12);
  color: #0284c7;
  border: 1px solid rgba(2, 132, 199, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
}

.badge-glass {
  background: rgba(255, 255, 255, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.8);
  color: #334155;
  padding: 5px 12px;
  border-radius: 10px;
  font-size: 0.8rem;
  font-weight: 600;
}

.status-pill-glass {
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 700;
  display: inline-block;
}

.status-new {
  background: rgba(245, 158, 11, 0.12);
  color: #d97706;
}

.status-read {
  background: rgba(16, 185, 129, 0.12);
  color: #059669;
}
</style>
