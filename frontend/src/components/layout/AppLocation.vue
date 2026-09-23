<template>
  <section class="location-section py-2 position-relative overflow-hidden">
    <div class="mx-auto px-4 px-md-5 position-relative z-2">
      <div class="row g-4 align-items-stretch">

        <!-- Chap taraf: Aloqa va Manzil Ma'lumotlari kartasi -->
        <div class="col-12 col-lg-5 d-flex flex-column justify-content-between">
          <div
            class="info-premium-card p-4 p-md-5 h-100 d-flex flex-column justify-content-between bg-white rounded-4 border-light-premium">

            <div>


              <!-- Sarlavha -->
              <h2 class="display-6 fw-black text-dark-premium mb-4 main-title">Biz bilan bog'lanish</h2>

              <!-- Ma'lumotlar bloki -->
              <div class="d-flex flex-column gap-4 my-4">
                <!-- Manzil -->
                <div class="d-flex align-items-start gap-3">
                  <div class="icon-box d-flex align-items-center justify-content-center rounded-circle flex-shrink-0">
                    <i class="bi bi-geo-alt-fill text-danger"></i>
                  </div>
                  <div>
                    <h6 class="fw-bold text-dark-premium m-0 mb-1">Klinika manzili</h6>
                    <p class="text-muted-premium small m-0 lh-base">Toshkent shahri, Yunusobod tumani, Bog'ishamol
                      ko'chasi, 12-uy</p>
                  </div>
                </div>

                <!-- Ish vaqti -->
                <div class="d-flex align-items-start gap-3">
                  <div class="icon-box d-flex align-items-center justify-content-center rounded-circle flex-shrink-0">
                    <i class="bi bi-clock-fill text-danger"></i>
                  </div>
                  <div>
                    <h6 class="fw-bold text-dark-premium m-0 mb-1">Ish tartibi</h6>
                    <p class="text-muted-premium small m-0 lh-base">Dushanba - Shanba: 24/7 (Tez yordam)<br>Poliklinika:
                      08:00 - 18:00</p>
                  </div>
                </div>

                <!-- Telefon -->
                <div class="d-flex align-items-start gap-3">
                  <div class="icon-box d-flex align-items-center justify-content-center rounded-circle flex-shrink-0">
                    <i class="bi bi-telephone-fill text-danger"></i>
                  </div>
                  <div>
                    <h6 class="fw-bold text-dark-premium m-0 mb-1">Aloqa markazi</h6>
                    <a href="tel:+998781222244"
                      class="text-muted-premium text-decoration-none small m-0 fw-bold fs-6 text-dark-premium">+998
                      (78) 122-22-44</a>
                  </div>
                </div>
              </div>
            </div>

            <!-- Navigatsiya ilovalariga o'tish tugmalari -->
            <div class="pt-4 border-top border-light-subtle d-flex flex-wrap gap-2">
              <a href="https://yandex.uz/maps/org/alfraganus_university_hospital/237760633546/" target="_blank"
                class="btn-nav yandex d-inline-flex align-items-center gap-2 fw-bold text-decoration-none rounded-3 px-3 py-2">
                <i class="bi bi-compass"></i>
                <span>Yandex Navigator</span>
              </a>
              <a href="https://maps.google.com" target="_blank"
                class="btn-nav google d-inline-flex align-items-center gap-2 fw-bold text-decoration-none rounded-3 px-3 py-2">
                <i class="bi bi-google"></i>
                <span>Google Maps</span>
              </a>
            </div>

          </div>
        </div>

        <!-- O'ng taraf: Xarita (Map Wrapper) -->
        <div class="col-12 col-lg-7">
          <div class="map-wrapper h-100 overflow-hidden rounded-4 border-light-premium position-relative">
            <!-- Yandex map widget linklarini yashirish va toza saqlash -->
            <div class="map-clean-container h-100">
              <iframe
                src="https://yandex.uz/map-widget/v1/?ll=69.266621%2C41.373516&mode=search&oid=237760633546&ol=biz&z=16.63"
                width="100%" height="100%" frameborder="0" allowfullscreen="true" class="map-iframe"></iframe>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>
</template>

<style scoped>

.container-custom {
  max-width: 1320px;
}

.info-premium-card {
  box-shadow: 0 10px 30px rgba(15, 23, 42, 0.02) !important;
  transition: transform 0.4s ease, box-shadow 0.4s ease;
}

.main-title {
  font-family: 'Outfit', sans-serif;
  color: var(--primary-dark);
  font-weight: 800;
  letter-spacing: -0.5px;
}

/* Ikonkalar uchun dumaloq boks */
.icon-box {
  width: 44px;
  height: 44px;
  background-color: rgba(220, 53, 69, 0.06);
  font-size: 1.1rem;
}

.text-dark-premium {
  color: var(--primary-dark);
}

.text-muted-premium {
  color: var(--text-muted);
}

/* Navigator Tugmalari */
.btn-nav {
  font-size: 0.85rem;
  transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
  background-color: #f1f5f9;
  color: var(--primary-dark);
}

.btn-nav i {
  font-size: 1rem;
}

.btn-nav.yandex:hover {
  background-color: #fc3f1d;
  color: white;
}

.btn-nav.google:hover {
  background-color: #4285f4;
  color: white;
}

/* Xarita oynasi (Map Wrapper) */
.map-wrapper {
  min-height: 480px;
  box-shadow: 0 15px 35px rgba(15, 23, 42, 0.03) !important;
  transition: border-color 0.4s ease;
}

.map-clean-container {
  width: 100%;
  height: 100%;
  position: relative;
  background-color: #e5e7eb;
}

.map-iframe {
  display: block;
  filter: grayscale(5%) contrast(102%);
  /* Xaritani biroz neytral va premium tonlash */
}

/* Hover interaksiyalari */
.info-premium-card:hover,
.map-wrapper:hover {
  border-color: rgba(220, 53, 69, 0.2) !important;
}

/* Mobil qurilmalar uchun moslashuv */
@media (max-width: 992px) {
  .map-wrapper {
    min-height: 380px;
    height: 380px;
  }

  .info-premium-card {
    padding: 30px !important;
  }
}

@media (max-width: 576px) {
  .map-wrapper {
    min-height: 300px;
    height: 300px;
  }

  .btn-nav {
    width: 100%;
    justify-content: center;
  }
}
</style>