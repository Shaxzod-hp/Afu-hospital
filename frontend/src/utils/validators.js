/**
 * Uzbekistan phone number validation
 * Accepts: +998 90 123 45 67  /  998901234567  /  901234567
 */
export const isValidPhone = (phone) => {
  if (!phone) return false
  const cleaned = phone.replace(/[\s\-()]/g, '')
  return /^(\+998|998)?[0-9]{9}$/.test(cleaned)
}

/**
 * Basic email validation
 */
export const isValidEmail = (email) =>
  /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(email).toLowerCase())

/**
 * Non-empty string check
 */
export const isRequired = (val) =>
  val !== null && val !== undefined && String(val).trim().length > 0

/**
 * Minimum length check
 */
export const minLength = (val, min) =>
  String(val || '').trim().length >= min

/**
 * Validate a contact form object
 * Returns { valid: Boolean, errors: Object }
 */
export const validateContactForm = ({ name, phone }) => {
  const errors = {}
  if (!isRequired(name))    errors.name  = "Ism majburiy maydon"
  if (!isValidPhone(phone)) errors.phone = "Telefon raqam noto'g'ri"
  return { valid: Object.keys(errors).length === 0, errors }
}
