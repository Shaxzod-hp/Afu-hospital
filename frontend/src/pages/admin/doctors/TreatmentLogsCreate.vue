<template>
  <div class="log-create-page">
    <div class="page-header mb-4">
      <router-link :to="`/admin/doctors`" class="back-link">
        <i class="fas fa-arrow-left"></i>
        <span>Shifokorlar ro'yxati</span>
      </router-link>

      <h4 class="fw-800 mt-3 mb-0">
        Bugungi jarayon
        <span v-if="doctor" class="text-primary-glass"
          >— {{ doctor.full_name }}</span
        >
      </h4>
      <p class="text-muted-glass small mb-0" v-if="doctor">
        {{ doctor.specialization?.name || doctor.position || "" }}
      </p>
    </div>

    <div v-if="loadingDoctor" class="text-center py-5">
      <div class="spinner-border text-primary" role="status"></div>
    </div>

    <div v-else-if="!doctor" class="content-card glass-panel text-center py-5">
      <i class="fas fa-user-slash mb-3 fs-1 opacity-50"></i>
      <p class="m-0 fw-600">Shifokor topilmadi.</p>
    </div>

    <div v-else class="row g-4">
      <!-- CHAP: YANGI LAVHA QO'SHISH FORMASI -->
      <div class="col-lg-7">
        <div class="content-card glass-panel form-box">
          <div v-if="modalError" class="alert-glass alert-danger-glass mb-3">
            {{ modalError }}
          </div>
          <div v-if="successMsg" class="alert-glass alert-success-glass mb-3">
            {{ successMsg }}
          </div>

          <!-- RASM YUKLASH: Drag & Drop zonasi -->
          <div class="mb-4">
            <label class="form-label-glass">
              Rasmlar (kamida 2 ta) <span class="text-danger">*</span>
            </label>

            <input
              ref="fileInputRef"
              type="file"
              accept="image/jpeg,image/png,image/webp"
              multiple
              class="d-none"
              @change="handlePhotosChange"
            />

            <div
              class="upload-dropzone"
              :class="{ 'is-dragover': isDragOver }"
              @click="fileInputRef?.click()"
              @dragover.prevent="isDragOver = true"
              @dragleave.prevent="isDragOver = false"
              @drop.prevent="handleDrop"
            >
              <i class="fas fa-cloud-upload-alt upload-icon"></i>
              <p class="upload-text mb-1">
                Rasmlarni shu yerga tashlang yoki
                <span class="upload-link">bosing</span>
              </p>
              <span class="upload-hint"
                >JPEG, PNG yoki WEBP &mdash; kamida {{ MIN_PHOTOS }} ta, ko'pi
                bilan {{ MAX_PHOTOS }} ta</span
              >
            </div>

            <div v-if="photoPreviews.length" class="upload-preview-grid mt-3">
              <div
                v-for="(src, i) in photoPreviews"
                :key="i"
                class="upload-preview-item"
              >
                <img :src="src" />
                <button
                  type="button"
                  class="preview-remove-btn"
                  @click.stop="removePhoto(i)"
                  title="O'chirish"
                >
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>

            <div
              v-if="photoPreviews.length"
              class="upload-counter mt-2"
              :class="photoCountOk ? 'counter-ok' : 'counter-warn'"
            >
              <i
                class="fas"
                :class="photoCountOk ? 'fa-check-circle' : 'fa-exclamation-circle'"
              ></i>
              {{ photoPreviews.length }} ta rasm tanlandi
              <template v-if="photoPreviews.length < MIN_PHOTOS">
                &mdash; yana {{ MIN_PHOTOS - photoPreviews.length }} ta
                kerak</template
              >
              <template v-else-if="photoPreviews.length > MAX_PHOTOS">
                &mdash; {{ photoPreviews.length - MAX_PHOTOS }} tasini
                olib tashlang</template
              >
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label-glass">
              Tavsif <span class="text-danger">*</span>
            </label>
            <textarea
              v-model="description"
              class="form-control-glass"
              rows="4"
              :maxlength="MAX_DESCRIPTION"
              placeholder="Bugun qanday muolaja qilingani haqida yozing..."
            ></textarea>
            <div class="desc-counter">
              {{ description.length }} / {{ MAX_DESCRIPTION }}
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2">
            <router-link to="/admin/doctors" class="btn-secondary-glass">
              Bekor qilish
            </router-link>
            <button
              class="btn-primary-glass"
              :disabled="submitting"
              @click="submitTreatmentLog"
            >
              <span
                v-if="submitting"
                class="spinner-border spinner-border-sm me-2"
              ></span>
              {{ submitting ? submitStage : "Saqlash" }}
            </button>
          </div>
        </div>
      </div>

      <!-- O'NG: OLDINGI LAVHALAR -->
      <div class="col-lg-5">
        <div class="content-card glass-panel logs-box">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-800 m-0">Oldingi lavhalar</h6>
            <span v-if="existingLogs.length" class="badge-soft-primary">
              {{ existingLogs.length }} ta
            </span>
          </div>

          <div v-if="loadingLogs" class="text-center py-4">
            <div
              class="spinner-border spinner-border-sm text-primary"
              role="status"
            ></div>
          </div>

          <div v-else-if="existingLogs.length === 0" class="empty-logs-state">
            <i class="fas fa-notes-medical mb-2 fs-3 opacity-50"></i>
            <p class="m-0 fw-600">Hali jarayon lavhalari mavjud emas</p>
          </div>

          <div v-else class="logs-list">
            <div
              v-for="log in existingLogs"
              :key="log.id"
              class="log-item"
              :class="{ 'is-expired': isExpired(log) }"
            >
              <!-- O'chirish tugmasi -->
              <button
                type="button"
                class="log-delete-btn"
                :disabled="deletingId === log.id"
                @click="deleteLog(log.id)"
                title="O'chirish"
              >
                <span
                  v-if="deletingId === log.id"
                  class="spinner-border spinner-border-sm"
                ></span>
                <i v-else class="fas fa-trash-alt"></i>
              </button>

              <div class="log-item-photos">
                <img
                  v-for="(photo, i) in log.photos.slice(0, log.photos.length > 3 ? 2 : 3)"
                  :key="i"
                  :src="resolveImage(photo)"
                  :alt="log.description"
                  loading="lazy"
                />
                <div v-if="log.photos.length > 3" class="log-item-more">
                  +{{ log.photos.length - 2 }}
                </div>
              </div>
              <p class="log-item-desc">{{ log.description }}</p>
              <span class="log-item-date">{{
                formatLogDate(log.created_at)
              }}</span>
              <span v-if="isExpired(log)" class="log-expired-badge">
                Saytda ko'rinmaydi (24 soat o'tgan)
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { useRoute } from "vue-router";
import doctorsService from "../../../services/doctorsService";

// Backend (TreatmentLog modeli) dagi chegaralar bilan bir xil
const MIN_PHOTOS = 2;
const MAX_PHOTOS = 10;
const MAX_DESCRIPTION = 2000;
const LIFETIME_MS = 24 * 60 * 60 * 1000;

const route = useRoute();

const doctor = ref(null);
const loadingDoctor = ref(true);

const selectedPhotos = ref([]);
const photoPreviews = ref([]);
const description = ref("");
const submitting = ref(false);
const submitStage = ref("");
const modalError = ref("");
const successMsg = ref("");

const fileInputRef = ref(null);
const isDragOver = ref(false);

const backendUrl = import.meta.env.VITE_STORAGE_URL || "";

const existingLogs = ref([]);
const loadingLogs = ref(true);
const deletingId = ref(null);

const photoCountOk = computed(
  () =>
    photoPreviews.value.length >= MIN_PHOTOS &&
    photoPreviews.value.length <= MAX_PHOTOS
);

const resolveImage = (path) => {
  if (!path) return "";
  return path.startsWith("http") ? path : backendUrl + path;
};

const formatLogDate = (dateStr) => {
  if (!dateStr) return "";
  const d = new Date(dateStr);
  return d.toLocaleString("uz-Latn-UZ", {
    day: "2-digit",
    month: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
  });
};

const isExpired = (log) =>
  Date.now() - new Date(log.created_at).getTime() > LIFETIME_MS;

// Laravel xatosidan foydalanuvchiga tushunarli matn chiqaramiz
const extractError = (err, fallback) => {
  const res = err?.response;
  if (!res) return "Server bilan aloqa yo'q. Internetni tekshirib, qayta urinib ko'ring.";
  if (res.status === 413) return "Rasmlar hajmi juda katta. Kamroq yoki kichikroq rasm tanlang.";
  if (res.status === 422 && res.data?.errors) {
    const first = Object.values(res.data.errors).flat()[0];
    if (first) return first;
  }
  if (res.status >= 500) return "Serverda xatolik yuz berdi. Birozdan so'ng qayta urinib ko'ring.";
  return res.data?.message || fallback;
};

const loadDoctor = async () => {
  loadingDoctor.value = true;
  doctor.value = null;
  try {
    const res = await doctorsService.adminFetchOne(route.params.id);
    // API javobi { success, data: {...} } bo'lib kelishi mumkin
    doctor.value = res && res.data && !res.full_name ? res.data : res;
  } catch (e) {
    console.error(
      "Shifokorni yuklashda xatolik:",
      e?.response?.status,
      e?.response?.data || e
    );
    doctor.value = null;
  } finally {
    loadingDoctor.value = false;
  }
};

const loadExistingLogs = async () => {
  if (!doctor.value) {
    existingLogs.value = [];
    loadingLogs.value = false;
    return;
  }
  loadingLogs.value = true;
  try {
    const list = await doctorsService.adminFetchLogs(doctor.value.id);

    existingLogs.value = (Array.isArray(list) ? list : [])
      .map((log) => {
        let photos = log.photos;
        if (typeof photos === "string") {
          try {
            photos = JSON.parse(photos);
          } catch {
            photos = [];
          }
        }
        return { ...log, photos: Array.isArray(photos) ? photos : [] };
      })
      .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
  } catch (e) {
    console.error(
      "Lavhalarni yuklashda xatolik:",
      e?.response?.status,
      e?.response?.data || e
    );
    existingLogs.value = [];
  } finally {
    loadingLogs.value = false;
  }
};

const appendPhotos = (files) => {
  const imageFiles = files.filter((f) => f.type.startsWith("image/"));
  if (imageFiles.length < files.length) {
    modalError.value = "Faqat rasm fayllarini (JPEG, PNG, WEBP) yuklash mumkin.";
  }
  selectedPhotos.value = [...selectedPhotos.value, ...imageFiles];
  photoPreviews.value = [
    ...photoPreviews.value,
    ...imageFiles.map((f) => URL.createObjectURL(f)),
  ];
};

const handlePhotosChange = (e) => {
  const files = Array.from(e.target.files || []);
  appendPhotos(files);
  e.target.value = ""; // bir xil faylni qayta tanlash imkoni uchun
};

const handleDrop = (e) => {
  isDragOver.value = false;
  const files = Array.from(e.dataTransfer.files || []);
  appendPhotos(files);
};

const removePhoto = (index) => {
  URL.revokeObjectURL(photoPreviews.value[index]);
  selectedPhotos.value.splice(index, 1);
  photoPreviews.value.splice(index, 1);
};

// Lavhani o'chirish funksiyasi
const deleteLog = async (id) => {
  if (deletingId.value) return;
  if (!confirm("Haqiqatan ham ushbu lavhani o'chirmoqchimisiz?")) return;

  deletingId.value = id;
  try {
    await doctorsService.removeLog(id);
    // Ro'yxatdan o'chirilganini darhol yangilash
    existingLogs.value = existingLogs.value.filter((log) => log.id !== id);
  } catch (e) {
    console.error("Lavhani o'chirishda xatolik:", e?.response?.data || e);
    alert(extractError(e, "O'chirishda xatolik yuz berdi."));
  } finally {
    deletingId.value = null;
  }
};

const resetForm = () => {
  photoPreviews.value.forEach((src) => URL.revokeObjectURL(src));
  selectedPhotos.value = [];
  photoPreviews.value = [];
  description.value = "";
};

const submitTreatmentLog = async () => {
  modalError.value = "";
  successMsg.value = "";

  const text = description.value.trim();

  if (selectedPhotos.value.length < MIN_PHOTOS) {
    modalError.value = `Kamida ${MIN_PHOTOS} ta rasm yuklash shart!`;
    return;
  }
  if (selectedPhotos.value.length > MAX_PHOTOS) {
    modalError.value = `Ko'pi bilan ${MAX_PHOTOS} ta rasm yuklash mumkin!`;
    return;
  }
  if (text.length < 5) {
    modalError.value = "Tavsif kamida 5 ta belgidan iborat bo'lishi shart!";
    return;
  }

  submitting.value = true;
  try {
    // Rasmlar api.js interceptorida avtomatik siqiladi
    submitStage.value = "Yuklanmoqda...";
    const formData = new FormData();
    formData.append("doctor_id", doctor.value.id);
    formData.append("description", text);
    selectedPhotos.value.forEach((file) => {
      formData.append("photos[]", file);
    });

    await doctorsService.createLog(formData);

    successMsg.value = "Muvaffaqiyatli saqlandi.";
    resetForm();
    await loadExistingLogs(); // yangi lavha o'ng tomonda darhol chiqishi uchun
  } catch (err) {
    modalError.value = extractError(err, "Saqlashda xatolik yuz berdi.");
  } finally {
    submitting.value = false;
    submitStage.value = "";
  }
};

onMounted(async () => {
  await loadDoctor();
  await loadExistingLogs();
});

onBeforeUnmount(() => {
  photoPreviews.value.forEach((src) => URL.revokeObjectURL(src));
});
</script>

<style scoped>
.fw-600 {
  font-weight: 600;
}
.fw-800 {
  font-weight: 800;
}
.text-muted-glass {
  color: #64748b;
}
.text-primary-glass {
  color: #0284c7;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #475569;
  font-weight: 600;
  text-decoration: none;
  font-size: 0.9rem;
}
.back-link:hover {
  color: #0284c7;
}

.glass-panel {
  background: rgba(255, 255, 255, 0.2) !important;
  backdrop-filter: blur(16px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.4) !important;
  border-radius: 20px;
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.04);
}

.form-box,
.logs-box {
  padding: 28px;
  height: 100%;
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
}

.alert-glass {
  padding: 12px 16px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.88rem;
}
.alert-danger-glass {
  background: rgba(239, 68, 68, 0.15);
  color: #b91c1c;
}
.alert-success-glass {
  background: rgba(16, 185, 129, 0.15);
  color: #047857;
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
  text-decoration: none;
}

.badge-soft-primary {
  background: rgba(2, 132, 199, 0.15);
  color: #0369a1;
  padding: 4px 12px;
  border-radius: 10px;
  font-size: 0.78rem;
  font-weight: 700;
}

/* ── DRAG & DROP RASM YUKLASH ZONASI ── */
.upload-dropzone {
  border: 2px dashed rgba(2, 132, 199, 0.35);
  border-radius: 16px;
  background: rgba(2, 132, 199, 0.04);
  padding: 28px 16px;
  text-align: center;
  cursor: pointer;
  transition: border-color 0.2s ease, background 0.2s ease, transform 0.15s ease;
}
.upload-dropzone:hover {
  border-color: rgba(2, 132, 199, 0.6);
  background: rgba(2, 132, 199, 0.07);
}
.upload-dropzone.is-dragover {
  border-color: #0284c7;
  background: rgba(2, 132, 199, 0.12);
  transform: scale(1.01);
}

.upload-icon {
  font-size: 1.8rem;
  color: #0284c7;
  margin-bottom: 8px;
  display: block;
}
.upload-text {
  color: #334155;
  font-weight: 600;
  font-size: 0.92rem;
}
.upload-link {
  color: #0284c7;
  text-decoration: underline;
}
.upload-hint {
  color: #64748b;
  font-size: 0.78rem;
}

.upload-preview-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
  gap: 10px;
}
.upload-preview-item {
  position: relative;
  aspect-ratio: 1 / 1;
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid rgba(0, 0, 0, 0.08);
}
.upload-preview-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.preview-remove-btn {
  position: absolute;
  top: 4px;
  right: 4px;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  border: none;
  background: rgba(15, 23, 42, 0.65);
  color: #fff;
  font-size: 0.7rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.15s ease;
}
.preview-remove-btn:hover {
  background: #ef4444;
}

.upload-counter {
  font-size: 0.82rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 6px;
}
.upload-counter.counter-ok {
  color: #059669;
}
.upload-counter.counter-warn {
  color: #d97706;
}

/* ── O'NG PANEL: OLDINGI LAVHALAR ── */
.empty-logs-state {
  text-align: center;
  color: #64748b;
  padding: 32px 8px;
}

.logs-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
  max-height: 640px;
  overflow-y: auto;
  padding-right: 4px;
}

.log-item-photos {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 6px;
  margin-bottom: 8px;
}

.log-item-photos img {
  width: 100%;
  aspect-ratio: 1 / 1;
  object-fit: cover;
  border-radius: 8px;
}

.log-item-more {
  display: flex;
  align-items: center;
  justify-content: center;
  aspect-ratio: 1 / 1;
  border-radius: 8px;
  background: rgba(2, 132, 199, 0.1);
  color: #0284c7;
  font-weight: 700;
  font-size: 0.8rem;
}

.log-item-desc {
  color: #334155;
  font-size: 0.86rem;
  margin: 0 0 4px;
  line-height: 1.4;
}

.log-item-date {
  color: #64748b;
  font-size: 0.75rem;
  font-weight: 600;
}
.log-item {
  border: 1px solid rgba(0, 0, 0, 0.06);
  border-radius: 14px;
  padding: 12px;
  background: rgba(255, 255, 255, 0.5);
  position: relative; /* O'chirish tugmasini joylashtirish uchun */
}

.log-delete-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  border: none;
  background: rgba(239, 68, 68, 0.15);
  color: #ef4444;
  font-size: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  cursor: pointer;
  z-index: 2;
}

.log-item.is-expired {
  opacity: 0.65;
}

.log-expired-badge {
  display: inline-block;
  margin-left: 8px;
  padding: 2px 8px;
  border-radius: 8px;
  background: rgba(217, 119, 6, 0.12);
  color: #b45309;
  font-size: 0.72rem;
  font-weight: 700;
}

.desc-counter {
  text-align: right;
  color: #94a3b8;
  font-size: 0.75rem;
  margin-top: 4px;
}

.log-delete-btn:disabled {
  cursor: wait;
}

.log-delete-btn:hover:not(:disabled) {
  background: #ef4444;
  color: #fff;
  transform: scale(1.05);
}
</style>
