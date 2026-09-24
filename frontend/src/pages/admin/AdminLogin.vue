<template>
  <div class="login-page d-flex align-items-center justify-content-center p-3">
    <div class="login-container">
      <div class="login-card-glass animate-fadeUp">
        <div class="login-header text-center mb-4">
          <h3 class="fw-bold text-dark mb-1">Xush kelibsiz!</h3>
          <p class="text-secondary small m-0">
            Admin paneliga kirish uchun ma'lumotlaringizni kiriting
          </p>
        </div>

        <form @submit.prevent="handleLogin" class="login-form">
          <div class="form-item mb-3">
            <label class="form-label-glass">Login</label>
            <div class="input-wrapper-glass">
              <i class="fas fa-user input-icon"></i>
              <input
                type="text"
                v-model="form.username"
                placeholder="Foydalanuvchi nomi"
                required
              />
            </div>
          </div>

          <div class="form-item mb-4">
            <label class="form-label-glass">Parol</label>
            <div class="input-wrapper-glass">
              <i class="fas fa-lock input-icon"></i>
              <input
                type="password"
                v-model="form.password"
                placeholder="********"
                required
              />
            </div>
          </div>

          <div v-if="auth.error" class="error-msg-glass mb-3">
            <i class="fas fa-exclamation-circle"></i> {{ auth.error }}
          </div>

          <button
            type="submit"
            class="submit-btn-glass w-100 py-3 rounded-3"
            :disabled="auth.loading"
          >
            <span v-if="!auth.loading"
              >Tizimga kirish <i class="fas fa-arrow-right ms-2"></i
            ></span>
            <span v-else
              ><span class="spinner-border spinner-border-sm me-2"></span>
              Kirilmoqda...</span
            >
          </button>
        </form>

        <div class="login-footer text-center mt-4">
          <router-link to="/" class="back-link">
            <i class="fas fa-chevron-left me-2"></i> Saytning bosh sahifasiga
            qaytish
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/auth";

const router = useRouter();
const auth = useAuthStore();

const form = reactive({ username: "", password: "" });

const handleLogin = async () => {
  if (!form.username || !form.password) return;
  const ok = await auth.login(form.username, form.password);
  if (ok) {
    if (auth.user && auth.user.role !== 'admin') {
      auth.error = "Faqat adminlar kirishi mumkin!";
      auth.logout();
      return;
    }
    router.push({ name: "admin-dashboard" });
  }
};
</script>

<style scoped>
/* =========================
   GLASSMORPHISM LOGIN
========================= */
.login-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 40%, #e0e7ff 100%);
  font-family: inherit;
}

.login-container {
  width: 100%;
  max-width: 440px;
}

.login-card-glass {
  background: rgba(255, 255, 255, 0.45);
  backdrop-filter: blur(25px) saturate(180%);
  -webkit-backdrop-filter: blur(25px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.7);
  border-radius: 28px;
  padding: 40px;
  box-shadow: 0 15px 35px 0 rgba(31, 38, 135, 0.08);
}

.form-label-glass {
  display: block;
  font-weight: 700;
  color: #334155;
  margin-bottom: 8px;
  font-size: 0.88rem;
}

.input-wrapper-glass {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 16px;
  color: #64748b;
  font-size: 1rem;
}

.input-wrapper-glass input {
  width: 100%;
  padding: 14px 16px 14px 46px;
  background: rgba(255, 255, 255, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.8);
  border-radius: 14px;
  color: #0f172a;
  font-weight: 500;
  transition: all 0.25s ease;
  outline: none;
}

.input-wrapper-glass input:focus {
  background: rgba(255, 255, 255, 0.9);
  border-color: #0284c7;
  box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.15);
}

.input-wrapper-glass input::placeholder {
  color: #94a3b8;
}

.error-msg-glass {
  background: rgba(227, 30, 36, 0.1);
  border: 1px solid rgba(227, 30, 36, 0.2);
  color: #e31e24;
  padding: 12px 16px;
  border-radius: 12px;
  font-size: 0.85rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
}

.submit-btn-glass {
  background: #0284c7;
  color: #ffffff;
  border: none;
  font-weight: 700;
  font-size: 0.95rem;
  transition: all 0.25s ease;
  box-shadow: 0 8px 20px rgba(2, 132, 199, 0.3);
}

.submit-btn-glass:hover:not(:disabled) {
  background: #0369a1;
  transform: translateY(-2px);
  box-shadow: 0 12px 24px rgba(2, 132, 199, 0.4);
}

.submit-btn-glass:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.back-link {
  color: #64748b;
  text-decoration: none;
  font-size: 0.88rem;
  font-weight: 600;
  transition: color 0.2s ease;
}

.back-link:hover {
  color: #0284c7;
}

@media (max-width: 480px) {
  .login-card-glass {
    padding: 28px 20px;
    border-radius: 22px;
  }
}
</style>
