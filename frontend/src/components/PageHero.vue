<template>
  <section class="page-hero" :class="`page-hero--${size}`">
    <video v-if="video" class="page-hero-video" autoplay muted loop playsinline>
      <source :src="videoSrc" type="video/mp4" />
    </video>
    <div class="hero-overlay"></div>
    <div class="container position-relative hero-content">
      <span v-if="subtitle" class="hero-subtitle">{{ subtitle }}</span>
      <h1 class="hero-title">{{ title }}</h1>
      <p v-if="description" class="hero-desc">{{ description }}</p>
    </div>
  </section>
</template>

<script setup>
defineProps({
  subtitle: { type: String, default: "" },
  title: { type: String, required: true },
  description: { type: String, default: "" },
  video: { type: Boolean, default: true },
  videoSrc: { type: String, default: "/bg-videoo.mp4" },
  // 'large'   — list/landing pages (Services, Statsionar, News)
  // 'compact' — detail pages (ServiceDetail, DoctorDetail, etc.)
  size: { type: String, default: "large" },
});
</script>

<style scoped>
/* ── HERO ── */
.page-hero {
  position: relative;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  text-align: center;
  overflow: hidden;
  background-color: #002b87;
}

[data-theme="dark"] .page-hero {
  background-color: #0f172a;
}

.page-hero--large {
  min-height: 420px;
  padding: 130px 0 90px;
}

.page-hero--compact {
  min-height: 280px;
  padding: 120px 0 60px;
}

.page-hero-video {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  z-index: 0;
  pointer-events: none;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    135deg,
    rgba(0, 43, 135, 0.8) 0%,
    rgba(0, 20, 70, 0.85) 100%
  );
  z-index: 1;
  pointer-events: none;
}

.hero-content {
  z-index: 2;
  padding: 0 12px;
}

.hero-subtitle {
  font-size: 0.8rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 3px;
  color: rgba(255, 255, 255, 0.85);
  display: inline-block;
  margin-bottom: 12px;
}

.page-hero--large .hero-title {
  font-size: clamp(1.8rem, 5vw, 3rem);
}

.page-hero--compact .hero-title {
  font-size: clamp(1.6rem, 4vw, 2.6rem);
}

.hero-title {
  font-weight: 800;
  font-family: "Outfit", sans-serif;
  color: #ffffff;
  margin: 0 0 10px;
  word-break: break-word;
}

.hero-desc {
  color: rgba(255, 255, 255, 0.9);
  font-size: 1rem;
  line-height: 1.7;
  max-width: 700px;
  margin: 0 auto;
}

/* ── Tablet (≤991px) ── */
@media (max-width: 991px) {
  .page-hero--large {
    min-height: 340px;
    padding: 110px 0 70px;
  }
  .page-hero--compact {
    min-height: 240px;
    padding: 100px 0 50px;
  }
}

/* ── Small tablet (≤768px) ── */
@media (max-width: 768px) {
  .page-hero--large {
    min-height: 300px;
    padding: 100px 0 55px;
  }
  .page-hero-video {
    object-position: center 25%;
  }
}

/* ── Mobile (≤576px) ── */
@media (max-width: 576px) {
  .page-hero--large {
    min-height: 260px;
    padding: 90px 16px 50px;
  }
  .page-hero--compact {
    min-height: 200px;
    padding: 80px 16px 40px;
  }
  .page-hero-video {
    object-position: center 30%;
  }
  .hero-desc {
    font-size: 0.88rem;
  }
}
</style>
