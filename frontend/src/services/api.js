import axios from 'axios'
import compressImage from '../utils/compressImage'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api',
  timeout: 10000,
  headers: { 'Content-Type': 'application/json' }
})

// FormData ichidagi katta rasmlarni yuborishdan oldin siqamiz —
// barcha admin formalar (shifokor, yangilik, lavha, ...) uchun bir joyda
const compressFormDataImages = async (formData) => {
  const result = new FormData()
  for (const [key, value] of formData.entries()) {
    if (value instanceof File && value.type.startsWith('image/')) {
      const file = await compressImage(value)
      result.append(key, file, file.name)
    } else {
      result.append(key, value)
    }
  }
  return result
}

// Attach auth token on every request
api.interceptors.request.use(async config => {
  const token = localStorage.getItem('admin_token')
  if (token) config.headers.Authorization = `Bearer ${token}`

  // FormData yuborilganda, brauzer o'zi to'g'ri
  // multipart/form-data + boundary header qo'yishi kerak.
  // Standart 'application/json' buni buzadi, shuning uchun o'chiramiz.
  if (config.data instanceof FormData) {
    delete config.headers['Content-Type']
    // Rasm yuklash sekin tarmoqda 10 soniyadan uzoq davom etishi mumkin
    config.timeout = 120000
    config.data = await compressFormDataImages(config.data)
  }

  return config
})

// Normalize response: unwrap { data: [...] } envelope if present
api.interceptors.response.use(
  res => res,
  err => {
    if (err.response?.status === 401) {
      localStorage.removeItem('admin_token')
      localStorage.removeItem('admin_user')
      const adminPrefix = `/${import.meta.env.VITE_ADMIN_PATH || 'admin'}`
      if (window.location.pathname.startsWith(adminPrefix)) {
        window.location.href = `${adminPrefix}/login`
      }
    }
    return Promise.reject(err)
  }
)

/**
 * Safely extracts array/object from various API envelope shapes:
 * - plain [...]
 * - { data: [...] }
 * - { success, data: [...] }
 * - { success, data: { data: [...], current_page, ... } } (Laravel pagination)
 */
export const unwrap = (res) => {
  const d = res.data

  if (Array.isArray(d)) return d

  if (d && typeof d === 'object') {
    // { data: [...] }
    if (Array.isArray(d.data)) return d.data

    // { data: { data: [...], current_page, ... } } — Laravel paginate() yana o'ralgan holatda
    if (d.data && typeof d.data === 'object' && Array.isArray(d.data.data)) {
      return d.data.data
    }

    return d
  }

  return d
}

export default api