import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'

// Import Bootstrap CSS and JS
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import 'bootstrap-icons/font/bootstrap-icons.css'

import './assets/main.css'

// Rasm yuklanmasa (fayl o'chirilgan, tarmoq xatosi) alt-matn va "buzilgan rasm"
// belgisi o'rniga neytral placeholder ko'rsatamiz — butun sayt uchun bitta joyda
const IMG_FALLBACK =
  'data:image/svg+xml,' +
  encodeURIComponent(
    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96"><g fill="none" stroke="#94a3b8" stroke-width="3" opacity=".7"><rect x="22" y="26" width="52" height="44" rx="6"/><circle cx="38" cy="41" r="5"/><path d="M24 64l15-14 11 10 8-7 14 12"/></g></svg>'
  )
window.addEventListener(
  'error',
  (e) => {
    const img = e.target
    if (img?.tagName !== 'IMG' || img.dataset.fallback) return
    img.dataset.fallback = '1'
    img.classList.add('img-broken')
    img.src = IMG_FALLBACK
  },
  true
)

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')
