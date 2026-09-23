<template>
  <section
    class="doctors-section px-3 px-md-5 py-4 py-md-5 position-relative overflow-hidden"
  >
    <!-- Header -->
    <div class="position-relative z-2">
      <div class="d-flex align-items-center justify-content-between flex-wrap">
        <div>
          <h2 class="font-display fw-800 text-white mt-1 mb-0">
            Malakali Shifokorlarimiz
          </h2>
        </div>
        <router-link
          to="/doctors"
          class="text-decoration-none text-danger fs-5 fw-semibold"
        >
          Barchasini ko'rish <i class="bi bi-arrow-up-right ms-1"></i>
        </router-link>
      </div>
    </div>

    <!-- Swiper container -->
    <div class="container-fluid p-0 position-relative">
      <template v-if="displayDoctors.length">
        <Swiper
          @swiper="onSwiper"
          :modules="modules"
          effect="coverflow"
          :grabCursor="true"
          :centeredSlides="true"
          :slidesPerView="'auto'"
          :loop="true"
          :loopAdditionalSlides="5"
          :observer="true"
          :observeParents="true"
          :speed="800"
          :autoplay="{
            delay: 3000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
          }"
          :coverflowEffect="{
            rotate: 0,
            stretch: -60,
            depth: 200,
            modifier: 1.2,
            scale: 0.85,
            slideShadows: false,
          }"
          :navigation="{ nextEl: '.doc-next', prevEl: '.doc-prev' }"
          :pagination="{ el: '.doc-pagination', clickable: true }"
          class="doc-swiper"
        >
          <!-- Displaying Doctors -->
          <SwiperSlide
            v-for="(doc, index) in displayDoctors"
            :key="`${doc.id}-${index}`"
            class="doc-slide"
          >
            <div
              class="doc-card position-relative overflow-hidden rounded-5 shadow-lg h-100"
            >
              <img
                :src="
                  getPhotoUrl(doc.photo || doc.image, doc.full_name || doc.name)
                "
                :alt="doc.full_name || doc.name"
                class="img-cover"
              />
              <div class="doc-overlay"></div>
              <div
                class="position-absolute bottom-0 start-0 w-100 p-4 text-white"
                style="z-index: 2"
              >
                <span
                  class="badge bg-danger rounded-pill px-3 py-2 mb-2 d-inline-block"
                >
                  {{ doc.specialization?.name || doc.specialty || "—" }}
                </span>
                <h3 class="fw-bold mb-1 text-white fs-5">
                  {{ doc.full_name || doc.name }}
                </h3>
                <p class="opacity-75 small mb-3">
                  {{ doc.experience_years || doc.experience || 0 }} yillik
                  tajriba
                </p>

                <!-- DETAIL LINK -->
                <router-link
                  :to="{
                    name: 'doctor-detail',
                    params: { slug: doc.slug || doc.id },
                  }"
                  class="btn btn-light btn-sm rounded-pill px-4 fw-semibold"
                >
                  Batafsil
                </router-link>
              </div>
            </div>
          </SwiperSlide>
        </Swiper>

        <button class="doc-prev doc-nav-btn" aria-label="Oldingi">
          <i class="bi bi-chevron-left"></i>
        </button>
        <button class="doc-next doc-nav-btn" aria-label="Keyingi">
          <i class="bi bi-chevron-right"></i>
        </button>
        <div class="doc-pagination d-flex justify-content-center mt-4"></div>
      </template>

      <div
        v-else-if="!rawDoctors.length && !loading"
        style="height: 520px"
        class="text-center d-flex flex-column justify-content-center py-5 text-white"
      >
        <i class="bi bi-cloud-slash display-4 d-block mb-2 text-secondary"></i>
        <p class="mb-0">Hozircha shifokorlar kiritilmagan</p>
      </div>

      <!-- Loader -->
      <div
        v-else
        class="d-flex align-items-center justify-content-center"
        style="height: 520px"
      >
        <div
          class="spinner-border text-light"
          style="width: 3rem; height: 3rem"
          role="status"
        >
          <span class="visually-hidden">Yuklanmoqda...</span>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted, ref, watch, nextTick } from "vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import {
  EffectCoverflow,
  Autoplay,
  Navigation,
  Pagination,
} from "swiper/modules";
import "swiper/css";
import "swiper/css/effect-coverflow";
import "swiper/css/navigation";
import "swiper/css/pagination";

import { useDoctorsStore } from "../../../stores/doctors";

const modules = [EffectCoverflow, Autoplay, Navigation, Pagination];
const store = useDoctorsStore();
const backendUrl = import.meta.env.VITE_API_URL || "";

const swiperInstance = ref(null);
const loading = ref(false);

const onSwiper = (swiper) => {
  swiperInstance.value = swiper;
};

// Original shifokorlar ro'yxati
const rawDoctors = computed(() => {
  const featured = store.featured;
  const doctors = store.doctors;

  if (Array.isArray(featured) && featured.length) return featured;
  if (Array.isArray(doctors)) return doctors;
  return [];
});

// Slaydlar cheksiz va uzluksiz aylanishi uchun ro'yxatni yetarlicha ko'paytiramiz (kamida 15 ta)
const displayDoctors = computed(() => {
  const list = rawDoctors.value;
  if (!list.length) return [];

  let result = [...list];
  while (result.length < 15) {
    result = result.concat(list);
  }
  return result;
});

// Ma'lumotlar o'zgarganda yoki yuklanganda Swiper sliderini qayta yangilash
watch(
  displayDoctors,
  () => {
    nextTick(() => {
      if (swiperInstance.value) {
        swiperInstance.value.update();
        if (
          swiperInstance.value.autoplay &&
          !swiperInstance.value.autoplay.running
        ) {
          swiperInstance.value.autoplay.start();
        }
      }
    });
  },
  { deep: true }
);

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

onMounted(async () => {
  loading.value = true;
  if (store.fetchDoctors) {
    await store.fetchDoctors();
  }
  loading.value = false;
});
</script>

<style scoped>
.doctors-section {
  background: linear-gradient(
      135deg,
      rgba(0, 20, 80, 0.85) 0%,
      rgba(0, 10, 40, 0.9) 100%
    ),
    url("https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1600&q=60")
      center/cover no-repeat;
}

.doc-swiper {
  padding: 50px 0;
}

.doc-slide {
  width: 360px;
  height: 570px;
}

.doc-card {
  background: #111827;
}

.img-cover {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.swiper-slide-active .doc-card {
  box-shadow: 0 24px 48px rgba(220, 53, 69, 0.25) !important;
}

.doc-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.92), rgba(0, 0, 0, 0.1));
}

.doc-nav-btn {
  position: absolute;
  top: 45%;
  transform: translateY(-50%);
  z-index: 10;
  width: 54px;
  height: 54px;
  border: none;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  color: white;
  font-size: 1.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.25s ease;
  cursor: pointer;
}

.doc-prev {
  left: 20px;
}
.doc-next {
  right: 20px;
}

.doc-nav-btn:hover {
  background: #dc3545;
}

:deep(.doc-pagination .swiper-pagination-bullet) {
  width: 8px;
  height: 8px;
  background: rgba(255, 255, 255, 0.35);
  opacity: 1;
}

:deep(.doc-pagination .swiper-pagination-bullet-active) {
  width: 28px;
  border-radius: 4px;
  background: #dc3545 !important;
}

@media (max-width: 768px) {
  .doc-slide {
    width: 300px;
    height: 450px;
  }
  .doc-nav-btn {
    width: 44px;
    height: 44px;
    font-size: 1rem;
  }
  .font-display {
    font-size: 1rem !important;
  }
  .text-danger {
    font-size: 0.9rem !important;
  }
  .z-2 {
    padding: 0 5px!;
  }
}
</style>
