<template>
  <!--
    SkeletonCard — reusable shimmer placeholder.

    Props:
      variant  "overlay"  (default) — matches .overlay-card: image + text at bottom, 360px tall
               "statsionar"         — stacked gallery + info block
               "detail"             — two-column detail layout (big image + text side)
  -->
  <div :class="['sk-root', `sk-${variant}`]" aria-hidden="true">

    <!-- ── OVERLAY variant (Services & SurgeryPage cards) ── -->
    <template v-if="variant === 'overlay'">
      <div class="sk-overlay-card">
        <!-- image area: full card height -->
        <div class="sk-block sk-img-full"></div>
        <!-- text content pinned to bottom -->
        <div class="sk-overlay-content">
          <div class="sk-block sk-line sk-line-lg mb-2"></div>
          <div class="sk-block sk-line sk-line-md mb-2"></div>
          <div class="sk-block sk-line sk-line-sm mb-3"></div>
          <div class="sk-block sk-btn-pill"></div>
        </div>
      </div>
    </template>

    <!-- ── STATSIONAR variant (stacked gallery + info block) ── -->
    <template v-else-if="variant === 'statsionar'">
      <!-- top gallery row — 2 equal panels, 420px tall -->
      <div class="sk-gallery-top">
        <div class="sk-block sk-gallery-cell"></div>
        <div class="sk-block sk-gallery-cell"></div>
      </div>
      <!-- bottom gallery row — 3 panels, 240px tall -->
      <div class="sk-gallery-bottom">
        <div class="sk-block sk-gallery-cell"></div>
        <div class="sk-block sk-gallery-cell"></div>
        <div class="sk-block sk-gallery-cell"></div>
      </div>
      <!-- package info block -->
      <div class="sk-pkg-info">
        <div class="sk-block sk-line sk-line-lg mb-3"></div>
        <div class="sk-block sk-line sk-line-md mb-2"></div>
        <div class="sk-block sk-line sk-line-full mb-4"></div>
        <!-- checkmark chips -->
        <div class="sk-chips mb-4">
          <div class="sk-block sk-chip"></div>
          <div class="sk-block sk-chip"></div>
          <div class="sk-block sk-chip"></div>
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <div class="sk-block sk-line sk-line-sm" style="width: 90px;"></div>
          <div class="sk-block sk-btn-pill"></div>
        </div>
      </div>
    </template>

    <!-- ── DETAIL variant (SurgeryDetail two-column layout) ── -->
    <template v-else-if="variant === 'detail'">
      <div class="row g-4 g-lg-5">
        <!-- Left: main image + 3 stat boxes -->
        <div class="col-lg-6 d-flex flex-column">
          <div class="sk-block sk-detail-img mb-4"></div>
          <div class="sk-stats-row">
            <div class="sk-block sk-stat-box"></div>
            <div class="sk-block sk-stat-box"></div>
            <div class="sk-block sk-stat-box"></div>
          </div>
        </div>
        <!-- Right: text content card -->
        <div class="col-lg-6">
          <div class="sk-detail-card">
            <div class="sk-block sk-badge mb-3"></div>
            <div class="sk-block sk-line sk-line-xl mb-3"></div>
            <div class="sk-block sk-line sk-line-full mb-2"></div>
            <div class="sk-block sk-line sk-line-full mb-2"></div>
            <div class="sk-block sk-line sk-line-md mb-4"></div>
            <!-- feature grid -->
            <div class="sk-features-grid mb-4">
              <div class="sk-block sk-feature-item"></div>
              <div class="sk-block sk-feature-item"></div>
              <div class="sk-block sk-feature-item"></div>
              <div class="sk-block sk-feature-item"></div>
            </div>
            <!-- price + button -->
            <div class="sk-price-row">
              <div>
                <div class="sk-block sk-line sk-line-sm mb-1" style="width:120px;"></div>
                <div class="sk-block sk-line sk-line-lg" style="width:160px;"></div>
              </div>
              <div class="sk-block sk-btn-pill sk-btn-wide"></div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- ── NEWS variant (yangilik kartasi: rasm + sana + sarlavha + matn) ── -->
    <template v-else-if="variant === 'news'">
      <div class="sk-card">
        <div class="sk-block sk-card-img"></div>
        <div class="sk-card-body">
          <div class="sk-block sk-line sk-line-sm mb-3" style="width: 35%"></div>
          <div class="sk-block sk-line sk-line-xl mb-2"></div>
          <div class="sk-block sk-line sk-line-md mb-4" style="height: 22px"></div>
          <div class="sk-block sk-line sk-line-full mb-2"></div>
          <div class="sk-block sk-line sk-line-full mb-2"></div>
          <div class="sk-block sk-line sk-line-md mb-4"></div>
          <div class="sk-block sk-line sk-line-sm" style="width: 30%"></div>
        </div>
      </div>
    </template>

    <!-- ── DOCTOR variant (shifokor kartasi: portret + ism + mutaxassislik) ── -->
    <template v-else-if="variant === 'doctor'">
      <div class="sk-card">
        <div class="sk-block sk-card-img sk-card-img--portrait"></div>
        <div class="sk-card-body text-center">
          <div class="sk-block sk-line sk-line-lg mx-auto mb-3" style="height: 18px"></div>
          <div class="sk-block sk-line sk-line-md mx-auto mb-4"></div>
          <div class="sk-block sk-btn-pill mx-auto"></div>
        </div>
      </div>
    </template>

    <!-- ── ARTICLE variant (yangilik / xizmat matni sahifasi) ── -->
    <template v-else-if="variant === 'article'">
      <div class="sk-article">
        <div class="sk-block sk-line sk-line-sm mb-3" style="width: 160px"></div>
        <div class="sk-block sk-line mb-2" style="height: 34px; width: 90%"></div>
        <div class="sk-block sk-line mb-4" style="height: 34px; width: 60%"></div>
        <div class="d-flex gap-3 mb-4">
          <div class="sk-block sk-chip"></div>
          <div class="sk-block sk-chip"></div>
          <div class="sk-block sk-chip"></div>
        </div>
        <div class="sk-block sk-article-img mb-4"></div>
        <div v-for="n in 6" :key="n" class="sk-block sk-line mb-3" :style="{ width: n % 3 === 0 ? '65%' : '100%' }"></div>
      </div>
    </template>

  </div>
</template>

<script setup>
defineProps({
  variant: {
    type: String,
    default: 'overlay',
    validator: (v) =>
      ['overlay', 'statsionar', 'detail', 'news', 'doctor', 'article'].includes(v),
  },
})
</script>

<style scoped>
/* Base "block" — ranglar main.css dagi --sk-base / --sk-shine (light va dark) */
.sk-block {
  background: linear-gradient(90deg, var(--sk-base) 25%, var(--sk-shine) 50%, var(--sk-base) 75%);
  background-size: 200% 100%;
  animation: sk-shimmer 1.4s ease-in-out infinite;
  border-radius: 8px;
  flex-shrink: 0;
}

@media (prefers-reduced-motion: reduce) {
  .sk-block { animation: none; }
}

/* ─────────────────────────────────────────────
   SHARED TEXT-LINE SIZES
───────────────────────────────────────────── */
.sk-line      { height: 14px; }
.sk-line-sm   { width: 45%; }
.sk-line-md   { width: 70%; }
.sk-line-lg   { width: 85%; }
.sk-line-xl   { height: 22px; width: 75%; }
.sk-line-full { width: 100%; }

/* Button-shaped block */
.sk-btn-pill {
  height: 34px;
  width: 110px;
  border-radius: 50px;
}
.sk-btn-wide {
  width: 140px;
}

/* ─────────────────────────────────────────────
   OVERLAY VARIANT  (height: 360px)
   Matches .overlay-card in Services & SurgeryPage
───────────────────────────────────────────── */
.sk-overlay-card {
  position: relative;
  height: 360px;
  border-radius: 22px;
  overflow: hidden;
}

/* The full-card image skeleton */
.sk-img-full {
  position: absolute;
  inset: 0;
  border-radius: 22px;
}

/* Text content pinned to bottom, mirroring .overlay-content */
.sk-overlay-content {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  padding: 22px;
  /* semi-transparent backdrop so lines are visible against the image skeleton */
  background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.18) 100%);
  border-radius: 0 0 22px 22px;
}
.sk-overlay-content .sk-block {
  /* lines inside overlay: slightly lighter to stand out */
  background: linear-gradient(
    90deg,
    rgba(255,255,255,0.22) 25%,
    rgba(255,255,255,0.45) 50%,
    rgba(255,255,255,0.22) 75%
  );
  background-size: 200% 100%;
}

@media (max-width: 768px) {
  .sk-overlay-card { height: 300px; }
}

/* ─────────────────────────────────────────────
   STATSIONAR VARIANT
   Mirrors gallery-top-row (420px), gallery-bottom-row (240px)
   and package-info block
───────────────────────────────────────────── */
.sk-statsionar {
  margin-bottom: 40px;
}

.sk-gallery-top {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  height: 420px;
  margin-bottom: 12px;
}

.sk-gallery-bottom {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  height: 240px;
  margin-bottom: 24px;
}

.sk-gallery-cell {
  border-radius: 16px;
  height: 100%;
}

.sk-pkg-info {
  padding: 0 4px;
}

.sk-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.sk-chip {
  height: 28px;
  width: 100px;
  border-radius: 50px;
}

@media (max-width: 991px) {
  .sk-gallery-top    { height: 320px; }
  .sk-gallery-bottom { height: 180px; }
}

@media (max-width: 768px) {
  .sk-gallery-top {
    grid-template-columns: 1fr;
    height: auto;
  }
  .sk-gallery-top .sk-gallery-cell { height: 240px; }
  .sk-gallery-bottom { height: 130px; }
}

@media (max-width: 576px) {
  .sk-gallery-bottom {
    grid-template-columns: repeat(2, 1fr);
    height: auto;
  }
  .sk-gallery-bottom .sk-gallery-cell { height: 140px; }
}

/* ─────────────────────────────────────────────
   DETAIL VARIANT
   Left: image height 380px (.card-media-wrap) + 3 stat boxes
   Right: white card with feature grid + price row
───────────────────────────────────────────── */
.sk-detail-img {
  height: 380px;
  border-radius: 20px;
}

.sk-stats-row {
  display: flex;
  gap: 12px;
  margin-top: auto;
}

.sk-stat-box {
  flex: 1;
  height: 100px;
  border-radius: 16px;
}

.sk-detail-card {
  background: var(--clr-surface);
  border: 1px solid var(--clr-border);
  border-radius: 16px;
  padding: 32px;
  display: flex;
  flex-direction: column;
  height: 100%;
  box-sizing: border-box;
}

.sk-badge {
  height: 28px;
  width: 150px;
  border-radius: 50px;
}

.sk-features-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.sk-feature-item {
  height: 52px;
  border-radius: 10px;
}

.sk-price-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  padding: 16px;
  background: var(--clr-faint);
  border-radius: 16px;
  margin-top: auto;
}

@media (max-width: 991px) {
  .sk-detail-img { height: 300px; }
}

/* ─────────────────────────────────────────────
   NEWS / DOCTOR CARD VARIANTS
───────────────────────────────────────────── */
.sk-card {
  background: var(--clr-surface);
  border: 1px solid var(--clr-border);
  border-radius: 20px;
  overflow: hidden;
  height: 100%;
}

.sk-card-img {
  height: 220px;
  border-radius: 0;
}

.sk-card-img--portrait {
  height: 300px;
}

.sk-card-body {
  padding: 22px;
}

/* ─────────────────────────────────────────────
   ARTICLE VARIANT
───────────────────────────────────────────── */
.sk-article-img {
  height: 380px;
  border-radius: 20px;
}

@media (max-width: 576px) {
  .sk-card-img { height: 190px; }
  .sk-card-img--portrait { height: 260px; }
  .sk-article-img { height: 220px; }
  .sk-card-body { padding: 18px; }
}
</style>
