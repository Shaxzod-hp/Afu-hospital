<template>
  <div class="management-page">
    <div class="glass-panel form-card">
      <div class="d-flex align-items-center justify-content-between mb-4">
        <h5 class="fw-700 mb-0">Operatsiyani tahrirlash</h5>
        <router-link
          to="/admin/surgeries"
          class="btn-secondary-glass text-decoration-none"
        >
          <i class="fas fa-arrow-left me-2"></i> Orqaga
        </router-link>
      </div>

      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
      </div>

      <form v-else @submit.prevent="handleSubmit">
        <div class="row g-4">
          <div class="col-12 col-md-6">
            <label class="form-label-glass">Nomi *</label>
            <input
              type="text"
              v-model="form.name"
              class="input-glass"
              :class="{ 'is-invalid-glass': errors.name }"
            />
            <div v-if="errors.name" class="error-text">{{ errors.name }}</div>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label-glass">Rasm</label>
            <input
              type="file"
              accept="image/jpeg,image/png,image/webp"
              class="input-glass"
              :class="{ 'is-invalid-glass': errors.photo }"
              @change="handlePhotoChange"
            />
            <div v-if="errors.photo" class="error-text">{{ errors.photo }}</div>
            <img
              v-if="photoPreview || existingPhoto"
              :src="photoPreview || existingPhoto"
              class="photo-preview mt-2"
            />
          </div>

          <div class="col-12">
            <label class="form-label-glass">Qisqa tavsif</label>
            <textarea
              v-model="form.short_description"
              class="input-glass"
              rows="3"
            ></textarea>
          </div>

          <div class="col-12">
            <label class="form-label-glass">Narxga kirgan xizmatlar</label>
            <div
              v-for="(item, idx) in form.included_items"
              :key="idx"
              class="d-flex gap-2 mb-2"
            >
              <input
                type="text"
                v-model="item.name"
                class="input-glass"
                placeholder="Xizmat nomi"
              />
              <input
                type="number"
                v-model="item.price"
                class="input-glass"
                style="max-width: 180px"
                placeholder="Narxi (so'm)"
                min="0"
              />
              <button
                type="button"
                class="btn-icon-danger"
                @click="removeItem(idx)"
              >
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>
            <button type="button" class="btn-outline-glass" @click="addItem">
              <i class="fas fa-plus me-2"></i> Band qo'shish
            </button>
          </div>
        </div>

        <div class="d-flex gap-2 mt-4">
          <button
            type="submit"
            class="btn-primary-glass"
            :disabled="submitting"
          >
            <span
              v-if="submitting"
              class="spinner-border spinner-border-sm me-2"
            ></span>
            Saqlash
          </button>
          <router-link
            to="/admin/surgeries"
            class="btn-secondary-glass text-decoration-none"
          >
            Bekor qilish
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import surgeryService from "../../../services/surgeryService";

const route = useRoute();
const router = useRouter();
const backendUrl = import.meta.env.VITE_STORAGE_URL || "";

const form = reactive({
  name: "",
  short_description: "",
  included_items: [],
});

const loading = ref(true);
const photoFile = ref(null);
const photoPreview = ref(null);
const existingPhoto = ref(null);
const errors = ref({});
const submitting = ref(false);

const loadSurgery = async () => {
  loading.value = true;
  try {
    const data = await surgeryService.fetchOne(route.params.id);
    const surgery = data?.data || data;
    form.name = surgery.name;
    form.short_description = surgery.short_description || "";
    form.included_items = surgery.included_items || [];
    if (surgery.photo) {
      existingPhoto.value = surgery.photo.startsWith("http")
        ? surgery.photo
        : backendUrl + surgery.photo;
    }
  } catch (err) {
    alert("Ma'lumotni yuklashda xatolik yuz berdi.");
    router.push("/admin/surgeries");
  } finally {
    loading.value = false;
  }
};

onMounted(loadSurgery);

const handlePhotoChange = (e) => {
  const file = e.target.files[0];
  if (!file) return;
  photoFile.value = file;
  photoPreview.value = URL.createObjectURL(file);
};

const addItem = () => {
  form.included_items.push({ name: "", price: "" });
};

const removeItem = (idx) => {
  form.included_items.splice(idx, 1);
};

const buildFormData = () => {
  const fd = new FormData();
  fd.append("name", form.name);
  fd.append("short_description", form.short_description || "");
  if (photoFile.value) fd.append("photo", photoFile.value);
  const items = form.included_items.filter(
    (item) => item.name && String(item.name).trim() !== ""
  );
  if (items.length) {
    items.forEach((item, idx) => {
      fd.append(`included_items[${idx}][name]`, item.name);
      fd.append(`included_items[${idx}][price]`, item.price);
    });
  } else {
    // Ro'yxat bo'sh bo'lsa ham yuboramiz — backend uni tozalaydi
    fd.append("included_items", "");
  }
  return fd;
};

const handleSubmit = async () => {
  errors.value = {};
  submitting.value = true;
  try {
    await surgeryService.update(route.params.id, buildFormData());
    router.push("/admin/surgeries");
  } catch (err) {
    if (err.response?.status === 422) {
      const raw = err.response.data.errors || {};
      Object.keys(raw).forEach((key) => {
        errors.value[key] = raw[key][0];
      });
    } else {
      alert(err.response?.data?.message || "Saqlashda xatolik yuz berdi.");
    }
  } finally {
    submitting.value = false;
  }
};
</script>

<style scoped>
.fw-700 {
  font-weight: 700;
}

.glass-panel {
  background: rgba(255, 255, 255, 0.2) !important;
  backdrop-filter: blur(16px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.4) !important;
  border-radius: 20px;
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.04);
}

.form-card {
  padding: 28px;
}

.form-label-glass {
  display: block;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 8px;
  font-size: 0.88rem;
}

.input-glass {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid rgba(255, 255, 255, 0.4) !important;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.25) !important;
  outline: none;
  color: #0f172a;
  font-weight: 600;
}

.input-glass:focus {
  border-color: #0284c7 !important;
  box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
}

.is-invalid-glass {
  border-color: #e31e24 !important;
}

.error-text {
  color: #e31e24;
  font-size: 0.8rem;
  font-weight: 600;
  margin-top: 4px;
}

.photo-preview {
  height: 100px;
  border-radius: 12px;
  object-fit: cover;
}

.btn-primary-glass {
  background: #0284c7;
  color: white;
  padding: 12px 24px;
  border: none;
  border-radius: 14px;
  font-weight: 700;
  transition: all 0.25s ease;
  box-shadow: 0 4px 16px rgba(2, 132, 199, 0.25);
  white-space: nowrap;
}

.btn-primary-glass:hover {
  box-shadow: 0 6px 20px rgba(2, 132, 199, 0.35);
}

.btn-secondary-glass {
  background: rgba(255, 255, 255, 0.3);
  color: #334155;
  padding: 12px 24px;
  border: 1px solid rgba(255, 255, 255, 0.4);
  border-radius: 14px;
  font-weight: 700;
  transition: all 0.25s ease;
}

.btn-secondary-glass:hover {
  background: rgba(255, 255, 255, 0.5);
}

.btn-outline-glass {
  background: transparent;
  color: #0284c7;
  padding: 10px 18px;
  border: 1.5px dashed rgba(2, 132, 199, 0.4);
  border-radius: 12px;
  font-weight: 700;
  font-size: 0.85rem;
  transition: all 0.2s ease;
}

.btn-outline-glass:hover {
  background: rgba(2, 132, 199, 0.06);
  border-color: #0284c7;
}

.btn-icon-danger {
  width: 44px;
  min-width: 44px;
  border: none;
  border-radius: 12px;
  background: rgba(227, 30, 36, 0.12);
  color: #e31e24;
  transition: all 0.2s ease;
}

.btn-icon-danger:hover {
  background: #e31e24;
  color: white;
}
</style>
