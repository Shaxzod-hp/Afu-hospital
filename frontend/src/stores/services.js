import { defineStore } from 'pinia'
import api, { unwrap } from '../services/api'
import servicesService from '../services/servicesService'

export const useServicesStore = defineStore('services', {
  state: () => ({
    services: [],
    loading: false,
    error: null,
  }),

  getters: {
    featured: (s) => s.services.filter(sv => sv.featured),
    getById: (s) => (id) =>
      s.services.find(sv => String(sv.id)?.toLowerCase() === String(id)?.toLowerCase()),
  },

  actions: {
    async fetchServices() {
      this.loading = true
      this.error = null
      try {
        const data = await api.get('/services').then(unwrap)
        this.services = Array.isArray(data) ? data : (data?.data || [])
      } catch (err) {
        this.services = []
        this.error = "Xizmatlar ma'lumotlarini yuklashda xatolik yuz berdi."
      } finally {
        this.loading = false
      }
    },

    async saveService(data) {
      if (data.id) return servicesService.update(data.id, data)
      return servicesService.create(data)
    },

    async removeService(id) {
      await servicesService.remove(id)
      this.services = this.services.filter(s => s.id !== id)
    },
  },
})