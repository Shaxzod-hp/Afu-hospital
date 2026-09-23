import { defineStore } from 'pinia'
import api, { unwrap } from '../services/api'

export const useServicesStore = defineStore('services', {
  state: () => ({
    services: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchServices() {
      this.loading = true
      this.error = null
      try {
        const data = await api.get('/services').then(unwrap)
        this.services = Array.isArray(data) ? data : []
      } catch (err) {
        this.services = []
        this.error = "Xizmatlar ma'lumotlarini yuklashda xatolik yuz berdi."
      } finally {
        this.loading = false
      }
    },
  },
})
