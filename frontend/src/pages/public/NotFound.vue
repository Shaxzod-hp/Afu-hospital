<template>
  <div
    class="not-found-wrapper min-vh-100 d-flex flex-column align-items-center justify-content-center"
  >
    <div class="content-container text-center p-4 p-md-5 rounded-4">
      <!-- 404 Illyustratsiya rasmi -->
      <img
        src="/not-found.png"
        alt="404 Page Not Found"
        class="img-fluid not-found-img mb-4"
      />

      <p class="text-dark-slate fs-5 mb-4 max-w-400 mx-auto fw-medium">
        Siz izlayotgan sahifa mavjud emas yoki ko'chirilgan bo'lishi mumkin.
      </p>

      <!-- Harakat tugmalari -->
      <div class="d-flex justify-content-center gap-3">
        <router-link
          to="/"
          class="btn btn-primary-custom px-4 py-2.5 rounded-pill fw-bold"
        >
          <i class="fas fa-home me-2"></i>Bosh sahifa
        </router-link>
        <router-link
          to="/contact"
          class="btn btn-outline-custom px-4 py-2.5 rounded-pill fw-bold"
        >
          Bog'lanish
        </router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>
.not-found-wrapper {
  /* Orqa fon uchun tepadagi Navbar matnlarini ajratib ko'rsatuvchi to'qroq, kontrasli fon */
  background: linear-gradient(135deg, #2d5ba5 0%, #0f172a 100%);
  padding: 20px;
}

/* O'rtadagi blok to'qlashgan fon ustida yaqqol ajralib turishi uchun */
.content-container {
  max-width: 580px;
  width: 100%;
}

.not-found-img {
  max-width: 420px;
  width: 100%;
  height: auto;
  object-fit: contain;
}

.text-dark-slate {
  color: white !important;
}

.max-w-400 {
  max-width: 420px;
}

/* Tugmalar stylingi */
.btn-primary-custom {
  background-color: #2563eb;
  color: #ffffff;
  border: none;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
  transition: all 0.25s ease;
}

.btn-primary-custom:hover {
  background-color: #1d4ed8;
  color: #ffffff;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
}

.btn-outline-custom {
  background-color: #f8fafc;
  color: #475569;
  border: 1px solid #cbd5e1;
  transition: all 0.25s ease;
}

.btn-outline-custom:hover {
  background-color: #f1f5f9;
  color: #0f172a;
  border-color: #94a3b8;
  transform: translateY(-2px);
}
</style>
