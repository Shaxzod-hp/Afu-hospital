<template>
  <div class="profile-page">
    <div class="row justify-content-center">
      <div class="col-12 col-xl-10">
        <div class="content-card-glass">
          <!-- Header -->
          <div class="card-header-glass">
            <div class="header-left">
              <div class="header-icon-glass text-primary">
                <i class="fas fa-user-gear"></i>
              </div>
              <div>
                <h4 class="mb-0 fw-bold text-dark">Profil Sozlamalari</h4>
                <p class="mb-0 text-secondary small">
                  Shaxsiy ma'lumotlar va xavfsizlikni boshqarish
                </p>
              </div>
            </div>
          </div>

          <div class="card-body p-4 p-md-5">
            <!-- Global Alerts -->
            <div
              v-if="successMsg"
              class="alert alert-success border-0 mb-4 fw-semibold glass-alert-success"
            >
              <i class="fas fa-check-circle me-2"></i> {{ successMsg }}
            </div>
            <div
              v-if="errorMsg"
              class="alert alert-danger border-0 mb-4 fw-semibold glass-alert-danger"
            >
              <i class="fas fa-exclamation-circle me-2"></i> {{ errorMsg }}
            </div>

            <div class="row g-4">
              <!-- 1-BO'LIM: SHAXSIY MA'LUMOTLAR -->
              <div class="col-12 col-lg-6">
                <div class="section-glass h-100">
                  <h5 class="fw-bold text-dark mb-4">
                    <i class="fas fa-id-card me-2 text-primary"></i> Asosiy
                    Ma'lumotlar
                  </h5>
                  <form @submit.prevent="handleUpdateProfile">
                    <div class="mb-3">
                      <label class="form-label text-dark fw-semibold small"
                        >Ism va Familiya</label
                      >
                      <input
                        type="text"
                        v-model="profileForm.name"
                        class="form-control form-control-glass"
                        placeholder="Admin ismi"
                        required
                      />
                    </div>

                    <div class="mb-3">
                      <label class="form-label text-dark fw-semibold small"
                        >Email manzil</label
                      >
                      <input
                        type="email"
                        v-model="profileForm.email"
                        class="form-control form-control-glass"
                        placeholder="admin@example.com"
                        required
                      />
                    </div>

                    <button
                      type="submit"
                      class="btn btn-primary btn-glass fw-bold w-100 mt-4"
                      :disabled="profileLoading"
                    >
                      <span
                        v-if="profileLoading"
                        class="spinner-border spinner-border-sm me-2"
                      ></span>
                      Ma'lumotlarni Saqlash
                    </button>
                  </form>
                </div>
              </div>

              <!-- 2-BO'LIM: XAVFSIZLIK (PAROLNI O'ZGARTIRISH) -->
              <div class="col-12 col-lg-6">
                <div class="section-glass h-100">
                  <h5 class="fw-bold text-dark mb-4">
                    <i class="fas fa-shield-halved me-2 text-warning"></i>
                    Parolni O'zgartirish
                  </h5>
                  <form @submit.prevent="handleChangePassword">
                    <!-- Joriy Parol -->
                    <div class="mb-3">
                      <label class="form-label text-dark fw-semibold small"
                        >Joriy parol</label
                      >
                      <div class="input-group">
                        <input
                          :type="showCurrentPassword ? 'text' : 'password'"
                          v-model="pwdForm.current_password"
                          class="form-control form-control-glass"
                          placeholder="••••••••"
                          required
                          autocomplete="current-password"
                        />
                        <button
                          class="btn btn-outline-secondary border-start-0 bg-white"
                          type="button"
                          @click="showCurrentPassword = !showCurrentPassword"
                        >
                          <i
                            :class="
                              showCurrentPassword
                                ? 'fas fa-eye-slash'
                                : 'fas fa-eye'
                            "
                          ></i>
                        </button>
                      </div>
                    </div>

                    <!-- Yangi Parol -->
                    <div class="mb-3">
                      <label class="form-label text-dark fw-semibold small"
                        >Yangi parol</label
                      >
                      <div class="input-group">
                        <input
                          :type="showNewPassword ? 'text' : 'password'"
                          v-model="pwdForm.new_password"
                          class="form-control form-control-glass"
                          placeholder="Minimal 8 ta belgi"
                          required
                          minlength="8"
                          autocomplete="new-password"
                        />
                        <button
                          class="btn btn-outline-secondary border-start-0 bg-white"
                          type="button"
                          @click="showNewPassword = !showNewPassword"
                        >
                          <i
                            :class="
                              showNewPassword
                                ? 'fas fa-eye-slash'
                                : 'fas fa-eye'
                            "
                          ></i>
                        </button>
                      </div>
                    </div>

                    <!-- Yangi Parolni Tasdiqlash -->
                    <div class="mb-3">
                      <label class="form-label text-dark fw-semibold small"
                        >Yangi parolni tasdiqlang</label
                      >
                      <div class="input-group">
                        <input
                          :type="showConfirmPassword ? 'text' : 'password'"
                          v-model="pwdForm.new_password_confirmation"
                          class="form-control form-control-glass"
                          placeholder="••••••••"
                          required
                          minlength="8"
                          autocomplete="new-password"
                        />
                        <button
                          class="btn btn-outline-secondary border-start-0 bg-white"
                          type="button"
                          @click="showConfirmPassword = !showConfirmPassword"
                        >
                          <i
                            :class="
                              showConfirmPassword
                                ? 'fas fa-eye-slash'
                                : 'fas fa-eye'
                            "
                          ></i>
                        </button>
                      </div>
                    </div>

                    <button
                      type="submit"
                      class="btn btn-warning btn-glass text-white fw-bold w-100 mt-4"
                      :disabled="pwdLoading"
                    >
                      <span
                        v-if="pwdLoading"
                        class="spinner-border spinner-border-sm me-2"
                      ></span>
                      Parolni Yangilash
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../../services/api";

const successMsg = ref("");
const errorMsg = ref("");

// Password visibility state
const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

// Profile Form State
const profileForm = ref({
  name: "",
  email: "",
});
const profileLoading = ref(false);

// Password Form State
const pwdForm = ref({
  current_password: "",
  new_password: "",
  new_password_confirmation: "",
});
const pwdLoading = ref(false);

// Profil ma'lumotlarini yuklash
const fetchProfile = async () => {
  try {
    const res = await api.get("/auth/me");
    const data = res.data?.data || res.data;
    if (data) {
      profileForm.value.name = data.name || "";
      profileForm.value.email = data.email || "";
    }
  } catch (err) {
    const user = JSON.parse(localStorage.getItem("admin_user") || "{}");
    if (user) {
      profileForm.value.name = user.name || "";
      profileForm.value.email = user.email || "";
    }
  }
};

// Profilni yangilash
const handleUpdateProfile = async () => {
  profileLoading.value = true;
  successMsg.value = "";
  errorMsg.value = "";

  try {
    const res = await api.put("/admin/profile", profileForm.value);
    successMsg.value =
      res.data?.message || "Profil ma'lumotlari muvaffaqiyatli yangilandi!";

    const storedUser = JSON.parse(localStorage.getItem("admin_user") || "{}");
    localStorage.setItem(
      "admin_user",
      JSON.stringify({ ...storedUser, ...profileForm.value })
    );
  } catch (err) {
    if (err.response?.data?.errors) {
      const firstErr = Object.values(err.response.data.errors)[0];
      errorMsg.value = Array.isArray(firstErr) ? firstErr[0] : firstErr;
    } else {
      errorMsg.value =
        err.response?.data?.message ||
        "Ma'lumotlarni yangilashda xatolik yuz berdi.";
    }
  } finally {
    profileLoading.value = false;
  }
};

// Parolni o'zgartirish
const handleChangePassword = async () => {
  if (pwdForm.value.new_password !== pwdForm.value.new_password_confirmation) {
    errorMsg.value = "Yangi parol va tasdiqlash paroli bir-biriga mos kelmadi.";
    return;
  }

  pwdLoading.value = true;
  successMsg.value = "";
  errorMsg.value = "";

  try {
    const res = await api.put("/admin/settings/password", pwdForm.value);
    successMsg.value =
      res.data?.message || "Parol muvaffaqiyatli o'zgartirildi!";
    pwdForm.value = {
      current_password: "",
      new_password: "",
      new_password_confirmation: "",
    };
  } catch (err) {
    if (err.response?.data?.errors) {
      const firstErr = Object.values(err.response.data.errors)[0];
      errorMsg.value = Array.isArray(firstErr) ? firstErr[0] : firstErr;
    } else {
      errorMsg.value =
        err.response?.data?.message ||
        "Parolni o'zgartirishda xatolik yuz berdi.";
    }
  } finally {
    pwdLoading.value = false;
  }
};

onMounted(fetchProfile);
</script>

<style scoped>
.content-card-glass {
  background: rgba(255, 255, 255, 0.45);
  backdrop-filter: blur(20px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.7);
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(31, 38, 135, 0.04);
}

.card-header-glass {
  padding: 24px 28px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(255, 255, 255, 0.6);
}

.header-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.header-icon-glass {
  width: 50px;
  height: 50px;
  border-radius: 16px;
  background: rgba(2, 132, 199, 0.12);
  border: 1px solid rgba(2, 132, 199, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.3rem;
}

.section-glass {
  background: rgba(255, 255, 255, 0.35);
  border: 1px solid rgba(255, 255, 255, 0.6);
  border-radius: 18px;
  padding: 24px;
}

.form-control-glass {
  background: rgba(255, 255, 255, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.8);
  border-radius: 12px 0 0 12px;
  padding: 12px 16px;
  color: #0f172a;
  font-weight: 500;
  transition: all 0.25s ease;
}

.input-group .btn {
  border-radius: 0 12px 12px 0;
  border-color: rgba(255, 255, 255, 0.8);
}

.form-control-glass:focus {
  background: rgba(255, 255, 255, 0.9);
  border-color: #0284c7;
  box-shadow: none;
}

.btn-glass {
  border: none;
  padding: 12px;
  border-radius: 12px;
  transition: all 0.25s ease;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.btn-glass:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.glass-alert-success {
  background: rgba(16, 185, 129, 0.12);
  color: #047857;
  border-radius: 12px;
}

.glass-alert-danger {
  background: rgba(239, 68, 68, 0.12);
  color: #b91c1c;
  border-radius: 12px;
}
</style>
