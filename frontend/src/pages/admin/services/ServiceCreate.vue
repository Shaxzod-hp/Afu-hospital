<template>
  <div class="management-page">
    <div
      class="page-header-glass glass-panel mb-4 d-flex align-items-center justify-content-between"
    >
      <h5 class="m-0 fw-800">
        <i class="fas fa-concierge-bell me-2"></i> Yangi xizmat qo'shish
      </h5>
      <router-link
        to="/admin/services"
        class="btn-secondary-glass text-decoration-none"
      >
        <i class="fas fa-arrow-left me-2"></i> Ortga
      </router-link>
    </div>

    <div class="content-card glass-panel p-4">
      <div v-if="formError" class="alert-glass alert-danger-glass mb-4">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ formError }}
      </div>

      <form @submit.prevent="saveService">
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
          <div class="col-12">
            <label class="form-label-glass"
              >Xizmat nomi <span class="text-danger">*</span></label
            >
            <input
              type="text"
              v-model="form.name"
              class="form-control-glass"
              placeholder="Masalan: Umumiy tekshiruv"
              required
            />
          </div>

          <div class="col-12">
            <label class="form-label-glass">Qisqacha tavsif</label>
            <textarea
              v-model="form.short_description"
              class="form-control-glass"
              rows="3"
              placeholder="Xizmat haqida qisqacha ma'lumot..."
            ></textarea>
          </div>

          <!-- Included items -->
          <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <label class="form-label-glass mb-0"
                >Xizmat bandlari (nomi va narxi)</label
              >
              <button type="button" class="btn-add-item" @click="addItem">
                <i class="fas fa-plus me-1"></i> Band qo'shish
              </button>
            </div>

            <div
              v-if="form.items.length === 0"
              class="small text-muted-glass mb-2"
            >
              Hozircha bandlar qo'shilmagan. Kamida bitta band qo'shing.
            </div>

            <div
              v-for="(item, index) in form.items"
              :key="index"
              class="item-row"
            >
              <input
                type="text"
                v-model="item.name"
                class="form-control-glass"
                placeholder="Masalan: Konsultatsiya"
              />
              <input
                type="number"
                v-model.number="item.price"
                class="form-control-glass price-input"
                placeholder="Narxi"
                min="0"
              />
              <button
                type="button"
                class="btn-remove-item"
                @click="removeItem(index)"
                title="O'chirish"
              >
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
          <router-link
            to="/admin/services"
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
import { ref } from "vue";
import { useRouter } from "vue-router";
import servicesService from "../../../services/servicesService";

const router = useRouter();

const saving = ref(false);
const formError = ref("");
const photoPreview = ref(null);
const fileInput = ref(null);

const form = ref({
  name: "",
  short_description: "",
  items: [],
  photoFile: null,
});

const addItem = () => {
  form.value.items.push({ name: "", price: null });
};

const removeItem = (index) => {
  form.value.items.splice(index, 1);
};

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

const saveService = async () => {
  if (!form.value.name || form.value.name.length < 2) {
    formError.value = "Xizmat nomi kamida 2 ta belgidan iborat bo'lishi shart!";
    return;
  }

  const validItems = form.value.items.filter(
    (i) => i.name && i.name.trim() !== ""
  );
  for (const item of validItems) {
    if (
      item.price === null ||
      item.price === "" ||
      isNaN(item.price) ||
      item.price < 0
    ) {
      formError.value = `"${item.name}" bandi uchun to'g'ri narx kiriting!`;
      return;
    }
  }

  saving.value = true;
  formError.value = "";

  try {
    const formData = new FormData();
    formData.append("name", form.value.name);
    if (form.value.short_description)
      formData.append("short_description", form.value.short_description);
    if (form.value.photoFile) formData.append("photo", form.value.photoFile);

    validItems.forEach((item, index) => {
      formData.append(`included_items[${index}][name]`, item.name);
      formData.append(`included_items[${index}][price]`, item.price);
    });

    await servicesService.create(formData);
    router.push("/admin/services");
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

.btn-add-item {
  background: rgba(2, 132, 199, 0.1);
  color: #0284c7;
  border: none;
  padding: 8px 16px;
  border-radius: 10px;
  font-weight: 700;
  font-size: 0.85rem;
  transition: all 0.2s ease;
}
.btn-add-item:hover {
  background: #0284c7;
  color: white;
}

.item-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}

.item-row .form-control-glass {
  flex: 1;
}
.price-input {
  max-width: 160px;
}

.btn-remove-item {
  width: 40px;
  height: 40px;
  min-width: 40px;
  border-radius: 10px;
  border: none;
  background: rgba(227, 30, 36, 0.12);
  color: #e31e24;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}
.btn-remove-item:hover {
  background: #e31e24;
  color: white;
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
  background: rgba(227, 30, 36, 0.15);
  color: #c1191e;
  border: 1px solid rgba(227, 30, 36, 0.3);
}
</style>
