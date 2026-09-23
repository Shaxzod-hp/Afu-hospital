import { defineStore } from 'pinia'
import authService from '../services/authService'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user:    JSON.parse(localStorage.getItem('admin_user') || 'null'),
    token:   localStorage.getItem('admin_token') || null,
    loading: false,
    error:   null,
  }),

  getters: {
    isAuthenticated: (s) => !!s.token,
  },

  actions: {
    async login(username, password) {
      this.loading = true
      this.error = null
      try {
        const data = await authService.login(username, password)
        if (!data || !data.token) {
          throw new Error("Tizimdan yaroqli token olinmadi.")
        }
        this.token = data.token
        this.user  = data.user || data
        localStorage.setItem('admin_token', this.token)
        localStorage.setItem('admin_user', JSON.stringify(this.user))
        return true
      } catch (e) {
        this.token = null
        this.user  = null
        localStorage.removeItem('admin_token')
        localStorage.removeItem('admin_user')
        if (e.response?.data?.message) {
          this.error = e.response.data.message
        } else if (e.response?.data?.errors) {
          const firstErr = Object.values(e.response.data.errors)[0]
          this.error = Array.isArray(firstErr) ? firstErr[0] : firstErr
        } else {
          this.error = e.message || "Tizim xatosi. Qayta urinib ko'ring."
        }
        return false
      } finally {
        this.loading = false
      }
    },

    logout() {
      this.token = null
      this.user  = null
      localStorage.removeItem('admin_token')
      localStorage.removeItem('admin_user')
    },
  },
})
