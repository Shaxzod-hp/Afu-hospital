<template>
  <div class="management-page">
    <!-- Filter & Action Bar -->
    <div
      class="filter-card glass-panel mb-4 d-flex align-items-center justify-content-between gap-3"
    >
      <div class="search-input flex-grow-1">
        <i class="fas fa-search search-icon"></i>
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Mutaxassislik nomi yoki tavsifi bo'yicha qidirish..."
        />
      </div>
      <button class="btn-primary-glass" @click="openModal()">
        <i class="fas fa-plus me-2"></i> Yangi mutaxassislik
      </button>
    </div>

    <!-- Global Alert / Error Notification -->
    <div
      v-if="alertMessage"
      :class="[
        'alert-glass',
        alertType === 'error' ? 'alert-danger-glass' : 'alert-success-glass',
        'mb-4',
      ]"
    >
      <i
        :class="
          alertType === 'error'
            ? 'fas fa-exclamation-triangle me-2'
            : 'fas fa-check-circle me-2'
        "
      ></i>
      <span>{{ alertMessage }}</span>
      <button class="btn-close-glass" @click="alertMessage = ''">
        &times;
      </button>
    </div>

    <!-- Data Table -->
    <div class="content-card glass-panel">
      <div class="table-responsive">
        <table class="table premium-table align-middle mb-0">
          <thead>
            <tr>
              <th class="ps-4">Nomi</th>
              <th>Tavsif</th>
              <th>Yaratilgan sana</th>
              <th class="text-end pe-4">Harakatlar</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="4" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="mt-2 text-muted-glass small">Yuklanmoqda...</div>
              </td>
            </tr>
            <tr v-else-if="loadError">
              <td colspan="4" class="text-center py-5">
                <div class="empty-state">
                  <i class="fas fa-exclamation-triangle mb-3 text-danger"></i>
                  <p>Ma'lumotlarni yuklashda xatolik yuz berdi.</p>
                  <button
                    class="btn-secondary-glass mt-2"
                    @click="fetchSpecializations"
                  >
                    Qayta urinish
                  </button>
                </div>
              </td>
            </tr>
            <tr v-else-if="filteredSpecializations.length === 0">
              <td colspan="4" class="text-center py-5">
                <div class="empty-state">
                  <i class="fas fa-stethoscope mb-3"></i>
                  <p>Hozircha mutaxassisliklar mavjud emas</p>
                </div>
              </td>
            </tr>
            <tr
              v-else
              v-for="item in filteredSpecializations"
              :key="item?.id || item"
              class="table-row-glass"
            >
              <td class="ps-4">
                <div class="fw-700 text-dark-slate">
                  {{ item?.name || "Nomsiz" }}
                </div>
              </td>
              <td>
                <div
                  class="text-muted-glass small text-truncate"
                  style="max-width: 320px"
                >
                  {{ item?.description || "Tavsif berilmagan" }}
                </div>
              </td>
              <td>
                <div class="text-muted-glass small fw-600">
                  {{ formatDate(item?.created_at) }}
                </div>
              </td>
              <td class="text-end pe-4">
                <div class="d-flex justify-content-end gap-2">
                  <button
                    class="action-btn edit"
                    @click="openModal(item)"
                    title="Tahrirlash"
                  >
                    <i class="fas fa-pen-nib"></i>
                  </button>
                  <button
                    class="action-btn delete"
                    @click="confirmDelete(item)"
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

    <!-- Create / Edit Modal -->
    <div v-if="showModal" class="modal-backdrop-glass" @click.self="closeModal">
      <div class="modal-card-glass">
        <div class="modal-header-glass">
          <h5 class="modal-title">
            <i
              :class="
                editingId ? 'fas fa-edit me-2' : 'fas fa-plus-circle me-2'
              "
            ></i>
            {{
              editingId
                ? "Mutaxassislikni tahrirlash"
                : "Yangi mutaxassislik qo'shish"
            }}
          </h5>
          <button class="btn-close-glass" @click="closeModal">&times;</button>
        </div>

        <form @submit.prevent="saveSpecialization" class="modal-body-glass">
          <div v-if="formError" class="alert-glass alert-danger-glass mb-3">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ formError }}
          </div>

          <div class="mb-3">
            <label class="form-label-glass"
              >Nomi <span class="text-danger">*</span></label
            >
            <input
              type="text"
              v-model="form.name"
              class="form-control-glass"
              placeholder="Masalan: Kardiologiya"
              required
            />
          </div>

          <div class="mb-3">
            <label class="form-label-glass">Tavsifi</label>
            <textarea
              v-model="form.description"
              class="form-control-glass"
              rows="3"
              placeholder="Mutaxassislik haqida qisqacha ma'lumot..."
            ></textarea>
          </div>

          <div class="modal-footer-glass">
            <button
              type="button"
              class="btn-secondary-glass"
              @click="closeModal"
            >
              Bekor qilish
            </button>
            <button type="submit" class="btn-primary-glass" :disabled="saving">
              <span
                v-if="saving"
                class="spinner-border spinner-border-sm me-2"
              ></span>
              {{ editingId ? "Saqlash" : "Qo'shish" }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="modal-backdrop-glass"
      @click.self="closeDeleteModal"
    >
      <div class="modal-card-glass modal-small">
        <div class="modal-header-glass">
          <h5 class="modal-title text-danger">
            <i class="fas fa-exclamation-triangle me-2"></i> O'chirishni
            tasdiqlash
          </h5>
          <button class="btn-close-glass" @click="closeDeleteModal">
            &times;
          </button>
        </div>
        <div class="modal-body-glass">
          <p>
            Haqiqatan ham
            <strong>{{ itemToDelete?.name || "ushbu" }}</strong>
            mutaxassisligini o'chirmoqchimisiz?
          </p>
          <p class="text-muted-glass small">
            Ushbu amalni ortga qaytarib bo'lmaydi.
          </p>
        </div>
        <div class="modal-footer-glass">
          <button class="btn-secondary-glass" @click="closeDeleteModal">
            Bekor qilish
          </button>
          <button
            class="btn-danger-glass"
            @click="executeDelete"
            :disabled="deleting"
          >
            <span
              v-if="deleting"
              class="spinner-border spinner-border-sm me-2"
            ></span>
            O'chirish
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import specializationsService from "../../../services/specializationsService";

const specializations = ref([]);
const loading = ref(true);
const loadError = ref(false);
const saving = ref(false);
const deleting = ref(false);
const searchQuery = ref("");
const alertMessage = ref("");
const alertType = ref("success");

const showModal = ref(false);
const showDeleteModal = ref(false);
const editingId = ref(null);
const itemToDelete = ref(null);
const formError = ref("");

const form = ref({
  name: "",
  description: "",
});

const filteredSpecializations = computed(() => {
  if (!Array.isArray(specializations.value)) return [];
  if (!searchQuery.value) return specializations.value;

  const q = searchQuery.value.toLowerCase().trim();
  return specializations.value.filter((item) => {
    if (!item) return false;
    const name = item.name ? String(item.name).toLowerCase() : "";
    const desc = item.description ? String(item.description).toLowerCase() : "";
    return name.includes(q) || desc.includes(q);
  });
});

const fetchSpecializations = async () => {
  loading.value = true;
  loadError.value = false;
  try {
    const data = await specializationsService.fetchAll();
    if (Array.isArray(data)) {
      specializations.value = data;
    } else if (Array.isArray(data?.data)) {
      specializations.value = data.data;
    } else {
      specializations.value = [];
    }
  } catch (err) {
    loadError.value = true;
  } finally {
    loading.value = false;
  }
};

onMounted(fetchSpecializations);

const formatDate = (dateStr) => {
  if (!dateStr) return "Bugun";
  try {
    return new Date(dateStr).toLocaleDateString("uz-Latn-UZ");
  } catch (e) {
    return dateStr;
  }
};

const openModal = (item = null) => {
  formError.value = "";
  if (item && item.id) {
    editingId.value = item.id;
    form.value = {
      name: item.name || "",
      description: item.description || "",
    };
  } else {
    editingId.value = null;
    form.value = {
      name: "",
      description: "",
    };
  }
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  editingId.value = null;
  formError.value = "";
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  itemToDelete.value = null;
};

const saveSpecialization = async () => {
  if (!form.value.name || form.value.name.trim().length < 2) {
    formError.value = "Nomi kamida 2 ta belgidan iborat bo'lishi shart!";
    return;
  }

  saving.value = true;
  formError.value = "";

  try {
    const payload = {
      name: form.value.name.trim(),
      description: form.value.description
        ? form.value.description.trim()
        : null,
    };

    if (editingId.value) {
      await specializationsService.update(editingId.value, payload);
      showAlert("Mutaxassislik muvaffaqiyatli yangilandi.", "success");
    } else {
      await specializationsService.create(payload);
      showAlert("Yangi mutaxassislik muvaffaqiyatli qo'shildi.", "success");
    }

    closeModal();
    fetchSpecializations();
  } catch (err) {
    if (err.response?.data?.message) {
      formError.value = err.response.data.message;
    } else if (err.response?.data?.errors?.name) {
      formError.value = err.response.data.errors.name[0];
    } else {
      formError.value = "Saqlashda xatolik yuz berdi. Qayta urinib ko'ring.";
    }
  } finally {
    saving.value = false;
  }
};

const confirmDelete = (item) => {
  if (!item) return;
  itemToDelete.value = item;
  showDeleteModal.value = true;
};

const executeDelete = async () => {
  if (!itemToDelete.value?.id) return;
  deleting.value = true;

  try {
    await specializationsService.remove(itemToDelete.value.id);
    showAlert("Mutaxassislik muvaffaqiyatli o'chirildi.", "success");
    closeDeleteModal();
    fetchSpecializations();
  } catch (err) {
    closeDeleteModal();
    const msg = err.response?.data?.message || "O'chirishda xatolik yuz berdi.";
    showAlert(msg, "error");
  } finally {
    deleting.value = false;
  }
};

const showAlert = (msg, type = "success") => {
  alertMessage.value = msg;
  alertType.value = type;
  setTimeout(() => {
    if (alertMessage.value === msg) {
      alertMessage.value = "";
    }
  }, 5000);
};
</script>

<style scoped>
.fw-600 {
  font-weight: 600;
}

.fw-700 {
  font-weight: 700;
}

.text-dark-slate {
  color: #0f172a;
}

.text-muted-glass {
  color: #64748b;
}

.glass-panel {
  background: rgba(255, 255, 255, 0.45) !important;
  backdrop-filter: blur(20px) saturate(180%);
  -webkit-backdrop-filter: blur(20px) saturate(180%);
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
  transition: all 0.2s ease;
}

.search-input input:focus {
  border-color: #0284c7 !important;
  background: rgba(255, 255, 255, 0.7) !important;
}

.btn-primary-glass {
  background: #0284c7;
  color: white;
  padding: 12px 24px;
  border-radius: 14px;
  font-weight: 700;
  border: none;
  cursor: pointer;
  transition: all 0.25s ease;
  box-shadow: 0 4px 16px rgba(2, 132, 199, 0.25);
  white-space: nowrap;
}

.btn-primary-glass:hover:not(:disabled) {
  background: #0369a1;
  transform: translateY(-2px);
}

.btn-secondary-glass {
  background: rgba(148, 163, 184, 0.2);
  color: #475569;
  padding: 10px 20px;
  border-radius: 12px;
  font-weight: 600;
  border: 1px solid rgba(255, 255, 255, 0.6);
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-secondary-glass:hover {
  background: rgba(148, 163, 184, 0.35);
  color: #0f172a;
}

.btn-danger-glass {
  background: #e31e24;
  color: white;
  padding: 10px 20px;
  border-radius: 12px;
  font-weight: 700;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-danger-glass:hover:not(:disabled) {
  background: #c1191e;
  transform: translateY(-1px);
}

.content-card {
  overflow: hidden;
}

.premium-table {
  background: transparent !important;
  --bs-table-bg: transparent !important;
}

.premium-table thead th {
  background: rgba(255, 255, 255, 0.25) !important;
  padding: 18px 24px;
  font-size: 0.8rem;
  font-weight: 700;
  color: #334155;
  text-transform: uppercase;
  border-bottom: 1px solid rgba(255, 255, 255, 0.4);
}

.table-row-glass {
  border-bottom: 1px solid rgba(255, 255, 255, 0.3);
  transition: background 0.2s ease;
}

.table-row-glass:hover {
  background: rgba(255, 255, 255, 0.35) !important;
}

.action-btn {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.action-btn.edit {
  background: rgba(2, 132, 199, 0.15);
  color: #0284c7;
}

.action-btn.edit:hover {
  background: #0284c7;
  color: white;
}

.action-btn.delete {
  background: rgba(227, 30, 36, 0.15);
  color: #e31e24;
}

.action-btn.delete:hover {
  background: #e31e24;
  color: white;
}

/* Modals */
.modal-backdrop-glass {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  backdrop-filter: blur(8px);
  z-index: 2000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
}

.modal-card-glass {
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(24px) saturate(200%);
  border: 1px solid rgba(255, 255, 255, 0.9);
  border-radius: 24px;
  width: 100%;
  max-width: 540px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
  overflow: hidden;
}

.modal-card-glass.modal-small {
  max-width: 440px;
}

.modal-header-glass {
  padding: 20px 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}

.modal-title {
  margin: 0;
  font-size: 1.15rem;
  font-weight: 800;
  color: #0f172a;
}

.btn-close-glass {
  background: transparent;
  border: none;
  font-size: 1.5rem;
  color: #64748b;
  cursor: pointer;
  line-height: 1;
}

.modal-body-glass {
  padding: 24px;
}

.modal-footer-glass {
  padding-top: 16px;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 12px;
}

.form-label-glass {
  font-weight: 700;
  font-size: 0.88rem;
  color: #334155;
  margin-bottom: 6px;
  display: block;
}

.form-control-glass {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.8);
  color: #0f172a;
  font-weight: 600;
  outline: none;
  transition: all 0.2s ease;
}

.form-control-glass:focus {
  border-color: #0284c7;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}

/* Alerts */
.alert-glass {
  padding: 14px 20px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-weight: 600;
  font-size: 0.9rem;
}

.alert-success-glass {
  background: rgba(16, 185, 129, 0.15);
  color: #047857;
  border: 1px solid rgba(16, 185, 129, 0.3);
}

.alert-danger-glass {
  background: rgba(227, 30, 36, 0.15);
  color: #c1191e;
  border: 1px solid rgba(227, 30, 36, 0.3);
}
</style>
