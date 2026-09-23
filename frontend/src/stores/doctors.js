import { defineStore } from 'pinia'
import doctorsService from '../services/doctorsService'

export const useDoctorsStore = defineStore('doctors', {
  state: () => ({
    doctors:      [],
    specialties:  [],
    activeFilter: 'Barchasi',
    loading:      false,
    error:        null,
  }),

  getters: {
    filtered: (s) => {
      const list = Array.isArray(s.doctors) ? s.doctors : []
      return s.activeFilter === 'Barchasi'
        ? list
        : list.filter(d => d.specialty === s.activeFilter)
    },

    featured: (s) => {
      const list = Array.isArray(s.doctors) ? s.doctors : []
      return list.filter(d => d.featured)
    },

    // stores/doctors.js
    getById: (s) => (idOrSlug) => {
      const list = Array.isArray(s.doctors) ? s.doctors : []
      return list.find(
        (d) =>
          String(d.id) === String(idOrSlug) ||
          d.slug === idOrSlug
      )
    },
  },

  actions: {
    setFilter(specialty) { this.activeFilter = specialty },

    async fetchSpecialties() {
      try {
        // Agarda service'ingizda fetchSpecialties yoki getSpecialties bo'lsa:
        if (doctorsService.fetchSpecialties) {
          const data = await doctorsService.fetchSpecialties()
          this.specialties = Array.isArray(data) ? data : (data?.data || [])
        } else if (doctorsService.getSpecialties) {
          const data = await doctorsService.getSpecialties()
          this.specialties = Array.isArray(data) ? data : (data?.data || [])
        }
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
        
        // Backend'dan keladigan turli formatlarni xavfsiz qabul qilish
        if (Array.isArray(data)) {
          this.doctors = data
        } else if (data && Array.isArray(data.data)) {
          this.doctors = data.data
        } else {
          this.doctors = []
        }
      } catch (err) {
        this.doctors = []
        this.error = "Shifokorlar ma'lumotlarini yuklashda xatolik yuz berdi."
      } finally {
        this.loading = false
      }
    },

    async saveDoctor(data) {
      if (data.id) return doctorsService.update(data.id, data)
      return doctorsService.create(data)
    },

    async removeDoctor(id) {
      await doctorsService.remove(id)
      if (Array.isArray(this.doctors)) {
        this.doctors = this.doctors.filter(d => d.id !== id)
      }
    },
  },
})