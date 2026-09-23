<template>
  <div class="about-page-wrapper min-vh-100 pb-5">
    <!-- HERO BANNER -->
    <section class="page-hero">
      <video class="page-hero-video" autoplay muted loop playsinline>
        <source src="/bg-videoo.mp4" type="video/mp4" />
      </video>
      <div class="page-hero-overlay"></div>
      <div class="container position-relative z-2 text-center">
        <h1 class="animate-fadeUp fw-bold">
          <span class="text-danger">ALFRAGANUS</span> UNIVERSITY HOSPITAL
        </h1>
        <p class="lead animate-fadeUp opacity-90 mx-auto max-w-700">
          Zamonaviy tibbiyotning ilg'or yutuqlari, innovatsion texnologiyalar va
          xalqaro standartlar birlashgan ko'p tarmoqli tibbiyot markazi.
        </p>
      </div>
    </section>

    <!-- CONTENT -->
    <div class="container my-4 my-lg-5">
      <div class="row align-items-center g-4">
        <!-- YOUTUBE VIDEO -->
        <div class="col-12">
          <div class="video-card rounded-4 overflow-hidden shadow-sm">
            <iframe
              src="https://www.youtube.com/embed/3qGjQBA_CW4"
              title="YouTube video player"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
              class="w-100 d-block"
            ></iframe>
          </div>
        </div>

        <!-- ABOUT TEXT -->
        <div class="col-12 text-start mt-4">
          <h2 class="title fw-bold mb-3 mb-md-4" style="color: #002b87">
            Alfraganus University Hospital
          </h2>
          <p class="text-secondary mb-3">
            <span class="text-danger fw-bold"
              >Alfraganus University Hospital</span
            >
            — zamonaviy tibbiyotning ilg‘or yutuqlari, innovatsion
            texnologiyalar va xalqaro standartlarni o‘zida mujassam etgan ko‘p
            tarmoqli tibbiyot markazidir.
          </p>
          <p class="text-secondary mb-3">
            Klinikamiz aholiga yuqori sifatli diagnostika, davolash,
            profilaktika va reabilitatsiya xizmatlarini ko‘rsatish maqsadida
            tashkil etilgan bo‘lib, bemorlar salomatligi va hayot sifatini
            yaxshilashni o‘zining ustuvor vazifasi deb biladi.
          </p>
          <p class="text-secondary mb-3">
            Klinikada tajribali shifokorlar, professor-o‘qituvchilar, malakali
            tibbiyot xodimlari hamda zamonaviy tibbiy uskunalar birlashgan. Bu
            esa kasalliklarni erta aniqlash, aniq tashxis qo‘yish va samarali
            davolash imkoniyatini yaratadi.
          </p>
          <p class="text-secondary lh-lg mb-0">
            <span class="text-danger fw-bold"
              >Klinikamizning asosiy maqsadi</span
            >
            — bemorlarga zamonaviy diagnostika va samarali davolash usullarini
            taqdim etish, ilg‘or jarrohlik amaliyotlarini qo‘llash hamda har bir
            bemorni individual kuzatuv va parvarish bilan ta’minlashdir. Biz
            xalqaro standartlarga mos yuqori sifatli tibbiy xizmat ko‘rsatish,
            bemorlar xavfsizligi va ularning sog‘lig‘ini tiklashni o‘z
            faoliyatimizning ustuvor yo‘nalishi deb bilamiz.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* ── HERO VIDEO BANNER ── */
.page-hero {
  position: relative;
  overflow: hidden;
  height: 450px;
  min-height: 450px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  background-color: #002b87;
}

.page-hero-video {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 100%;
  height: 100%;
  min-width: 100%;
  min-height: 100%;
  object-fit: cover;
  transform: translate(-50%, -50%);
  z-index: 0;
  pointer-events: none;
}

.page-hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    135deg,
    rgba(0, 43, 135, 0.75) 0%,
    rgba(0, 20, 70, 0.8) 100%
  );
  z-index: 1;
}

.page-hero .container {
  position: relative;
  z-index: 2;
}

.max-w-700 {
  max-width: 700px;
}

/* ── YOUTUBE VIDEO CARD ── */
.video-card iframe {
  width: 100%;
  height: 450px;
  border: none;
}

/* ── TABLET RESPONSIVE ── */
@media (max-width: 991px) {
  .video-card iframe {
    height: 360px;
  }
}

/* ── MOBILE RESPONSIVE ── */
@media (max-width: 768px) {
  .page-hero {
    height: 320px !important;
    min-height: 320px !important;
    padding: 0 15px;
  }

  .page-hero .container h1 {
    font-size: 1.5rem;
    line-height: 1.3;
  }

  .page-hero .container p {
    font-size: 0.9rem;
    line-height: 1.4;
  }

  .video-card iframe {
    height: 260px;
  }
}

@media (max-width: 576px) {
  .video-card iframe {
    height: 210px;
  }
}
</style>
