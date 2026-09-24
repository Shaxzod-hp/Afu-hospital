<template>
  <div class="doctors-page min-vh-100 pb-5">
    <!-- HERO SECTION -->
    <section class="page-hero">
      <video
        ref="heroVideo"
        class="page-hero-video"
        autoplay
        muted
        loop
        playsinline
        preload="auto"
      >
        <source src="/bg-videoo.mp4" type="video/mp4" />
      </video>

      <div class="hero-overlay"></div>

      <div class="container position-relative hero-content">
        <span class="hero-subtitle">Bizning Jamoadoshlar</span>
        <h1 class="hero-title">Malakali Shifokorlarimiz</h1>
        <p class="hero-desc" style="max-width: 700px; margin: 0 auto">
          Sizning salomatligingiz uchun eng yaxshi mutaxassislar birlashgan.
          Qabulga onlayn yoziling.
        </p>
      </div>
    </section>

    <!-- MAIN CONTENT AREA -->
    <div class="container-fluid px-lg-5 px-3 doctors-content-wrap">
      <!-- Search Bar -->
      <div class="row mb-4">
        <div class="col-12">
          <div
            class="search-box bg-white p-2 rounded-pill shadow-sm d-flex align-items-center"
          >
            <input
              v-model="searchQuery"
              type="text"
              class="form-control border-0 shadow-none ps-3 ps-md-4 bg-transparent fs-6"
              placeholder="Shifokor ismi yoki mutaxassisligi bo'yicha..."
            />
            <button
              class="btn btn-akfa-red rounded-pill px-3 px-md-4 py-2 d-flex align-items-center gap-2 fw-semibold text-nowrap"
            >
              <i class="fas fa-search"></i>
              <span class="d-none d-sm-inline">Qidirish</span>
            </button>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <!-- Sidebar Filter -->
        <aside class="col-12 col-lg-3 position-sticky top-0">
          <div
            class="filter-card bg-white p-4 rounded-4 shadow-sm border-0 sticky-sidebar"
          >
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h6 class="fw-bold text-dark m-0">Mutaxassisliklar</h6>
              <button
                v-if="selectedSpecialties.length"
                @click="clearFilters"
                class="btn btn-link p-0 text-danger text-decoration-none extra-small fw-bold"
              >
                Tozalash
              </button>
            </div>

            <div class="specialities-scroll d-flex flex-column gap-2 pe-2">
              <div
                v-for="(spec, index) in specialties"
                :key="spec.slug || index"
                class="form-check custom-checkbox"
              >
                <input
                  class="form-check-input"
                  type="checkbox"
                  :id="'spec-' + index"
                  :value="spec.slug"
                  v-model="selectedSpecialties"
                />
                <label
                  class="form-check-label text-secondary small cursor-pointer"
                  :for="'spec-' + index"
                >
                  {{ spec.name }}
                </label>
              </div>
            </div>
          </div>
        </aside>

        <!-- Doctors List -->
        <main class="col-12 col-lg-9">
          <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-danger" role="status">
              <span class="visually-hidden">Yuklanmoqda...</span>
            </div>
          </div>

          <!-- Shifokorlar Kartalari -->
          <div v-else-if="filteredDoctors.length" class="row g-4">
            <div
              v-for="doc in filteredDoctors"
              :key="doc.id"
              class="col-12 col-sm-6 col-xl-4"
            >
              <router-link
                :to="'/doctors/' + doc.slug"
                class="text-decoration-none"
              >
                <div class="doctor-vertical-card">
                  <img
                    :src="getPhotoUrl(doc.photo, doc.full_name || doc.name)"
                    :alt="doc.full_name || doc.name"
                    class="doc-card-image"
                  />

                  <div class="doc-card-info-box">
                    <h6 class="doc-name fw-bold mb-1">
                      {{ doc.full_name || doc.name }}
                    </h6>
                    <p class="doc-specialty text-muted mb-0">
                      {{
                        doc.specialization?.name ||
                        doc.specialty ||
                        "Mutaxassis"
                      }}
                    </p>
                  </div>
                </div>
              </router-link>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-5 bg-white rounded-4 shadow-sm">
            <i class="bi bi-person-x display-4 text-muted"></i>
            <h5 class="fw-bold text-dark mt-3 mb-1">Shifokor topilmadi</h5>
            <p class="text-muted small">
              Qidiruv yoki filtr mezonlarini o'zgartirib ko'ring.
            </p>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useDoctorsStore } from "../../../stores/doctors";

const route = useRoute();
const router = useRouter();
const doctorsStore = useDoctorsStore();

const heroVideo = ref(null);
const searchQuery = ref("");
const selectedSpecialties = ref([]);

const loading = computed(() => doctorsStore.loading);

// 1. Mutaxassisliklarni FAQT BAZADAN olish va unikal ro'yxat tuzish
const specialties = computed(() => {
  // Agar store da tayyor store.specialties bo'lsa
  if (doctorsStore.specialties && doctorsStore.specialties.length > 0) {
    return doctorsStore.specialties.map((s) =>
      typeof s === "string" ? { name: s, slug: slugify(s) } : s
    );
  }

  // Aks holda bazadagi shifokorlar (doctors) obyektidan ajratib olish
  const docs = doctorsStore.doctors || [];
  const map = new Map();

  docs.forEach((doc) => {
    const specObj = doc.specialization;
    if (specObj && specObj.name) {
      const slug = specObj.slug || slugify(specObj.name);
      if (!map.has(slug)) {
        map.set(slug, { name: specObj.name, slug: slug });
      }
    } else if (doc.specialty) {
      const slug = doc.specialty_slug || slugify(doc.specialty);
      if (!map.has(slug)) {
        map.set(slug, { name: doc.specialty, slug: slug });
      }
    }
  });

  return Array.from(map.values());
});

// Stringdan mos slug hosil qiluvchi yordamchi funksiya
function slugify(text) {
  if (!text) return "";
  return text
    .toString()
    .toLowerCase()
    .trim()
    .replace(/\s+/g, "-")
    .replace(/[^\w\-]+/g, "")
    .replace(/\-\-+/g, "-");
}

// 2. URL parametri (?specialization=...) bo'yicha checkbox galochkasini sinxronlash
const syncFromQuery = () => {
  const querySpec = route.query.specialization;
  if (!querySpec) return;

  const targetSlug = querySpec.toLowerCase().trim();

  // Bazadagi mutaxassisliklar ichidan mosini izlash (slug yoki name bo'yicha)
  const matched = specialties.value.find(
    (s) =>
      s.slug?.toLowerCase() === targetSlug ||
      s.name?.toLowerCase().includes(targetSlug) ||
      targetSlug.includes(s.slug?.toLowerCase())
  );

  if (matched) {
    selectedSpecialties.value = [matched.slug];
  } else {
    selectedSpecialties.value = [targetSlug];
  }
};

// URL dagi ?specialization parametr o'zgarganda watch qilish
watch(
  () => route.query.specialization,
  () => {
    syncFromQuery();
  },
  { immediate: true }
);

// Mutaxassisliklar bazadan yuklangandan so'ng qayta tekshirish
watch(specialties, () => {
  syncFromQuery();
});

const clearFilters = () => {
  selectedSpecialties.value = [];
  if (route.query.specialization) {
    router.replace({ query: {} });
  }
};

const backendUrl = import.meta.env.VITE_STORAGE_URL || "";

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

// 3. Asosiy Filtrlash Mantiqi
const filteredDoctors = computed(() => {
  const list = doctorsStore.doctors || [];

  return list.filter((doc) => {
    const docName = (doc.full_name || doc.name || "").toLowerCase();
    const docSpecName = (
      doc.specialization?.name ||
      doc.specialty ||
      ""
    ).toLowerCase();
    const docSpecSlug = (
      doc.specialization?.slug ||
      doc.specialty_slug ||
      slugify(docSpecName)
    ).toLowerCase();

    // A) Qidiruv Inputi bo'yicha
    const query = searchQuery.value.toLowerCase().trim();
    const matchesSearch =
      !query || docName.includes(query) || docSpecName.includes(query);

    // B) Sidebar Checkbox'lari bo'yicha
    const matchesSpec =
      selectedSpecialties.value.length === 0 ||
      selectedSpecialties.value.some((selected) => {
        const sel = selected.toLowerCase();
        return (
          docSpecSlug === sel ||
          docSpecName.includes(sel) ||
          sel.includes(docSpecSlug)
        );
      });

    return matchesSearch && matchesSpec;
  });
});

onMounted(async () => {
  window.scrollTo({ top: 0, behavior: "smooth" });

  if (doctorsStore.fetchDoctors) {
    await doctorsStore.fetchDoctors();
  }

  syncFromQuery();

  if (heroVideo.value) {
    heroVideo.value.play().catch(() => {});
  }
});
</script>

<style scoped>
.page-hero {
  position: relative;
  width: 100%;
  min-height: 450px;
  padding: 130px 0 100px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  text-align: center;
  overflow: hidden;
  background-color: #002b87;
}

.page-hero-video {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  z-index: 1;
  pointer-events: none;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    135deg,
    rgba(0, 43, 135, 0.75) 0%,
    rgba(0, 20, 70, 0.8) 100%
  );
  z-index: 2;
  pointer-events: none;
}

.hero-content {
  z-index: 3;
}

.hero-subtitle {
  font-size: 0.8rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 3px;
  color: rgba(255, 255, 255, 0.85);
  display: inline-block;
  margin-bottom: 12px;
}

.hero-title {
  font-size: clamp(2rem, 5vw, 3rem);
  font-weight: 800;
  font-family: "Outfit", sans-serif;
  color: #ffffff;
  margin-bottom: 12px;
}

.hero-desc {
  color: rgba(255, 255, 255, 0.9);
  font-size: 1rem;
}

.doctors-content-wrap {
  margin-top: -65px;
  position: relative;
  z-index: 4;
}

[data-theme="dark"] .page-hero {
  background-color: #0f172a;
}

.extra-small {
  font-size: 0.75rem;
}

.btn-akfa-red {
  background-color: #e31e24;
  color: #ffffff;
  border: none;
  transition: background-color 0.2s ease;
}

.btn-akfa-red:hover {
  background-color: #e31e24;
  color: #ffffff;
}

.specialities-scroll {
  max-height: 320px;
  overflow-y: auto;
}

.custom-checkbox .form-check-input:checked {
  background-color: #e31e24;
  border-color: #e31e24;
}

.doctor-vertical-card {
  position: relative;
  height: 420px;
  border-radius: 28px;
  overflow: hidden;
  background-color: #f3f5f8;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1),
    box-shadow 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
  cursor: pointer;
}

.doctor-vertical-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 18px 35px rgba(0, 0, 0, 0.12);
}

.doc-card-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: top center;
  display: block;
}

.doc-card-info-box {
  position: absolute;
  bottom: 12px;
  left: 12px;
  right: 12px;
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 16px 20px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
  transition: background-color 0.25s ease;
}

.doctor-vertical-card:hover .doc-card-info-box {
  background: #ffffff;
}

.doc-name {
  color: #1e293b;
  font-size: 1.05rem;
  letter-spacing: -0.2px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.doc-specialty {
  font-size: 0.85rem;
  font-weight: 500;
  color: #64748b !important;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

@media (max-width: 991px) {
  .page-hero {
    min-height: 400px;
    padding: 110px 0 70px;
  }
  .doctors-content-wrap {
    margin-top: -20px;
  }
}

@media (max-width: 768px) {
  .page-hero {
    min-height: 350px;
    padding: 100px 15px 60px;
  }
  .doctors-content-wrap {
    margin-top: -15px;
  }
  .position-sticky {
    display: none;
  }
}

@media (max-width: 576px) {
  .page-hero {
    min-height: 320px;
    padding: 90px 12px 50px;
  }
  .hero-subtitle {
    font-size: 0.7rem;
    letter-spacing: 2px;
  }
  .hero-title {
    font-size: 1.8rem;
  }
  .hero-desc {
    font-size: 0.9rem;
  }
  .doctor-vertical-card {
    height: 400px;
    border-radius: 22px;
  }
  .doc-card-info-box {
    bottom: 10px;
    left: 10px;
    right: 10px;
    padding: 12px 16px;
    border-radius: 16px;
  }
}
</style>
