// Telefon rasmlari 3–10 MB bo'ladi. Hostingdagi PHP yuklash limitlariga
// (upload_max_filesize / post_max_size) urilmaslik uchun rasmni yuborishdan
// oldin brauzerning o'zida kichraytirib, JPEG formatiga o'tkazamiz.

const MAX_SIDE = 1600
const QUALITY = 0.82
const SKIP_BELOW_BYTES = 700 * 1024

const loadImage = (file) =>
  new Promise((resolve, reject) => {
    const url = URL.createObjectURL(file)
    const img = new Image()
    img.onload = () => {
      URL.revokeObjectURL(url)
      resolve(img)
    }
    img.onerror = (e) => {
      URL.revokeObjectURL(url)
      reject(e)
    }
    img.src = url
  })

export default async function compressImage(file) {
  if (!file.type.startsWith('image/') || file.size <= SKIP_BELOW_BYTES) return file

  try {
    const img = await loadImage(file)
    const scale = Math.min(1, MAX_SIDE / Math.max(img.naturalWidth, img.naturalHeight))
    const width = Math.round(img.naturalWidth * scale)
    const height = Math.round(img.naturalHeight * scale)

    const canvas = document.createElement('canvas')
    canvas.width = width
    canvas.height = height
    const ctx = canvas.getContext('2d')
    // PNG shaffof foni JPEG'da qora bo'lib qolmasin
    ctx.fillStyle = '#fff'
    ctx.fillRect(0, 0, width, height)
    ctx.drawImage(img, 0, 0, width, height)

    const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', QUALITY))
    if (!blob || blob.size >= file.size) return file

    const name = file.name.replace(/\.[^.]+$/, '') + '.jpg'
    return new File([blob], name, { type: 'image/jpeg', lastModified: Date.now() })
  } catch {
    // Brauzer rasmni o'qiy olmasa — asl faylni yuboramiz, server tekshiradi
    return file
  }
}
