<template>
  <div class="news-form-page">
    <!-- Header Bar -->
    <div
      class="glass-panel p-4 mb-4 d-flex align-items-center justify-content-between gap-3"
    >
      <div class="d-flex align-items-center gap-3">
        <router-link
          to="/admin/news"
          class="btn-back-glass text-decoration-none"
        >
          <i class="fas fa-arrow-left"></i>
        </router-link>
        <h3 class="m-0 fw-800 text-dark-slate">
          {{ isEdit ? "Yangilikni tahrirlash" : "Yangi yangilik qo'shish" }}
        </h3>
      </div>
    </div>

    <!-- Form Container -->
    <div class="glass-panel p-4 p-md-5">
      <form @submit.prevent="handleSubmit" class="premium-form">
        <div class="row g-4">
          <!-- Rasm Yuklash (File Upload) -->
          <div class="col-12">
            <label class="form-label">Yangilik rasmi</label>
            <div class="image-upload-wrapper">
              <div class="row align-items-center g-3">
                <!-- Preview area -->
                <div class="col-auto" v-if="imagePreview">
                  <div
                    class="preview-box position-relative rounded-3 overflow-hidden shadow-sm"
                  >
                    <img
                      :src="imagePreview"
                      alt="Preview"
                      class="w-100 h-100 object-fit-cover"
                    />
                    <button
                      type="button"
                      class="btn-remove-img position-absolute top-0 end-0 m-1"
                      @click="removeImage"
                    >
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>

                <!-- Input area -->
                <div class="col">
                  <input
                    type="file"
                    ref="fileInput"
                    accept="image/*"
                    class="d-none"
                    @change="handleFileChange"
                  />
                  <button
                    type="button"
                    class="btn-upload-glass w-100 py-3 d-flex flex-column align-items-center justify-content-center gap-2"
                    @click="$refs.fileInput.click()"
                  >
                    <i class="fas fa-cloud-upload-alt fs-4 text-primary"></i>
                    <span class="fw-700 text-secondary small">
                      {{
                        imagePreview
                          ? "Rasmni almashtirish"
                          : "Kompyuterdan rasm tanlang"
                      }}
                    </span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Sarlavha -->
          <div class="col-12">
            <label class="form-label">Sarlavha *</label>
            <div class="input-wrapper">
              <input
                type="text"
                v-model="form.title"
                placeholder="Yangilik sarlavhasini kiriting"
                required
              />
            </div>
          </div>

          <!-- Kategoriya va Sana -->
          <div class="col-12 col-md-6">
            <label class="form-label">Kategoriya *</label>
            <div class="d-flex gap-2 align-items-start">
              <div class="input-wrapper flex-grow-1">
                <select v-model="form.category_id" required>
                  <option value="" disabled>Tanlang</option>
                  <option
                    v-for="cat in categories"
                    :key="cat.id"
                    :value="cat.id"
                  >
                    {{ cat.name }}
                  </option>
                </select>
              </div>
              <button
                type="button"
                class="btn-add-category flex-shrink-0"
                @click="showCategoryModal = true"
                title="Yangi kategoriya qo'shish"
              >
                <i class="fas fa-plus"></i>
              </button>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label">Sana</label>
            <div class="input-wrapper">
              <input type="date" v-model="form.date" />
            </div>
          </div>

          <!-- Qisqacha tavsif (Summary) -->
          <div class="col-12">
            <label class="form-label">Qisqacha tavsif (Anons)</label>
            <div class="input-wrapper">
              <textarea
                rows="2"
                v-model="form.summary"
                placeholder="Ro'yxatda ko'rinadigan qisqacha matn..."
              ></textarea>
            </div>
          </div>

          <!-- To'liq matn (Content) -->
          <div class="col-12">
            <label class="form-label">Batafsil matn / Mazmuni *</label>
            <div class="input-wrapper">
              <textarea
                rows="6"
                v-model="form.content"
                placeholder="Yangilikning to'liq matnini kiriting..."
                required
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top">
          <router-link
            to="/admin/news"
            class="btn-cancel-glass text-decoration-none"
          >
            Bekor qilish
          </router-link>

          <button type="submit" class="btn-save-glass" :disabled="saving">
            <span v-if="!saving">
              <i class="fas fa-check me-2"></i>
              {{ isEdit ? "Yangilash" : "Saqlash" }}
            </span>
            <span v-else>
              <span class="spinner-border spinner-border-sm me-2"></span>
              Saqlanmoqda...
            </span>
          </button>
        </div>
      </form>
    </div>

    <!-- Kategoriya qo'shish modali -->
    <div
      v-if="showCategoryModal"
      class="modal-overlay"
      @click.self="closeCategoryModal"
    >
      <div class="modal-box">
        <h5 class="fw-bold mb-3">Yangi kategoriya</h5>
        <div class="input-wrapper mb-3">
          <input
            type="text"
            v-model="newCategoryName"
            placeholder="Kategoriya nomi"
            @keyup.enter="createCategory"
          />
        </div>
        <p v-if="categoryError" class="text-danger small mb-3">
          {{ categoryError }}
        </p>
        <div class="d-flex justify-content-end gap-2">
          <button
            type="button"
            class="btn-cancel-glass"
            @click="closeCategoryModal"
          >
            Bekor qilish
          </button>
          <button
            type="button"
            class="btn-save-glass"
            :disabled="creatingCategory"
            @click="createCategory"
          >
            {{ creatingCategory ? "Saqlanmoqda..." : "Qo'shish" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "@/services/api";

const route = useRoute();
const router = useRouter();

const backendUrl = import.meta.env.VITE_STORAGE_URL || "";
const isEdit = computed(() => !!route.params.id);

const saving = ref(false);
const fileInput = ref(null);
const selectedFile = ref(null);
const imagePreview = ref(null);
const categories = ref([]);

// Kategoriya modal holati
const showCategoryModal = ref(false);
const newCategoryName = ref("");
const creatingCategory = ref(false);
const categoryError = ref("");

const form = reactive({
  title: "",
  category_id: "",
  date: new Date().toISOString().split("T")[0],
  summary: "",
  content: "",
});

const loadCategories = async () => {
  try {
    const res = await api.get("/admin/news-categories");
    categories.value = res.data?.data || res.data || [];
  } catch (err) {
    console.error("Kategoriyalarni yuklashda xatolik:", err);
  }
};

// Tahrirlash holatida ma'lumotlarni yuklab olish
onMounted(async () => {
  await loadCategories();

  if (isEdit.value) {
    try {
      const res = await api.get(`/admin/news/${route.params.id}`);
      const data = res.data?.data || res.data;

      form.title = data.title || "";
      form.category_id = data.category_id || "";
      form.summary = data.summary || "";
      form.content = data.content || data.body || "";
      form.date = data.published_at
        ? data.published_at.split("T")[0]
        : data.created_at
        ? data.created_at.split("T")[0]
        : form.date;

      if (data.image) {
        imagePreview.value = data.image.startsWith("http")
          ? data.image
          : `${backendUrl}${data.image.startsWith("/") ? "" : "/"}${
              data.image
            }`;
      }
    } catch (err) {
      alert("Yangilik ma'lumotlarini yuklashda xatolik yuz berdi.");
      router.push("/admin/news");
    }
  }
});

// Yangi kategoriya yaratish
const createCategory = async () => {
  const name = newCategoryName.value.trim();
  if (!name) {
    categoryError.value = "Kategoriya nomini kiriting.";
    return;
  }

  creatingCategory.value = true;
  categoryError.value = "";

  try {
    const res = await api.post("/admin/news-categories", { name });
    const newCategory = res.data?.data || res.data;

    categories.value.push(newCategory);
    form.category_id = newCategory.id; // yangisini avtomatik tanlaymiz

    closeCategoryModal();
  } catch (err) {
    categoryError.value =
      err.response?.data?.message ||
      err.response?.data?.errors?.name?.[0] ||
      "Kategoriya qo'shishda xatolik yuz berdi.";
  } finally {
    creatingCategory.value = false;
  }
};

const closeCategoryModal = () => {
  showCategoryModal.value = false;
  newCategoryName.value = "";
  categoryError.value = "";
};

// Fayl tanlanganda preview hosil qilish
const handleFileChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    selectedFile.value = file;
    imagePreview.value = URL.createObjectURL(file);
  }
};

const removeImage = () => {
  selectedFile.value = null;
  imagePreview.value = null;
  if (fileInput.value) fileInput.value.value = "";
};

// Formani saqlash
const handleSubmit = async () => {
  saving.value = true;

  try {
    const formData = new FormData();
    formData.append("title", form.title);
    formData.append("category_id", form.category_id);
    formData.append("summary", form.summary || "");
    formData.append("content", form.content);
    formData.append("is_published", 1); // Standart holatda chop etish

    if (form.date) {
      formData.append("published_at", form.date);
    }

    // FAQTAYgina yangi fayl tanlangan bo'lsagina image append qilinadi
    if (selectedFile.value instanceof File) {
      formData.append("image", selectedFile.value);
    }

    if (isEdit.value) {
      // Laravel Multipart (FormData) bilan PUT ishlashi uchun _method append qilinadi
      formData.append("_method", "PUT");
      await api.post(`/admin/news/${route.params.id}`, formData);
    } else {
      await api.post("/admin/news", formData);
    }

    router.push("/admin/news");
  } catch (err) {
    console.error("Save error:", err.response?.data);
    alert(err.response?.data?.message || "Saqlashda xatolik yuz berdi.");
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
.text-dark-slate {
  color: #0f172a;
}

.glass-panel {
  background: rgba(255, 255, 255, 0.45) !important;
  backdrop-filter: blur(16px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.6) !important;
  border-radius: 20px;
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
}

.btn-back-glass {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #0f172a;
  transition: all 0.2s ease;
}

.btn-back-glass:hover {
  background: #0f172a;
  color: white;
}

.premium-form label {
  display: block;
  font-weight: 700;
  font-size: 0.82rem;
  color: #475569;
  margin-bottom: 8px;
  text-transform: uppercase;
}

.input-wrapper input,
.input-wrapper select,
.input-wrapper textarea {
  width: 100%;
  padding: 12px 16px;
  background: rgba(255, 255, 255, 0.5) !important;
  border: 1px solid rgba(255, 255, 255, 0.7);
  border-radius: 12px;
  outline: none;
  color: #0f172a;
  font-weight: 600;
  transition: all 0.2s ease;
}

.input-wrapper input:focus,
.input-wrapper select:focus,
.input-wrapper textarea:focus {
  background: #ffffff !important;
  border-color: #0284c7;
  box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}

/* Image Upload UI */
.btn-upload-glass {
  border: 2px dashed rgba(2, 132, 199, 0.3);
  background: rgba(255, 255, 255, 0.3);
  border-radius: 14px;
  cursor: pointer;
  transition: all 0.25s ease;
}

.btn-upload-glass:hover {
  background: rgba(2, 132, 199, 0.05);
  border-color: #0284c7;
}

.preview-box {
  width: 120px;
  height: 90px;
  background: #cbd5e1;
}

.btn-remove-img {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  border: none;
  background: rgba(227, 30, 36, 0.9);
  color: white;
  font-size: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Kategoriya qo'shish tugmasi */
.btn-add-category {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  border: 1px solid rgba(2, 132, 199, 0.3);
  background: rgba(2, 132, 199, 0.08);
  color: #0284c7;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.btn-add-category:hover {
  background: #0284c7;
  color: white;
}

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
}

.modal-box {
  background: #ffffff;
  border-radius: 16px;
  padding: 28px;
  width: 100%;
  max-width: 380px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

/* Footer Buttons */
.btn-cancel-glass {
  padding: 12px 24px;
  border: 1px solid rgba(0, 0, 0, 0.08);
  background: rgba(0, 0, 0, 0.04);
  color: #475569;
  border-radius: 12px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
}

.btn-save-glass {
  padding: 12px 32px;
  border: none;
  background: #0284c7;
  color: white;
  border-radius: 12px;
  font-weight: 700;
  box-shadow: 0 4px 16px rgba(2, 132, 199, 0.25);
  transition: all 0.2s ease;
}

.btn-save-glass:hover {
  background: #0369a1;
  transform: translateY(-1px);
}
</style>