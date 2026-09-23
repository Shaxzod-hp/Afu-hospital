import { defineStore } from 'pinia'
import api, { unwrap } from '../services/api'

export const useStatsionarStore = defineStore('statsionarPublic', {
  state: () => ({
    packages: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchPackages() {
      this.loading = true
      this.error = null
      try {
        const data = await api.get('/statsionar').then(unwrap)
        this.packages = Array.isArray(data) ? data : (data?.data || [])
      } catch (err) {
        this.packages = []
        this.error = "Ma'lumotlarni yuklashda xatolik yuz berdi."
      } finally {
        this.loading = false
      }
    },
  },
})