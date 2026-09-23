import { defineStore } from 'pinia'
import newsService from '../services/newsService'

export const useNewsStore = defineStore('news', {
  state: () => ({
    news: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchNews() {
      this.loading = true
      this.error = null
      try {
        const data = await newsService.fetchAll()
        this.news = Array.isArray(data) ? data : []
      } catch (err) {
        this.news = []
        this.error = "Yangiliklar ma'lumotlarini yuklashda xatolik yuz berdi."
      } finally {
        this.loading = false
      }
    },
  },
})
