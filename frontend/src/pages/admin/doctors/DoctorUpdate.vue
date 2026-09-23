<template>
  <div class="management-page">
    <div
      class="page-header-glass glass-panel mb-4 d-flex align-items-center justify-content-between"
    >
      <h5 class="m-0 fw-800">
        <i class="fas fa-user-edit me-2"></i> Shifokor ma'lumotlarini tahrirlash
      </h5>
      <router-link
        to="/admin/doctors"
        class="btn-secondary-glass text-decoration-none"
      >
        <i class="fas fa-arrow-left me-2"></i> Ortga
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="content-card glass-panel p-5 text-center">
      <div class="spinner-border text-primary" role="status"></div>
      <div class="mt-2 text-muted-glass">Ma'lumotlar yuklanmoqda...</div>
    </div>

    <!-- Load Error State -->
    <div v-else-if="loadError" class="content-card glass-panel p-5 text-center">
      <i class="fas fa-exclamation-triangle mb-2 fs-3 text-danger"></i>
      <p class="m-0 fw-700">{{ loadError }}</p>
      <button class="btn btn-sm btn-outline-danger mt-3" @click="fetchDoctor">
        <i class="fas fa-redo me-1"></i> Qayta urinish
      </button>
    </div>

    <div v-else class="content-card glass-panel p-4">
      <div v-if="formError" class="alert-glass alert-danger-glass mb-4">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ formError }}
      </div>

      <form @submit.prevent="saveDoctor">
        <div class="row g-4">
          <!-- Photo upload -->
          <div class="col-12">
            <label class="form-label-glass"
              >Rasm (Max: 5MB, JPG/PNG/WebP)</label
            >
            <input
              type="file"
              ref="fileInput"
              accept="image/jpeg,image/png,image/webp,image/jpg"
              class="d-none"
              @change="handleFileChange"
            />
            <div class="upload-dropzone" @click="$refs.fileInput.click()">
              <div v-if="photoPreview" class="preview-wrapper">
                <img :src="photoPreview" alt="Preview" class="preview-img" />
                <div class="preview-overlay">
                  <i class="fas fa-camera me-1"></i> Rasmni almashtirish
                </div>
              </div>
              <div v-else class="upload-placeholder">
                <i class="fas fa-cloud-upload-alt upload-icon"></i>
                <p class="mb-0">Rasm yuklash uchun bosing</p>
              </div>
            </div>
          </div>

          <!-- Basic info -->
          <div class="col-md-6">
            <label class="form-label-glass"
              >Ism familiya <span class="text-danger">*</span></label
            >
            <input
              type="text"
              v-model="form.full_name"
              class="form-control-glass"
              placeholder="Masalan: Aliyev Vali"
              required
            />
          </div>

          <div class="col-md-6">
            <label class="form-label-glass"
              >Yo'nalish <span class="text-danger">*</span></label
            >
            <select
              v-model="form.specialization_id"
              class="form-control-glass"
              required
            >
              <option value="" disabled>Yo'nalishni tanlang</option>
              <option
                v-for="spec in specializations"
                :key="spec.id"
                :value="spec.id"
              >
                {{ spec.name }}
              </option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label-glass">Lavozimi</label>
            <input
              type="text"
              v-model="form.position"
              class="form-control-glass"
              placeholder="Masalan: Bosh shifokor"
            />
          </div>

          <div class="col-md-6">
            <label class="form-label-glass">Ish tajribasi (yil)</label>
            <input
              type="number"
              v-model.number="form.experience_years"
              class="form-control-glass"
              min="0"
              max="80"
              placeholder="0"
            />
          </div>

          <div class="col-12">
            <label class="form-label-glass">Shifokor haqida ma'lumot</label>
            <textarea
              v-model="form.bio"
              class="form-control-glass"
              rows="3"
              placeholder="Shifokor haqida umumiy ma'lumot..."
            ></textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label-glass">Ta'lim (qayerda o'qigan)</label>
            <textarea
              v-model="form.education"
              class="form-control-glass"
              rows="2"
              placeholder="Masalan: Toshkent Tibbiyot Akademiyasi, 2015"
            ></textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label-glass">Oldingi ish joyi</label>
            <textarea
              v-model="form.previous_workplace"
              class="form-control-glass"
              rows="2"
              placeholder="Oldin qayerda ishlagan"
            ></textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label-glass">Telefon raqami</label>
            <input
              type="text"
              v-model="form.phone"
              class="form-control-glass"
              placeholder="+998 90 123 45 67"
            />
          </div>

          <!-- Haftalik ish vaqti grafigi -->
          <div class="col-12">
            <label class="form-label-glass mb-3">Ish vaqti grafigi</label>
            <div class="schedule-container p-3 rounded-4 border bg-white-50">
              <div
                v-for="day in weekDaysMap"
                :key="day.key"
                class="row align-items-center mb-3 pb-2 border-bottom last-no-border"
              >
                <div class="col-md-3 col-4">
                  <div class="form-check form-switch">
                    <input
                      type="checkbox"
                      class="form-check-input"
                      :id="'day_' + day.key"
                      v-model="form.schedule[day.key].active"
                    />
                    <label
                      class="form-check-label fw-bold text-dark"
                      :for="'day_' + day.key"
                    >
                      {{ day.label }}
                    </label>
                  </div>
                </div>

                <template v-if="form.schedule[day.key].active">
                  <div class="col-md-4 col-4">
                    <div class="d-flex align-items-center gap-2">
                      <span class="small text-muted">Dan:</span>
                      <select
                        v-model="form.schedule[day.key].start"
                        class="form-control-glass py-1"
                      >
                        <option
                          v-for="time in timeSlots"
                          :key="time"
                          :value="time"
                        >
                          {{ time }}
                        </option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4 col-4">
                    <div class="d-flex align-items-center gap-2">
                      <span class="small text-muted">Gacha:</span>
                      <select
                        v-model="form.schedule[day.key].end"
                        class="form-control-glass py-1"
                      >
                        <option
                          v-for="time in timeSlots"
                          :key="time"
                          :value="time"
                        >
                          {{ time }}
                        </option>
                      </select>
                    </div>
                  </div>
                </template>

                <div v-else class="col-8 text-muted small fst-italic">
                  Dam olish kuni
                </div>
              </div>
            </div>
          </div>

          <!-- Social -->
          <div class="col-12">
            <label class="form-label-glass mb-2 d-block"
              >Ijtimoiy tarmoqlar</label
            >
            <div class="row g-3">
              <div class="col-md-4">
                <div class="social-input">
                  <i class="fab fa-instagram"></i>
                  <input
                    type="text"
                    v-model="form.instagram"
                    placeholder="Instagram"
                  />
                </div>
              </div>
              <div class="col-md-4">
                <div class="social-input">
                  <i class="fab fa-telegram"></i>
                  <input
                    type="text"
                    v-model="form.telegram"
                    placeholder="Telegram"
                  />
                </div>
              </div>
              <div class="col-md-4">
                <div class="social-input">
                  <i class="fab fa-whatsapp"></i>
                  <input
                    type="text"
                    v-model="form.whatsapp"
                    placeholder="WhatsApp"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
          <router-link
            to="/admin/doctors"
            class="btn-secondary-glass text-decoration-none"
            >Bekor qilish</router-link
          >
          <button type="submit" class="btn-primary-glass" :disabled="saving">
            <span
              v-if="saving"
              class="spinner-border spinner-border-sm me-2"
            ></span>
            Saqlash
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import doctorsService from "../../../services/doctorsService";
import specializationsService from "../../../services/specializationsService";

const route = useRoute();
const router = useRouter();

const specializations = ref([]);
const loading = ref(true);
const loadError = ref("");
const saving = ref(false);
const formError = ref("");
const photoPreview = ref(null);
const fileInput = ref(null);

const timeSlots = [
  "07:00",
  "08:00",
  "09:00",
  "10:00",
  "11:00",
  "12:00",
  "13:00",
  "14:00",
  "15:00",
  "16:00",
  "17:00",
  "18:00",
  "19:00",
  "20:00",
  "21:00",
];

const weekDaysMap = [
  { key: "monday", label: "Dushanba" },
  { key: "tuesday", label: "Seshanba" },
  { key: "wednesday", label: "Chorshanba" },
  { key: "thursday", label: "Payshanba" },
  { key: "friday", label: "Juma" },
  { key: "saturday", label: "Shanba" },
  { key: "sunday", label: "Yakshanba" },
];

const form = ref({
  full_name: "",
  specialization_id: "",
  position: "",
  experience_years: null,
  bio: "",
  education: "",
  previous_workplace: "",
  phone: "",
  instagram: "",
  telegram: "",
  whatsapp: "",
  photoFile: null,
  schedule: {
    monday: { active: true, start: "09:00", end: "18:00" },
    tuesday: { active: true, start: "09:00", end: "18:00" },
    wednesday: { active: true, start: "09:00", end: "18:00" },
    thursday: { active: true, start: "09:00", end: "18:00" },
    friday: { active: true, start: "09:00", end: "18:00" },
    saturday: { active: false, start: "09:00", end: "14:00" },
    sunday: { active: false, start: "09:00", end: "14:00" },
  },
});

const fetchSpecializations = async () => {
  try {
    const data = await specializationsService.fetchAll();
    specializations.value = Array.isArray(data) ? data : data?.data || [];
  } catch (err) {
    specializations.value = [];
  }
};

const fetchDoctor = async () => {
  loading.value = true;
  loadError.value = "";
  try {
    const data = await doctorsService.fetchOne(route.params.id);
    const doctor = data?.data || data;

    // Backend doctor_schedules jadvalidan `schedules` massivini qaytaradi:
    // [{ day: "monday", start_time: "09:00:00", end_time: "18:00:00" }, ...]
    // Undan formadagi { monday: { active, start, end }, ... } obyektini quramiz.
    let parsedSchedule = form.value.schedule;
    if (Array.isArray(doctor.schedules)) {
      const hhmm = (t, fallback) => (t ? String(t).slice(0, 5) : fallback);
      parsedSchedule = {};
      for (const { key } of weekDaysMap) {
        const row = doctor.schedules.find(
          (s) => String(s.day).toLowerCase() === key
        );
        parsedSchedule[key] = row
          ? {
              active: row.is_available !== false && row.is_available !== 0,
              start: hhmm(row.start_time, "09:00"),
              end: hhmm(row.end_time, "18:00"),
            }
          : { active: false, start: "09:00", end: "18:00" };
      }
    }

    form.value = {
      full_name: doctor.full_name || "",
      specialization_id:
        doctor.specialization_id || doctor.specialization?.id || "",
      position: doctor.position || "",
      experience_years: doctor.experience_years ?? null,
      bio: doctor.bio || "",
      education: doctor.education || "",
      previous_workplace: doctor.previous_workplace || "",
      phone: doctor.phone || "",
      instagram: doctor.instagram || "",
      telegram: doctor.telegram || "",
      whatsapp: doctor.whatsapp || "",
      photoFile: null,
      schedule: parsedSchedule,
    };
    photoPreview.value = doctor.photo
      ? doctor.photo.startsWith("http")
        ? doctor.photo
        : (import.meta.env.VITE_STORAGE_URL || "") + doctor.photo
      : null;
  } catch (err) {
    loadError.value = "Shifokor ma'lumotlarini yuklashda xatolik yuz berdi.";
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  await fetchSpecializations();
  await fetchDoctor();
});

const handleFileChange = (e) => {
  const file = e.target.files[0];
  if (!file) return;

  if (file.size > 5 * 1024 * 1024) {
    formError.value = "Rasm hajmi 5MB dan oshmasligi kerak!";
    return;
  }

  form.value.photoFile = file;
  photoPreview.value = URL.createObjectURL(file);
  formError.value = "";
};

const saveDoctor = async () => {
  if (!form.value.full_name || form.value.full_name.length < 2) {
    formError.value =
      "Ism familiya kamida 2 ta belgidan iborat bo'lishi shart!";
    return;
  }
  if (!form.value.specialization_id) {
    formError.value = "Yo'nalishni tanlash shart!";
    return;
  }

  saving.value = true;
  formError.value = "";

  try {
    const formData = new FormData();
    formData.append("full_name", form.value.full_name);
    formData.append("specialization_id", form.value.specialization_id);
    // Bo'sh maydonlar ham yuboriladi: backend ularni null qiladi va
    // admin eski qiymatni o'chira oladi
    const optional = [
      "position",
      "experience_years",
      "bio",
      "education",
      "previous_workplace",
      "phone",
      "instagram",
      "telegram",
      "whatsapp",
    ];
    for (const key of optional) {
      formData.append(key, form.value[key] ?? "");
    }

    // Ish vaqtini JSON formatida jo'natamiz
    formData.append("schedule", JSON.stringify(form.value.schedule));

    if (form.value.photoFile) formData.append("photo", form.value.photoFile);

    await doctorsService.update(route.params.id, formData);
    router.push("/admin/doctors");
  } catch (err) {
    if (err.response?.data?.message) {
      formError.value = err.response.data.message;
    } else if (err.response?.data?.errors) {
      const firstError = Object.values(err.response.data.errors)[0];
      formError.value = Array.isArray(firstError)
        ? firstError[0]
        : "Saqlashda xatolik yuz berdi.";
    } else {
      formError.value = "Saqlashda xatolik yuz berdi. Qayta urinib ko'ring.";
    }
  } finally {
    saving.value = false;
  }
};
</script>

<style scoped>
.fw-700 {
  font-weight: 700;
}
.fw-800 {
  font-weight: 800;
}
.text-muted-glass {
  color: #64748b;
}

.glass-panel {
  background: rgba(255, 255, 255, 0.2) !important;
  backdrop-filter: blur(16px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.4) !important;
  border-radius: 20px;
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.04);
}

.page-header-glass {
  padding: 18px 24px;
}
.content-card {
  overflow: hidden;
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
  border: 1px solid rgba(255, 255, 255, 0.4);
  display: inline-flex;
  align-items: center;
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

.social-input {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.8);
}

.social-input i {
  color: #64748b;
  width: 18px;
  text-align: center;
}

.social-input input {
  flex: 1;
  border: nil;
  border: none;
  outline: none;
  background: transparent;
  font-weight: 600;
  color: #0f172a;
}

.upload-dropzone {
  border: 2px dashed rgba(2, 132, 199, 0.3);
  border-radius: 16px;
  padding: 16px;
  text-align: center;
  cursor: pointer;
  background: rgba(255, 255, 255, 0.5);
  transition: all 0.2s ease;
}
.upload-dropzone:hover {
  border-color: #0284c7;
  background: rgba(2, 132, 199, 0.05);
}

.upload-icon {
  font-size: 2rem;
  color: #0284c7;
  margin-bottom: 8px;
}

.preview-wrapper {
  position: relative;
  height: 160px;
  border-radius: 12px;
  overflow: hidden;
  max-width: 200px;
  margin: 0 auto;
}
.preview-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.preview-overlay {
  position: absolute;
  inset: 0;
  background: rgba(15, 23, 42, 0.5);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  opacity: 0;
  transition: opacity 0.2s ease;
}
.preview-wrapper:hover .preview-overlay {
  opacity: 1;
}

.alert-glass {
  padding: 14px 20px;
  border-radius: 14px;
  font-weight: 600;
  font-size: 0.9rem;
}
.alert-danger-glass {
  background: rgba(239, 68, 68, 0.15);
  color: #b91c1c;
  border: 1px solid rgba(239, 68, 68, 0.3);
}
</style>
