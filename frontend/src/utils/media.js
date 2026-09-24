const storageUrl = import.meta.env.VITE_STORAGE_URL || "";

/**
 * Backend'dagi rasm yo'lini to'liq URL ga aylantiradi.
 * Bazada yo'l "/storage/uploads/..." ko'rinishida saqlanadi.
 * Rasm bo'lmasa null qaytaradi — komponent o'zi placeholder ko'rsatadi.
 */
export const mediaUrl = (path) => {
  if (!path) return null;
  if (/^https?:\/\//.test(path)) return path;
  let clean = path.startsWith("/") ? path : `/${path}`;
  if (!clean.startsWith("/storage") && !clean.startsWith("/uploads")) {
    clean = `/storage${clean}`;
  }
  return storageUrl + clean;
};

/**
 * HTML matndan teglarni olib tashlab, oddiy matn qaytaradi (kartalardagi qisqa tavsif uchun).
 */
export const stripHtml = (html = "") => {
  if (!html) return "";
  const doc = new DOMParser().parseFromString(html, "text/html");
  return (doc.body.textContent || "").replace(/\s+/g, " ").trim();
};

/**
 * Taxminiy o'qish vaqti (daqiqa), ~200 so'z/daqiqa.
 */
export const readingTime = (html = "") => {
  const words = stripHtml(html).split(" ").filter(Boolean).length;
  return Math.max(1, Math.round(words / 200));
};
