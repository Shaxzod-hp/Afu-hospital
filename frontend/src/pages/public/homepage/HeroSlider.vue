<template>
  <div class="hero-container position-relative">
    <Swiper :modules="modules" effect="fade" :loop="true" :autoplay="{ delay: 4500, disableOnInteraction: false }"
      :navigation="{ prevEl: '.hero-prev', nextEl: '.hero-next' }" class="hero-swiper">
      <SwiperSlide v-for="(slide, i) in slides" :key="i" class="hero-slide">
        <!-- Background image -->
        <div class="hero-img-wrap">
          <img :src="slide.image" :alt="slide.title" class="img-cover" />
          <div class="hero-overlay" />
        </div>

        <!-- Content -->
        <div
          class="hero-content d-flex flex-column justify-content-center align-items-center text-white text-center px-3">
          <span class="hero-eyebrow animate-fadeUp">{{ slide.eyebrow }}</span>
          <h1 class="hero-title font-display fw-900 animate-fadeUp animate-fadeUp-delay-1">
            {{ slide.title }}
          </h1>
          <p class="hero-sub animate-fadeUp animate-fadeUp-delay-2">{{ slide.sub }}</p>
          <div class="d-flex gap-3 mt-4 animate-fadeUp animate-fadeUp-delay-3 flex-wrap justify-content-center">
            <router-link to="/contact" class="btn btn-danger rounded-pill px-4 py-2 fw-bold">
              Qabulga yozilish <i class="bi bi-calendar-plus ms-1"></i>
            </router-link>
            <router-link to="/services" class="btn btn-outline-light rounded-pill px-4 py-2 fw-bold">
              Xizmatlar <i class="bi bi-arrow-right ms-1"></i>
            </router-link>
          </div>
        </div>
      </SwiperSlide>
    </Swiper>

    <!-- Custom navigation -->
    <button class="hero-prev hero-nav-btn d-none d-md-flex" aria-label="Oldingi">
      <i class="bi bi-chevron-left"></i>
    </button>
    <button class="hero-next hero-nav-btn d-none d-md-flex" aria-label="Keyingi">
      <i class="bi bi-chevron-right"></i>
    </button>
  </div>
</template>

<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay, EffectFade, Navigation } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/effect-fade'
import 'swiper/css/navigation'

const modules = [Autoplay, EffectFade, Navigation]

const slides = [
  {
    eyebrow: 'Zamonaviy Tibbiyot',
    title: 'Sog\'ligingiz — Bizning Ustuvorligimiz',
    sub: 'Xalqaro standartlardagi tibbiy yordam va malakali mutaxassislar jamoasi',
    image: 'https://images.unsplash.com/photo-1551190822-a9333d879b1f?auto=format&fit=crop&w=1600&q=80',
  },
  {
    eyebrow: 'Yurak-Qon Tomir',
    title: 'Zamonaviy Kardiologiya Markazi',
    sub: 'Eng yangi diagnostika uskunalari bilan yurak kasalliklarini davolash',
    image: 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1600&q=80',
  },
  {
    eyebrow: 'Lazer Tibbiyoti',
    title: 'Og\'riqsiz Jarrohlik Muolajalar',
    sub: 'Minimal invaziv lazer texnologiyalari — tezkor tiklash, minimal asorat',
    image: 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?auto=format&fit=crop&w=1600&q=80',
  },
  {
    eyebrow: 'Diagnostika',
    title: 'Aniq va Tezkor Tashxis',
    sub: 'UZI va laboratoriya tahlillari — natijalar bir kunda',
    image: 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?auto=format&fit=crop&w=1600&q=80',
  },
]
</script>

<style scoped>
.hero-container {
  width: 100%;
  height: 100vh;
  background: #001433;
}

.hero-swiper,
.hero-slide {
  height: 100%;
}

.hero-img-wrap {
  position: absolute;
  inset: 0;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to right,
      rgba(0, 20, 80, .75) 0%,
      rgba(0, 20, 80, .45) 50%,
      rgba(0, 20, 80, .55) 100%);
}

.hero-content {
  position: relative;
  z-index: 2;
  height: 100%;
  padding-top: 80px;
  padding-bottom: 60px;
}

.hero-eyebrow {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 3px;
  color: rgba(255, 255, 255, .8);
  margin-bottom: 12px;
  display: inline-block;
}

.hero-title {
  font-size: clamp(1.9rem, 4.5vw, 3.4rem);
  line-height: 1.15;
  max-width: 800px;
  margin: 0 auto 12px;
}

.hero-sub {
  font-size: clamp(0.9rem, 1.8vw, 1.15rem);
  color: rgba(255, 255, 255, .8);
  max-width: 600px;
  margin: 0 auto;
}

/* Navigation */
.hero-nav-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 10;
  background: none;
  border: none;
  color: white;
  font-size: 4rem;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.25s ease;
}

.hero-nav-btn:hover {
  color: #d32f2f;
}

.hero-prev {
  left: 24px;
}

.hero-next {
  right: 24px;
}

:deep(.swiper-pagination-bullet) {
  width: 8px;
  height: 8px;
  background: rgba(255, 255, 255, .45);
  opacity: 1;
  margin: 0;
  transition: all 0.3s ease;
}

:deep(.swiper-pagination-bullet-active) {
  width: 24px;
  border-radius: 4px;
  background: #d32f2f !important;
}

@media (max-width: 768px) {
  .hero-container {
    height: 520px;
  }
  .hero-content {
    padding-top: 76px;
    padding-bottom: 30px;
  }
}

@media (max-width: 480px) {
  .hero-container {
    height: 480px;
  }
  .hero-content {
    padding-top: 70px;
    padding-bottom: 20px;
  }
}
</style>