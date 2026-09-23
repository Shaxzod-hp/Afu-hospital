import { defineStore } from 'pinia'
import newsServices from '../services/newsService'

export const useNewsStore = defineStore('news', {
  state: () => ({
    news: [],
    categories: ['Yangilik', 'Aksiya', 'Tadbir'],
    activeFilter: 'Barchasi',
    loading: false,
    error: null,
  }),

  getters: {
    filtered: (s) =>
      s.activeFilter === 'Barchasi'
        ? s.news
        : s.news.filter(n => n.category === s.activeFilter),

    featured: (s) => s.news.filter(n => n.featured || n.is_featured),

    getById: (s) => (id) =>
      s.news.find(n => String(n.id) === String(id) || n.slug === id),
  },

  actions: {
    setFilter(cat) { 
      this.activeFilter = cat 
    },

    async fetchNews() {
      this.loading = true
      this.error = null
      try {
        const data = await newsServices.fetchAll()
        // Laravel paginate qaytarsa data.data ko'rinishida bo'ladi
        this.news = Array.isArray(data) ? data : (data?.data || [])
      } catch (err) {
        this.news = []
        this.error = "Yangiliklar ma'lumotlarini yuklashda xatolik yuz berdi."
      } finally {
        this.loading = false
      }
    },

    async saveNews(data) {
      let response
      if (data.id || (data instanceof FormData && data.has('id'))) {
        const id = data.id || data.get('id')
        response = await newsServices.update(id, data)
      } else {
        response = await newsServices.create(data)
      }
      // Baza yangilangach ro'yxatni ham qayta yuklab qo'yamiz
      await this.fetchNews()
      return response
    },

    async removeNews(id) {
      await newsServices.remove(id)
      this.news = this.news.filter(n => String(n.id) !== String(id))
    },
  },
})