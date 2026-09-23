<template>
  <div class="doctor-detail-page min-vh-100">
    <!-- HERO SECTION -->
    <section class="page-hero">
      <video
        class="page-hero-video"
        autoplay
        muted
        loop
        playsinline
        preload="auto"
      >
        <source src="/bg-videoo.mp4" type="video/mp4" />
      </video>

      <div class="page-hero-overlay"></div>

      <div class="container position-relative z-2">
        <span class="hero-subtitle">Bizning Mutaxassis</span>
        <h1 class="hero-title">
          {{ doctorName }}
        </h1>
        <p class="hero-desc" v-if="doctor">
          {{ doctorSpecialty }}
          <template v-if="doctorExperience">
            &bull; {{ doctorExperience }} yil tajriba</template
          >
        </p>
      </div>
    </section>

    <!-- CONTENT -->
    <div class="detail-content-wrap container py-5">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-danger" role="status">
          <span class="visually-hidden">Yuklanmoqda...</span>
        </div>
      </div>

      <!-- Doctor Profile Content -->
      <div v-else-if="doctor" class="row g-4 text-start align-items-start">
        <!-- 1. MOBIL UCHUN RASM -->
        <div class="col-12 d-lg-none">
          <div
            class="card main-profile-card border-0 shadow-sm rounded-4 overflow-hidden mb-4"
          >
            <div class="doctor-image-wrapper">
              <img :src="doctorPhoto" :alt="doctorName" class="doctor-image" />
            </div>
          </div>
        </div>

        <!-- LEFT COLUMN: PHOTO & QUICK INFO CARD (Desktop) -->
        <div class="col-lg-4 d-none d-lg-block">
          <div
            class="card main-profile-card border-0 shadow-sm rounded-4 overflow-hidden position-sticky"
            style="top: 20px"
          >
            <div class="doctor-image-wrapper">
              <img :src="doctorPhoto" :alt="doctorName" class="doctor-image" />
            </div>

            <div class="card-body p-4 text-center">
              <h3 class="fw-bold mb-1 text-dark fs-4">
                {{ doctorName }}
              </h3>

              <p class="text-danger fw-semibold mb-3">
                {{ doctorSpecialty }}
              </p>

              <!-- STATS & POSITION -->
              <div
                class="row g-0 py-3 border-top border-bottom mb-4 text-center bg-body-tertiary rounded-3"
              >
                <div class="col-6 border-end px-2">
                  <div class="h5 fw-bold text-dark mb-0">
                    {{ doctorExperience }}
                  </div>
                  <div class="extra-small text-muted">Tajriba (yil)</div>
                </div>

                <div class="col-6 px-2">
                  <div
                    class="h6 fw-bold text-dark mb-0 text-truncate"
                    :title="doctor.position || 'Shifokor'"
                  >
                    {{ doctor.position || "Shifokor" }}
                  </div>
                  <div class="extra-small text-muted">Lavozimi</div>
                </div>
              </div>

              <!-- SOCIAL & CONTACT LINKS -->
              <div
                v-if="hasSocials"
                class="d-flex justify-content-center gap-2 mb-0"
              >
                <a
                  v-if="doctor.phone"
                  :href="'tel:' + doctor.phone"
                  class="social-icon-btn phone"
                  title="Telefon"
                >
                  <i class="bi bi-telephone-fill"></i>
                </a>
                <a
                  v-if="doctor.telegram"
                  :href="formatSocialLink(doctor.telegram, 'telegram')"
                  target="_blank"
                  class="social-icon-btn telegram"
                  title="Telegram"
                >
                  <i class="bi bi-telegram"></i>
                </a>
                <a
                  v-if="doctor.instagram"
                  :href="formatSocialLink(doctor.instagram, 'instagram')"
                  target="_blank"
                  class="social-icon-btn instagram"
                  title="Instagram"
                >
                  <i class="bi bi-instagram"></i>
                </a>
                <a
                  v-if="doctor.whatsapp"
                  :href="formatSocialLink(doctor.whatsapp, 'whatsapp')"
                  target="_blank"
                  class="social-icon-btn whatsapp"
                  title="WhatsApp"
                >
                  <i class="bi bi-whatsapp"></i>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- RIGHT COLUMN: DETAILED INFO OR SCHEDULE BASED ON TAB -->
        <div class="col-lg-8">
          <div class="col-12 mb-2">
            <div
              class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start gap-3"
            >
              <button
                @click="activeTab = 'info'"
                class="btn tab-btn px-4 py-3 rounded-pill fw-semibold d-flex align-items-center gap-2 shadow-sm"
                :class="
                  activeTab === 'info'
                    ? 'btn-akfa-red text-white'
                    : 'btn-white text-dark bg-white'
                "
              >
                <i class="bi bi-person-badge"></i>
                <span>Shifokor ma'lumotlari</span>
              </button>

              <button
                @click="activeTab = 'schedule'"
                class="btn tab-btn px-4 py-3 rounded-pill fw-semibold d-flex align-items-center gap-2 shadow-sm"
                :class="
                  activeTab === 'schedule'
                    ? 'btn-akfa-red text-white'
                    : 'btn-white text-dark bg-white'
                "
              >
                <i class="bi bi-clock-history"></i>
                <span>Ish vaqti grafigi</span>
              </button>
            </div>
          </div>

          <!-- TAB 1: SHIFOKOR HAQIDA MA'LUMOTLAR -->
          <div v-if="activeTab === 'info'">
            <!-- BIO CARD -->
            <div
              class="card detail-info-card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4"
            >
              <div class="card-section-title mb-3">
                <i class="bi bi-person-badge-fill text-danger me-2 fs-5"></i>
                <h4 class="fw-bold text-dark m-0">Mutaxassis haqida</h4>
              </div>

              <p class="text-secondary fs-6 lh-lg mb-0">
                {{
                  doctor.bio ||
                  `${doctorName} o'z sohasining yuqori malakali mutaxassisi bo'lib, bemorlarga zamonaviy va samarali davolash usullarini taklif etadi.`
                }}
              </p>
            </div>

            <!-- EDUCATION & PREVIOUS WORK -->
            <div class="row g-4 mb-4">
              <!-- EDUCATION -->
              <div class="col-md-6">
                <div
                  class="card detail-info-card border-0 shadow-sm rounded-4 p-4 h-100"
                >
                  <div class="card-section-title mb-3">
                    <i class="bi bi-mortarboard-fill text-danger me-2 fs-5"></i>
                    <h5 class="fw-bold text-dark m-0">Ma'lumoti</h5>
                  </div>

                  <div
                    v-if="doctor.education"
                    class="text-secondary small-text lh-base"
                  >
                    <div class="d-flex align-items-start gap-2">
                      <i class="bi bi-check-circle-fill text-danger mt-1"></i>
                      <span>{{ doctor.education }}</span>
                    </div>
                  </div>
                  <div v-else class="text-muted small">
                    Ma'lumot ko'rsatilmadi.
                  </div>
                </div>
              </div>

              <!-- PREVIOUS WORKPLACE -->
              <div class="col-md-6">
                <div
                  class="card detail-info-card border-0 shadow-sm rounded-4 p-4 h-100"
                >
                  <div class="card-section-title mb-3">
                    <i class="bi bi-briefcase-fill text-danger me-2 fs-5"></i>
                    <h5 class="fw-bold text-dark m-0">Tajriba & Ish Joylari</h5>
                  </div>

                  <div
                    v-if="doctor.previous_workplace"
                    class="text-secondary small-text lh-base"
                  >
                    <div class="d-flex align-items-start gap-2">
                      <i class="bi bi-building-check text-danger mt-1"></i>
                      <span>{{ doctor.previous_workplace }}</span>
                    </div>
                  </div>
                  <div v-else class="text-muted small">
                    Tajriba ma'lumotlari kiritilmagan.
                  </div>
                </div>
              </div>
            </div>

            <!-- SERVICES -->
            <div
              class="card detail-info-card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4"
            >
              <div class="card-section-title mb-3">
                <i class="bi bi-shield-plus text-danger me-2 fs-5"></i>
                <h4 class="fw-bold text-dark m-0">Xizmatlar</h4>
              </div>

              <ul
                v-if="servicesList.length"
                class="list-unstyled mb-0 d-flex flex-column gap-2"
              >
                <li
                  v-for="srv in servicesList"
                  :key="srv"
                  class="d-flex align-items-center gap-2 text-secondary"
                >
                  <i class="bi bi-patch-check-fill text-danger"></i>
                  <span>{{ srv }}</span>
                </li>
              </ul>
              <div v-else class="text-muted small">
                Konsultatsiya va umumiy ko'rik
              </div>
            </div>

            <!-- ISH JARAYONIDAN LAVHALAR -->
            <div
              v-if="treatmentLogs.length"
              class="card detail-info-card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4"
            >
              <div class="card-section-title mb-3">
                <i class="bi bi-camera-fill text-danger me-2 fs-5"></i>
                <h4 class="fw-bold text-dark m-0">Ish jarayonidan lavhalar</h4>
              </div>

              <div class="row g-3">
                <div
                  v-for="log in treatmentLogs"
                  :key="log.id"
                  class="col-12 col-sm-6"
                >
                  <div class="treatment-log-card h-100">
                    <div
                      class="log-slideshow"
                      @mouseenter="pauseLogSlide(log.id)"
                      @mouseleave="resumeLogSlide(log.id)"
                    >
                      <img
                        v-for="(photo, i) in log.photos"
                        :key="i"
                        :src="resolveImage(photo)"
                        :alt="log.description"
                        class="log-slide-img"
                        :class="{ active: (logSlideIndex[log.id] || 0) === i }"
                      />
                      <div v-if="log.photos.length > 1" class="log-slide-dots">
                        <span
                          v-for="(photo, i) in log.photos"
                          :key="i"
                          class="log-dot"
                          :class="{
                            active: (logSlideIndex[log.id] || 0) === i,
                          }"
                        ></span>
                      </div>
                    </div>

                    <div class="log-body">
                      <p class="text-secondary small mb-1">
                        {{ log.description }}
                      </p>
                      <span class="text-muted extra-small">{{
                        formatLogDate(log.created_at)
                      }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- TAB 2: ISH VAQTI GRAFIGI -->
          <div v-else-if="activeTab === 'schedule'">
            <div
              class="card detail-info-card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4"
            >
              <div class="card-section-title mb-4">
                <i class="bi bi-clock-history text-danger me-2 fs-5"></i>
                <h4 class="fw-bold text-dark m-0">Ish vaqti grafigi</h4>
              </div>

              <div class="table-responsive">
                <table class="table align-middle mb-0">
                  <thead>
                    <tr class="text-muted border-bottom">
                      <th scope="col" class="pb-3 fw-semibold">
                        Hafta kunlari
                      </th>
                      <th scope="col" class="pb-3 fw-semibold text-center">
                        Vaqti (dan)
                      </th>
                      <th scope="col" class="pb-3 fw-semibold text-center">
                        Vaqti (gacha)
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="day in weekDays"
                      :key="day.key"
                      class="border-bottom"
                    >
                      <td class="py-3 fw-semibold text-dark">
                        {{ day.name }}
                      </td>
                      <td class="py-3 text-center">
                        <span
                          v-if="getScheduleTime(day.key).start"
                          class="text-dark fw-bold"
                        >
                          {{ getScheduleTime(day.key).start }}
                        </span>
                        <span v-else class="text-danger fs-5">
                          <i class="bi bi-x-circle"></i>
                        </span>
                      </td>
                      <td class="py-3 text-center">
                        <span
                          v-if="getScheduleTime(day.key).end"
                          class="text-dark fw-bold"
                        >
                          {{ getScheduleTime(day.key).end }}
                        </span>
                        <span v-else class="text-danger fs-5">
                          <i class="bi bi-x-circle"></i>
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- ALL DOCTORS BUTTON -->
          <router-link
            to="/doctors"
            class="doctors btn btn-primary py-2 w-100 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2"
          >
            <i class="bi bi-arrow-left"></i>
            <span>Barcha shifokorlar ro'yxatiga qaytish</span>
          </router-link>
        </div>
      </div>

      <!-- NOT FOUND -->
      <div v-else class="text-center py-5 bg-white rounded-4 shadow-sm my-5">
        <i class="bi bi-person-exclamation display-3 text-muted"></i>
        <h4 class="fw-bold text-dark mt-3">Shifokor topilmadi</h4>
        <p class="text-muted small">
          Bunday shifokor mavjud emas yoki o'chirilgan bo'lishi mumkin.
        </p>
        <router-link
          to="/doctors"
          class="btn btn-akfa-red mt-2 rounded-pill px-4 py-2 fw-semibold"
        >
          Shifokorlar ro'yxatiga qaytish
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  ref,
  reactive,
  computed,
  onMounted,
  onBeforeUnmount,
  watch,
  nextTick,
} from "vue";
import { useRoute } from "vue-router";
import { useDoctorsStore } from "../../../stores/doctors";
import doctorsService from "../../../services/doctorsService";

const route = useRoute();
const doctorsStore = useDoctorsStore();

const doctor = ref(null);
const loading = ref(true);
const treatmentLogs = ref([]);
const activeTab = ref("info");

const backendUrl = import.meta.env.VITE_STORAGE_URL || "";

const weekDays = [
  { key: "monday", name: "Dushanba" },
  { key: "tuesday", name: "Seshanba" },
  { key: "wednesday", name: "Chorshanba" },
  { key: "thursday", name: "Payshanba" },
  { key: "friday", name: "Juma" },
  { key: "saturday", name: "Shanba" },
  { key: "sunday", name: "Yakshanba" },
];

// ===== ISH VAQTI GRAFIGI =====
const DAY_ALIASES = {
  monday: ["monday", "mon", "dushanba", "1"],
  tuesday: ["tuesday", "tue", "seshanba", "2"],
  wednesday: ["wednesday", "wed", "chorshanba", "3"],
  thursday: ["thursday", "thu", "payshanba", "4"],
  friday: ["friday", "fri", "juma", "5"],
  saturday: ["saturday", "sat", "shanba", "6"],
  sunday: ["sunday", "sun", "yakshanba", "7", "0"],
};

const EMPTY_TIME = { start: null, end: null };

const fmtTime = (t) => (t ? String(t).trim().slice(0, 5) : null);

const parseRange = (str) => {
  const parts = String(str).split(/\s*[-–—]\s*/);
  if (parts.length !== 2) return EMPTY_TIME;
  return { start: fmtTime(parts[0]), end: fmtTime(parts[1]) };
};

const getScheduleTime = (dayKey) => {
  const list = doctor.value?.schedules;

  if (Array.isArray(list) && list.length) {
    const aliases = DAY_ALIASES[dayKey];
    const item = list.find(
      (s) =>
        aliases.includes(String(s.day).toLowerCase()) &&
        s.is_available !== false &&
        s.is_available !== 0
    );
    if (!item) return EMPTY_TIME;
    return { start: fmtTime(item.start_time), end: fmtTime(item.end_time) };
  }

  if (doctor.value?.working_hours && dayKey !== "sunday") {
    return parseRange(doctor.value.working_hours);
  }
  return EMPTY_TIME;
};
// ===== /ISH VAQTI GRAFIGI =====

const doctorName = computed(() => {
  if (!doctor.value) return "";
  return doctor.value.full_name || doctor.value.name || "Shifokor profili";
});

const doctorSpecialty = computed(() => {
  if (!doctor.value) return "";
  return (
    doctor.value.specialization?.name || doctor.value.specialty || "Mutaxassis"
  );
});

const doctorExperience = computed(() => {
  if (!doctor.value) return 0;
  return doctor.value.experience_years ?? doctor.value.experience ?? 0;
});

const doctorPhoto = computed(() => {
  if (!doctor.value) return "";
  const photo = doctor.value.photo || doctor.value.image;
  if (!photo) {
    return (
      "https://ui-avatars.com/api/?name=" +
      encodeURIComponent(doctorName.value) +
      "&background=0284c7&color=fff"
    );
  }
  return photo.startsWith("http") ? photo : backendUrl + photo;
});

const resolveImage = (path) => {
  if (!path) return "";
  return path.startsWith("http") ? path : backendUrl + path;
};

const hasSocials = computed(() => {
  if (!doctor.value) return false;
  return (
    doctor.value.phone ||
    doctor.value.telegram ||
    doctor.value.instagram ||
    doctor.value.whatsapp
  );
});

const servicesList = computed(() => {
  if (!doctor.value) return [];
  if (Array.isArray(doctor.value.services)) return doctor.value.services;
  if (typeof doctor.value.services === "string") {
    return doctor.value.services.split(",").map((s) => s.trim());
  }
  return [];
});

const formatSocialLink = (val, type) => {
  if (!val) return "#";
  if (val.startsWith("http://") || val.startsWith("https://")) return val;
  const cleanVal = val.replace("@", "");
  if (type === "telegram") return `https://t.me/${cleanVal}`;
  if (type === "instagram") return `https://instagram.com/${cleanVal}`;
  if (type === "whatsapp") return `https://wa.me/${cleanVal}`;
  return val;
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

const logSlideIndex = reactive({});
const logSlideTimers = {};
const logPausedIds = reactive({});

const startLogSlideTimer = (log) => {
  const photos = Array.isArray(log.photos) ? log.photos : [];
  if (photos.length <= 1) return;
  logSlideIndex[log.id] = logSlideIndex[log.id] || 0;
  logSlideTimers[log.id] = setInterval(() => {
    if (logPausedIds[log.id]) return;
    logSlideIndex[log.id] = ((logSlideIndex[log.id] || 0) + 1) % photos.length;
  }, 2500);
};

const pauseLogSlide = (id) => {
  logPausedIds[id] = true;
};
const resumeLogSlide = (id) => {
  logPausedIds[id] = false;
};

const clearAllLogTimers = () => {
  Object.values(logSlideTimers).forEach((t) => clearInterval(t));
  Object.keys(logSlideTimers).forEach((k) => delete logSlideTimers[k]);
};

const loadDoctor = async () => {
  loading.value = true;
  doctor.value = null;
  clearAllLogTimers();
  treatmentLogs.value = [];

  const idOrSlug = route.params.id || route.params.slug;

  if (!idOrSlug || idOrSlug === "undefined") {
    loading.value = false;
    return;
  }

  try {
    let found = doctorsStore.getById ? doctorsStore.getById(idOrSlug) : null;

    if (!found || !found.schedules) {
      const res = await doctorsService.fetchOne(idOrSlug);
      found = res && res.data && !res.full_name ? res.data : res;
    }

    doctor.value = found ? { ...found } : null;
  } catch (e) {
    console.error(
      "fetchOne xatosi:",
      e?.response?.status,
      e?.response?.data || e
    );
    doctor.value = null;
  }

  if (doctor.value) {
    try {
      const slugOrId = doctor.value.slug || doctor.value.id || idOrSlug;
      const logs = await doctorsService.fetchLogs(slugOrId);
      treatmentLogs.value = Array.isArray(logs) ? logs : [];

      treatmentLogs.value.forEach((log) => {
        if (log.photos && log.photos.length > 1) {
          startLogSlideTimer(log);
        }
      });
    } catch (e) {
      console.error(
        "fetchLogs xatosi:",
        e?.response?.status,
        e?.response?.data || e
      );
      treatmentLogs.value = [];
    }
  }

  await nextTick();
  loading.value = false;
};

onMounted(() => {
  window.scrollTo({ top: 0, behavior: "smooth" });
  loadDoctor();
});

onBeforeUnmount(clearAllLogTimers);

watch(
  () => [route.params.id, route.params.slug],
  () => {
    loadDoctor();
  }
);
</script>

<style scoped>
.doctor-detail-page {
  width: 100%;
  min-height: 100vh;
}

.detail-content-wrap {
  position: relative;
  z-index: 4;
}

.page-hero {
  position: relative;
  width: 100%;
  min-height: 380px;
  padding: 120px 0 80px;
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
  z-index: 1;
  pointer-events: none;
}

.page-hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    135deg,
    rgba(0, 43, 135, 0.75) 0%,
    rgba(0, 20, 70, 0.85) 100%
  );
  z-index: 2;
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
  font-size: clamp(2rem, 4vw, 2.8rem);
  font-weight: 800;
  font-family: "Outfit", sans-serif;
  color: white;
  margin-bottom: 10px;
}

.hero-desc {
  color: rgba(255, 255, 255, 0.9);
  font-size: 1.05rem;
}

.main-profile-card,
.detail-info-card {
  background-color: #ffffff;
  border-radius: 24px !important;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04) !important;
}

.doctor-image-wrapper {
  position: relative;
  width: 100%;
  height: 380px;
  overflow: hidden;
  background-color: #f3f5f8;
}

.doctor-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: top center;
}

.card-section-title {
  display: flex;
  align-items: center;
}

.extra-small {
  font-size: 0.75rem;
}

.small-text {
  font-size: 0.92rem;
}

.social-icon-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  text-decoration: none;
  font-size: 1.1rem;
  transition: transform 0.2s ease, filter 0.2s ease;
}

.social-icon-btn:hover {
  transform: translateY(-3px);
  color: #fff;
  filter: brightness(1.1);
}

.social-icon-btn.phone {
  background-color: #25d366;
}
.social-icon-btn.telegram {
  background-color: #0088cc;
}
.social-icon-btn.instagram {
  background: linear-gradient(
    45deg,
    #f09433,
    #e6683c,
    #dc2743,
    #cc2366,
    #bc1888
  );
}
.social-icon-btn.whatsapp {
  background-color: #25d366;
}

.btn-akfa-red {
  background-color: #e53935;
  color: #ffffff;
  border: none;
  transition: all 0.25s ease;
}

.btn-akfa-red:hover {
  background-color: #d32f2f;
  color: #ffffff;
}

.treatment-log-card {
  border: 1px solid rgba(0, 0, 0, 0.06);
  border-radius: 16px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  background: #fff;
}

.log-slideshow {
  position: relative;
  width: 100%;
  height: 220px;
  background: #f1f5f9;
  overflow: hidden;
}

.log-slide-img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: 0;
  transition: opacity 0.6s ease;
}

.log-slide-img.active {
  opacity: 1;
}

.log-slide-dots {
  position: absolute;
  bottom: 10px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 6px;
  z-index: 2;
}

.log-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.5);
  transition: all 0.2s ease;
}

.log-dot.active {
  background: #ffffff;
  transform: scale(1.3);
}

.log-body {
  padding: 14px 16px;
}

@media (max-width: 991px) {
  .page-hero {
    min-height: 320px;
    padding: 100px 0 60px;
  }
}

@media (max-width: 576px) {
  .page-hero {
    min-height: 280px;
    padding: 80px 12px 40px;
  }
  .hero-title {
    font-size: 1.75rem;
  }
  .log-slideshow {
    height: 180px;
  }
  .doctors {
    padding: 2px !important;
  }
}
</style>
