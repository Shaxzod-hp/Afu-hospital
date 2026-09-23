import { defineStore } from 'pinia'
import surgeryService from '../services/surgeryService'

// TODO: connect to /api/admin/surgeries once backend is ready
export const useSurgeryStore = defineStore('surgery', {
  state: () => ({
    surgeries: [],
    loading:   false,
    error:     null,
  }),

  getters: {
    featured: (s) => s.surgeries.filter(op => op.featured),
    getById:  (s) => (id) =>
      s.surgeries.find(op => String(op.id)?.toLowerCase() === String(id)?.toLowerCase()),
  },

  actions: {
    async fetchSurgeries() {
      this.loading = true
      this.error   = null
      try {
        const data = await surgeryService.fetchAllPublic()
        this.surgeries = Array.isArray(data) ? data : (data?.data || [])
      } catch (err) {
        this.surgeries = []
        this.error = "Operatsiyalar ma'lumotlarini yuklashda xatolik yuz berdi."
      } finally {
        this.loading = false
      }
    },

    async saveSurgery(data) {
      if (data.id) return surgeryService.update(data.id, data)
      return surgeryService.create(data)
    },

    async removeSurgery(id) {
      await surgeryService.remove(id)
      this.surgeries = this.surgeries.filter(s => s.id !== id)
    },
  },
})