// Brauzerlarda "uz-Latn" lokali to'liq emas ("2026 M09 22" chiqadi) — oy nomlari qo'lda
const UZ_MONTHS = [
  "yanvar", "fevral", "mart", "aprel", "may", "iyun",
  "iyul", "avgust", "sentabr", "oktabr", "noyabr", "dekabr",
]

/**
 * Format a date string or Date object to Uzbek locale
 * @param {string|Date} d
 * @param {'date'|'datetime'} mode
 */
export const formatDate = (d, mode = 'date') => {
  if (!d) return ''
  const date = new Date(d)
  if (isNaN(date)) return ''
  let out = `${date.getDate()}-${UZ_MONTHS[date.getMonth()]}, ${date.getFullYear()}`
  if (mode === 'datetime') {
    const hh = String(date.getHours()).padStart(2, '0')
    const mm = String(date.getMinutes()).padStart(2, '0')
    out += ` ${hh}:${mm}`
  }
  return out
}

/**
 * Format UZS price with space-separated thousands
 * @param {number} amount
 */
export const formatPrice = (amount) => {
  if (!amount && amount !== 0) return ''
  return new Intl.NumberFormat('uz-UZ').format(amount) + " so'm"
}

/**
 * Format a price range object { min, max }
 */
export const formatPriceRange = ({ min, max } = {}) => {
  if (!min && !max) return ''
  if (!max) return `${formatPrice(min)} dan`
  return `${formatPrice(min)} – ${formatPrice(max)}`
}

/**
 * Truncate a string to n characters
 */
export const truncate = (str, n = 120) => {
  if (!str) return ''
  return str.length > n ? str.slice(0, n).trimEnd() + '...' : str
}

/**
 * Convert string to URL-safe slug
 */
export const slugify = (str) =>
  str
    .toLowerCase()
    .trim()
    .replace(/[^\w\s-]/g, '')
    .replace(/\s+/g, '-')

/**
 * Return initials from a full name (e.g. "John Doe" → "JD")
 */
export const initials = (name = '') =>
  name.split(' ').slice(0, 2).map(w => w[0]?.toUpperCase() || '').join('')

/**
 * Relative time: returns "3 kun oldin" etc.
 */
export const timeAgo = (d) => {
  if (!d) return ''
  const diff = Date.now() - new Date(d).getTime()
  const mins  = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days  = Math.floor(diff / 86400000)
  if (mins  < 1)  return 'Hozir'
  if (mins  < 60) return `${mins} daqiqa oldin`
  if (hours < 24) return `${hours} soat oldin`
  if (days  < 30) return `${days} kun oldin`
  return formatDate(d)
}
