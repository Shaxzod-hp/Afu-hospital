<template>
  <div class="management-page">
    <div
      class="page-header-glass glass-panel mb-4 d-flex align-items-center justify-content-between"
    >
      <h5 class="m-0 fw-800">
        <i class="fas fa-bed me-2"></i> Yangi statsionar paketi qo'shish
      </h5>
      <router-link
        to="/admin/statsionar"
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

      <form @submit.prevent="savePackage">
        <div class="row g-4">
          <!-- Multi-photo upload -->
          <div class="col-12">
            <label class="form-label-glass">
              Rasmlar <span class="text-danger">*</span>
              <span class="small text-muted-glass fw-normal"
                >(kamida 2 ta, har biri max 5MB, JPG/PNG/WebP)</span
              >
            </label>

            <input
              type="file"
              ref="fileInput"
              accept="image/jpeg,image/png,image/webp,image/jpg"
              class="d-none"
              multiple
              @change="handleFilesChange"
            />

            <div class="photo-grid">
              <div
                v-for="(preview, index) in photoPreviews"
                :key="index"
                class="photo-thumb"
              >
                <img :src="preview" alt="Preview" />
                <button
                  type="button"
                  class="remove-photo-btn"
                  @click="removePhoto(index)"
                  title="O'chirish"
                >
                  <i class="fas fa-times"></i>
                </button>
              </div>

              <div
                class="photo-thumb add-thumb"
                @click="$refs.fileInput.click()"
              >
                <i class="fas fa-plus"></i>
                <span>Rasm qo'shish</span>
              </div>
            </div>
          </div>

          <!-- Basic info -->
          <div class="col-12">
            <label class="form-label-glass"
              >Paket nomi <span class="text-danger">*</span></label
            >
            <input
              type="text"
              v-model="form.name"
              class="form-control-glass"
              placeholder="Masalan: VIP palata"
              required
            />
          </div>

          <!-- Included services -->
          <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <label class="form-label-glass mb-0"
                >Paketga kiradigan xizmatlar</label
              >
              <button type="button" class="btn-add-item" @click="addItem">
                <i class="fas fa-plus me-1"></i> Xizmat qo'shish
              </button>
            </div>

            <div
              v-if="form.items.length === 0"
              class="small text-muted-glass mb-2"
            >
              Hozircha xizmat qo'shilmagan.
            </div>

            <div
              v-for="(item, index) in form.items"
              :key="index"
              class="item-row"
            >
              <input
                type="text"
                v-model="form.items[index]"
                class="form-control-glass"
                placeholder="Masalan: 3 mahal ovqatlanish"
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

          <!-- Note -->
          <div class="col-12">
            <label class="form-label-glass">Eslatma</label>
            <textarea
              v-model="form.note"
              class="form-control-glass"
              rows="3"
              placeholder="Qo'shimcha eslatma yoki shartlar..."
            ></textarea>
          </div>

          <!-- Price -->
          <div class="col-md-6">
            <label class="form-label-glass">Yakuniy narx (so'm)</label>
            <input
              type="number"
              v-model.number="form.price"
              class="form-control-glass"
              min="0"
              placeholder="Masalan: 850000"
            />
          </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
          <router-link
            to="/admin/statsionar"
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
import statsionarService from "../../../services/statsionarService";

const router = useRouter();

const saving = ref(false);
const formError = ref("");
const fileInput = ref(null);
const photoFiles = ref([]);
const photoPreviews = ref([]);

const form = ref({
  name: "",
  note: "",
  price: null,
  items: [],
});

const addItem = () => {
  form.value.items.push("");
};

const removeItem = (index) => {
  form.value.items.splice(index, 1);
};

const handleFilesChange = (e) => {
  const files = Array.from(e.target.files || []);
  if (files.length === 0) return;

  for (const file of files) {
    if (file.size > 5 * 1024 * 1024) {
      formError.value = `"${file.name}" hajmi 5MB dan oshib ketdi!`;
      continue;
    }
    photoFiles.value.push(file);
    photoPreviews.value.push(URL.createObjectURL(file));
  }

  formError.value = "";
  e.target.value = "";
};

const removePhoto = (index) => {
  photoFiles.value.splice(index, 1);
  photoPreviews.value.splice(index, 1);
};

const savePackage = async () => {
  if (!form.value.name || form.value.name.length < 2) {
    formError.value = "Paket nomi kamida 2 ta belgidan iborat bo'lishi shart!";
    return;
  }

  if (photoFiles.value.length < 2) {
    formError.value = "Kamida 2 ta rasm yuklashingiz shart!";
    return;
  }

  saving.value = true;
  formError.value = "";

  try {
    const formData = new FormData();
    formData.append("name", form.value.name);
    if (form.value.note) formData.append("note", form.value.note);
    if (form.value.price !== null && form.value.price !== "") {
      formData.append("price", form.value.price);
    }

    const validItems = form.value.items.filter((i) => i && i.trim() !== "");
    validItems.forEach((item, index) => {
      formData.append(`included_items[${index}]`, item);
    });

    photoFiles.value.forEach((file) => {
      formData.append("photos[]", file);
    });

    await statsionarService.create(formData);
    router.push("/admin/statsionar");
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

/* Photo grid */
.photo-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
  gap: 12px;
}

.photo-thumb {
  position: relative;
  height: 120px;
  border-radius: 14px;
  overflow: hidden;
  background: rgba(255, 255, 255, 0.5);
}

.photo-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-photo-btn {
  position: absolute;
  top: 6px;
  right: 6px;
  width: 26px;
  height: 26px;
  border-radius: 8px;
  border: none;
  background: rgba(15, 23, 42, 0.65);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
}
.remove-photo-btn:hover {
  background: #e31e24;
}

.add-thumb {
  border: 2px dashed rgba(2, 132, 199, 0.35);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 6px;
  cursor: pointer;
  color: #0284c7;
  font-size: 0.8rem;
  font-weight: 600;
  transition: all 0.2s ease;
}
.add-thumb i {
  font-size: 1.3rem;
}
.add-thumb:hover {
  background: rgba(2, 132, 199, 0.06);
  border-color: #0284c7;
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
