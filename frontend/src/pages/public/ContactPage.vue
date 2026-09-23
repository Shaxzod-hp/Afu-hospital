<template>
  <div class="min-vh-100 pb-5 contact-page-root">
    <!-- Premium Header -->
    <section class="page-hero">
      <!-- Background Video -->
      <video class="page-hero-video" autoplay muted loop playsinline>
        <source src="/bg-videoo.mp4" type="video/mp4" />
      </video>

      <!-- Dark Overlay -->
      <div class="page-hero-overlay"></div>

      <!-- Content -->
      <div class="container position-relative z-2 text-center">
        <h1 class="animate-fadeUp">Murojaat Qiling</h1>

        <p class="animate-fadeUp hero-desc">
          Biz sizga yordam berishga doimo tayyormiz. Savollaringiz bo'lsa
          yo'llang.
        </p>
      </div>
    </section>

    <div class="container my-4 my-lg-5">
      <div class="row g-4 g-lg-5">
        <!-- Contact Info -->
        <div class="col-lg-5 animate-fadeUp">
          <div class="glass-card p-4 p-md-5 h-100">
            <h2 class="fw-bold mb-3 mb-md-4">Aloqa ma'lumotlari</h2>
            <p class="text-secondary mb-4 mb-md-5">
              Savol va takliflaringiz uchun quyidagi ma'lumotlar orqali
              bog'lanishingiz mumkin.
            </p>

            <div class="d-flex flex-column gap-3 gap-md-4 mb-4 mb-md-5">
              <div class="d-flex align-items-center gap-3 gap-md-4 group-hover">
                <div class="icon-round bg-primary-light text-primary">
                  <i class="fas fa-phone-alt"></i>
                </div>
                <div>
                  <h6 class="fw-bold mb-1">Telefon</h6>
                  <p class="text-secondary mb-0">+998 78 122 22 44</p>
                </div>
              </div>

              <div class="d-flex align-items-center gap-3 gap-md-4 group-hover">
                <div class="icon-round bg-success-light text-success">
                  <i class="fas fa-envelope"></i>
                </div>
                <div>
                  <h6 class="fw-bold mb-1">Email</h6>
                  <p class="text-secondary mb-0">info@alfraganushospital.uz</p>
                </div>
              </div>

              <div class="d-flex align-items-center gap-3 gap-md-4 group-hover">
                <div class="icon-round bg-danger-light text-danger">
                  <i class="fas fa-location-dot"></i>
                </div>
                <div>
                  <h6 class="fw-bold mb-1">Manzil</h6>
                  <p class="text-secondary mb-0">
                    Toshkent shahr, Yunusobod tumani, Yuqori Qora-qamish
                    ko'chasi 5-uy
                  </p>
                </div>
              </div>
            </div>

            <h4
              class="fw-bold text-primary mb-3 mb-md-4 text-uppercase tracking-wider"
            >
              <i class="fas fa-clock me-2"></i> Ish vaqti
            </h4>
            <div
              class="d-flex justify-content-between border-bottom border-gray-200 pb-3 mb-3 text-secondary"
            >
              <span
                >Shifokorlar<span class="text-danger">:</span> Dushanba -
                Shanba</span
              ><span class="fw-bold text-dark">09:00 – 16:00</span>
            </div>
            <div
              class="d-flex justify-content-between border-bottom border-gray-200 pb-3 mb-3 text-secondary"
            >
              <span>Klinikamiz va Navbatchi shifokor </span
              ><span class="text-danger fw-bold fs-5">24/7</span>
            </div>
          </div>
        </div>

        <!-- Form -->
        <div class="col-lg-7 animate-fadeUp" style="animation-delay: 0.2s">
          <div class="glass-card p-4 p-md-5 rounded-4">
            <h3 class="fw-bold mb-2">Ariza qoldiring</h3>
            <p class="text-secondary mb-4 mb-md-5">
              Quyidagi maydonlarni to'ldiring, biz siz bilan tez orada
              bog'lanamiz.
            </p>

            <form @submit.prevent="submitForm">
              <div class="row g-3 g-md-4 mb-3 mb-md-4">
                <div class="col-md-6">
                  <div class="form-group mb-0">
                    <label class="mb-1 fw-semibold">Ism va familiya *</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="form.name"
                      placeholder="Ismingizni kiriting"
                      required
                    />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group mb-0">
                    <label class="mb-1 fw-semibold">Telefon raqam *</label>
                    <input
                      type="tel"
                      class="form-control"
                      v-model="form.phone"
                      placeholder="+998 90 123 45 67"
                      required
                    />
                  </div>
                </div>
              </div>
              <div class="form-group mb-4 mb-md-5">
                <label class="mb-1 fw-semibold">Xabar</label>
                <textarea
                  class="form-control"
                  rows="5"
                  v-model="form.message"
                  placeholder="Murojaat sababingizni qisqacha yozing..."
                ></textarea>
              </div>

              <div
                v-if="successMsg"
                class="alert alert-success fs-6 rounded-3 mb-4"
              >
                <i class="fas fa-check-circle me-2"></i>{{ successMsg }}
              </div>
              <div
                v-if="errorMsg"
                class="alert alert-danger fs-6 rounded-3 mb-4"
              >
                <i class="fas fa-exclamation-triangle me-2"></i>{{ errorMsg }}
              </div>

              <button
                type="submit"
                class="btn btn-premium btn-primary w-100 py-3 rounded-pill d-flex justify-content-center align-items-center"
                :disabled="submitting"
              >
                <span v-if="!submitting"
                  >Xabarni yuborish <i class="fas fa-paper-plane ms-2"></i
                ></span>
                <span v-else
                  ><span
                    class="spinner-border spinner-border-sm me-2"
                    role="status"
                    aria-hidden="true"
                  ></span>
                  Yuborilmoqda...</span
                >
              </button>
            </form>
          </div>
        </div>
        <!-- /Form -->
      </div>
      <!-- /row -->
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from "vue";
import contactsService from "@/services/contactsService";
import { validateContactForm } from "@/utils/validators";

const form = reactive({ name: "", phone: "", specialty: "", message: "" });
const submitting = ref(false);
const successMsg = ref("");
const errorMsg = ref("");

const submitForm = async () => {
  successMsg.value = "";
  errorMsg.value = "";

  const { valid, errors } = validateContactForm(form);
  if (!valid) {
    errorMsg.value = Object.values(errors)[0];
    return;
  }

  submitting.value = true;
  try {
    await contactsService.submit({ ...form });
    successMsg.value =
      "Arizangiz qabul qilindi! Tez orada siz bilan bog'lanamiz.";
    Object.assign(form, { name: "", phone: "", specialty: "", message: "" });
  } catch (e) {
    // Forma tozalanmaydi — foydalanuvchi qayta yuborishi mumkin
    const errs = e.response?.data?.errors;
    if (e.response?.status === 429) {
      errorMsg.value =
        "Juda ko'p urinish. Iltimos, birozdan so'ng qayta urinib ko'ring.";
    } else if (errs) {
      const first = Object.values(errs)[0];
      errorMsg.value = Array.isArray(first) ? first[0] : first;
    } else {
      errorMsg.value =
        "Xabarni yuborib bo'lmadi. Iltimos, qayta urinib ko'ring yoki bizga qo'ng'iroq qiling.";
    }
  } finally {
    submitting.value = false;
  }
};
</script>

<style scoped>
.contact-page-root {
  padding-top: 0;
}

/* ══════════ PAGE HERO (VIDEO HERO) ══════════ */
.page-hero {
  position: relative;
  width: 100%;
  height: 450px; /* Katta ekranlar uchun aniq 450px */
  min-height: 450px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  color: #fff;
  background-color: #002b87;
}

/* Video position va alignment */
.page-hero-video {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 100%;
  height: 100%;
  min-width: 100%;
  min-height: 100%;
  object-fit: cover;
  transform: translate(-50%, -50%);
  z-index: 0;
  pointer-events: none;
}

/* Dark Overlay */
.page-hero-overlay {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    135deg,
    rgba(0, 43, 135, 0.75) 0%,
    rgba(0, 20, 70, 0.8) 100%
  );
  z-index: 1;
  pointer-events: none;
}

.page-hero h1 {
  font-size: clamp(2rem, 5vw, 3rem);
  font-weight: 800;
  font-family: "Outfit", sans-serif;
  margin-bottom: 12px;
  color: white;
}

.hero-desc {
  max-width: 700px;
  margin: 0 auto;
  font-size: 1rem;
  color: rgba(255, 255, 255, 0.85);
}

/* ══════════ CARDS & ELEMENTS ══════════ */
.icon-round {
  width: 50px;
  height: 50px;
  min-width: 50px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
}

.bg-primary-light {
  background-color: rgba(37, 99, 235, 0.1);
}

.bg-success-light {
  background-color: #dcfce7;
}

.bg-danger-light {
  background-color: #fee2e2;
}

.glass-card {
  background: white;
  border: 1px solid rgba(0, 0, 0, 0.05);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
}

/* Dark Theme */
[data-theme="dark"] .page-hero {
  background: #0f172a;
}

[data-theme="dark"] .glass-card {
  background: #1e293b;
  border-color: #334155;
  color: #f1f5f9;
}

[data-theme="dark"] .text-secondary {
  color: #94a3b8 !important;
}

[data-theme="dark"] .text-dark {
  color: #f8fafc !important;
}

[data-theme="dark"] .border-gray-200 {
  border-color: #334155 !important;
}

[data-theme="dark"] .form-control {
  background-color: #0f172a;
  border-color: #334155;
  color: white;
}

[data-theme="dark"] .form-control::placeholder {
  color: #475569;
}

[data-theme="dark"] label {
  color: #cbd5e1;
}

/* ══════════ MOBILE & RESPONSIVE ══════════ */
@media (max-width: 768px) {
  .page-hero {
    height: 320px; /* Mobil qurilmalarda aniq 320px */
    min-height: 320px;
    padding: 0 16px;
  }

  .page-hero h1 {
    font-size: 1.75rem;
    margin-bottom: 8px;
  }

  .hero-desc {
    font-size: 0.88rem;
    line-height: 1.5;
  }

  .glass-card {
    padding: 1.25rem !important;
    border-radius: 1rem !important;
  }

  .icon-round {
    width: 42px;
    height: 42px;
    min-width: 42px;
    font-size: 1rem;
  }

  textarea.form-control {
    height: 110px;
  }
}
</style>
