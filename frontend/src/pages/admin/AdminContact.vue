<template>
  <div class="management-page">
    <!-- Header va Filter -->
    <div
      class="filter-card glass-panel mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3"
    >
      <div class="header-text">
        <h4 class="fw-800 mb-1 text-dark-slate">Murojaatlar ro'yxati</h4>
        <p class="text-muted-glass small mb-0">
          Kelib tushgan barcha murojaat va arizalar bilan tanishing
        </p>
      </div>

      <!-- Filter Tabs -->
      <div class="premium-filter-tabs glass-tabs">
        <button
          v-for="tab in ['all', 'unread', 'read']"
          :key="tab"
          :class="['tab-item', filterStatus === tab ? 'active' : '']"
          @click="filterStatus = tab"
        >
          {{
            tab === "all" ? "Barchasi" : tab === "unread" ? "Yangi" : "O'qilgan"
          }}
        </button>
      </div>
    </div>

    <!-- Data Table -->
    <div class="content-card glass-panel">
      <div class="table-responsive">
        <table class="table premium-table align-middle mb-0">
          <thead>
            <tr>
              <th class="ps-4">Murojaatchi</th>
              <th>Aloqa</th>
              <th>Yo'nalish</th>
              <th>Sana</th>
              <th>Holat</th>
              <th class="text-end pe-4">Ko'rish</th>
            </tr>
          </thead>
          <tbody>
            <!-- State 1: Loading -->
            <tr v-if="loading">
              <td colspan="6" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="mt-2 text-muted-glass small">Yuklanmoqda...</div>
              </td>
            </tr>

            <!-- State 2: Error -->
            <tr v-else-if="error">
              <td colspan="6" class="text-center py-5">
                <div class="error-state text-danger">
                  <i class="fas fa-exclamation-triangle mb-2 fs-3"></i>
                  <p class="m-0 fw-700">{{ error }}</p>
                  <button class="btn btn-sm btn-outline-danger mt-3" @click="fetchContacts">
                    <i class="fas fa-redo me-1"></i> Qayta urinish
                  </button>
                </div>
              </td>
            </tr>

            <!-- State 3: Empty -->
            <tr v-else-if="filteredContacts.length === 0">
              <td colspan="6" class="text-center py-5">
                <div class="empty-state">
                  <i class="fas fa-envelope-open mb-3 fs-1 opacity-50"></i>
                  <p class="m-0 fw-600">Hozircha murojaatlar kiritilmagan</p>
                </div>
              </td>
            </tr>

            <!-- State 4: Data list -->
            <tr
              v-else
              v-for="c in filteredContacts"
              :key="c.id"
              :class="['table-row-glass', { 'unread-row': !c.read }]"
            >
              <td class="ps-4">
                <div class="d-flex align-items-center gap-3">
                  <div
                    class="contact-avatar shadow-sm"
                    :class="!c.read ? 'avatar-active' : 'avatar-idle'"
                  >
                    {{ c.name ? c.name.charAt(0) : 'M' }}
                  </div>
                  <div class="fw-700 text-dark-slate">{{ c.name }}</div>
                </div>
              </td>
              <td>
                <div class="text-muted-glass small fw-600">
                  <i class="fas fa-phone-alt me-2 text-primary"></i>
                  {{ c.phone }}
                </div>
              </td>
              <td>
                <span class="badge-soft-glass">{{
                  c.specialty || "Noma'lum"
                }}</span>
              </td>
              <td>
                <div class="text-muted-glass small fw-600">
                  {{ formatDate(c.created_at) }}
                </div>
              </td>
              <td>
                <span
                  :class="[
                    'status-pill',
                    c.read ? 'status-read' : 'status-new',
                  ]"
                >
                  {{ c.read ? "O'qildi" : "Yangi" }}
                </span>
              </td>
              <td class="text-end pe-4">
                <button
                  class="action-btn view"
                  @click="viewContact(c)"
                  title="Xabarni ko'rish"
                >
                  <i class="fas fa-eye"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div
        v-if="!loading && !error && contacts.length"
        class="d-flex justify-content-between align-items-center px-4 py-3 small text-muted-glass"
      >
        <span>Ko'rsatilmoqda: {{ contacts.length }} / {{ total }}</span>
        <button
          v-if="currentPage < lastPage"
          class="btn btn-sm btn-outline-primary rounded-pill px-3"
          :disabled="loadingMore"
          @click="loadMore"
        >
          <span v-if="loadingMore" class="spinner-border spinner-border-sm me-1"></span>
          Ko'proq yuklash
        </button>
      </div>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal-glass animate-scaleUp">
          <div class="modal-header-glass">
            <h5 class="fw-800 m-0 text-dark-slate">Murojaat tafsilotlari</h5>
            <button class="close-btn" @click="closeModal">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div class="modal-body" v-if="selectedContact">
            <div class="detail-grid mb-4">
              <div class="detail-item">
                <label>Foydalanuvchi</label>
                <div class="val fw-700 text-dark-slate">
                  {{ selectedContact.name }}
                </div>
              </div>
              <div class="detail-item text-end">
                <label>Sana va vaqt</label>
                <div class="val text-muted-glass">
                  {{ formatDate(selectedContact.created_at) }}
                </div>
              </div>
              <div class="detail-item">
                <label>Telefon raqami</label>
                <div class="val text-primary fw-700">
                  {{ selectedContact.phone }}
                </div>
              </div>
              <div class="detail-item text-end">
                <label>Mutaxassislik</label>
                <div class="val">
                  <span class="badge-soft-glass">{{
                    selectedContact.specialty || "-"
                  }}</span>
                </div>
              </div>
            </div>

            <div class="message-section">
              <label>Xabar matni:</label>
              <div class="message-bubble glass-panel">
                {{ selectedContact.message || "Xabar qoldirilmagan." }}
              </div>
            </div>

            <div class="modal-footer-glass justify-content-between">
              <button
                class="btn-delete-contact"
                @click="handleDelete(selectedContact.id)"
              >
                <i class="fas fa-trash-alt me-2"></i> O'chirish
              </button>
              <button class="btn-save-glass" @click="closeModal">Yopish</button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import api from "../../services/api";

const contacts = ref([]);
const loading = ref(true);
const error = ref(null);
const filterStatus = ref("all");
const showModal = ref(false);
const selectedContact = ref(null);

const filteredContacts = computed(() => {
  if (filterStatus.value === "all") return contacts.value;
  return contacts.value.filter((c) =>
    filterStatus.value === "read" ? c.read : !c.read
  );
});

const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);
const loadingMore = ref(false);

const fetchPage = async (page) => {
  const res = await api.get("/admin/contacts", { params: { page, per_page: 50 } });
  const payload = res.data?.data || {};
  currentPage.value = payload.current_page || 1;
  lastPage.value = payload.last_page || 1;
  total.value = payload.total ?? 0;
  return Array.isArray(payload.data) ? payload.data : [];
};

const fetchContacts = async () => {
  loading.value = true;
  error.value = null;
  try {
    contacts.value = await fetchPage(1);
  } catch (err) {
    contacts.value = [];
    error.value = "Murojaatlarni yuklashda xatolik yuz berdi.";
  } finally {
    loading.value = false;
  }
};

const loadMore = async () => {
  loadingMore.value = true;
  try {
    const more = await fetchPage(currentPage.value + 1);
    const ids = new Set(contacts.value.map((c) => c.id));
    contacts.value.push(...more.filter((c) => !ids.has(c.id)));
  } catch (err) {
    alert("Keyingi murojaatlarni yuklashda xatolik yuz berdi.");
  } finally {
    loadingMore.value = false;
  }
};

onMounted(fetchContacts);

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

const viewContact = async (c) => {
  selectedContact.value = { ...c };
  showModal.value = true;
  if (!c.read) {
    c.read = true;
    try {
      await api.put(`/admin/contacts/${c.id}`, { read: true });
    } catch {
      c.read = false;
    }
  }
};

const closeModal = () => {
  showModal.value = false;
  selectedContact.value = null;
};

const handleDelete = async (id) => {
  if (!confirm("Ushbu murojaatni o'chirmoqchimisiz?")) return;
  try {
    await api.delete(`/admin/contacts/${id}`);
    showModal.value = false;
    fetchContacts();
  } catch (err) {
    alert(err.response?.data?.message || "O'chirishda xatolik yuz berdi.");
    showModal.value = false;
  }
};
</script>

<style scoped>
.fw-600 { font-weight: 600; }
.fw-700 { font-weight: 700; }
.fw-800 { font-weight: 800; }
.text-dark-slate { color: #0f172a; }
.text-muted-glass { color: #64748b; }

.glass-panel {
  background: rgba(255, 255, 255, 0.2) !important;
  backdrop-filter: blur(16px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.4) !important;
  border-radius: 20px;
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.04);
}

.filter-card { padding: 16px 24px; }

.glass-tabs {
  background: rgba(255, 255, 255, 0.25);
  padding: 4px;
  border-radius: 12px;
  display: flex;
  gap: 4px;
  border: 1px solid rgba(255, 255, 255, 0.4);
}

.tab-item {
  border: none;
  background: transparent;
  padding: 8px 18px;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 700;
  color: #64748b;
}

.tab-item.active {
  background: #0284c7;
  color: white;
}

.content-card { overflow: hidden; }

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

.table-row-glass { border-bottom: 1px solid rgba(255, 255, 255, 0.2); }
.unread-row { background: rgba(2, 132, 199, 0.05) !important; }

.contact-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
}

.avatar-active { background: rgba(2, 132, 199, 0.2); color: #0284c7; }
.avatar-idle { background: rgba(255, 255, 255, 0.4); color: #64748b; }

.badge-soft-glass {
  background: rgba(255, 255, 255, 0.3);
  color: #334155;
  border: 1px solid rgba(255, 255, 255, 0.4);
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 600;
}

.status-pill {
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 700;
}

.status-new { background: rgba(217, 119, 6, 0.15); color: #b45309; }
.status-read { background: rgba(16, 185, 129, 0.15); color: #047857; }

.action-btn {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: none;
}

.action-btn.view { background: rgba(2, 132, 199, 0.15); color: #0284c7; }

.empty-state { text-align: center; color: #64748b; }
.error-state { text-align: center; }
</style>
