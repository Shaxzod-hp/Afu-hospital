import { defineStore } from 'pinia'
import doctorsService from '../services/doctorsService'

export const useDoctorsStore = defineStore('doctors', {
  state: () => ({
    doctors:     [],
    specialties: [],
    loading:     false,
    error:       null,
  }),

  getters: {
    getById: (s) => (idOrSlug) => {
      const list = Array.isArray(s.doctors) ? s.doctors : []
      return list.find(
        (d) => String(d.id) === String(idOrSlug) || d.slug === idOrSlug
      )
    },
  },

  actions: {
    async fetchSpecialties() {
      try {
        const data = await doctorsService.fetchSpecialties()
        this.specialties = Array.isArray(data) ? data : []
      } catch (err) {
        console.error("Mutaxassisliklarni yuklashda xatolik:", err)
        this.specialties = []
      }
    },

    async fetchDoctors() {
      this.loading = true
      this.error   = null
      try {
        const data = await doctorsService.fetchAll()
        this.doctors = Array.isArray(data) ? data : []
      } catch (err) {
        this.doctors = []
        this.error = "Shifokorlar ma'lumotlarini yuklashda xatolik yuz berdi."
      } finally {
        this.loading = false
      }
    },
  },
})
