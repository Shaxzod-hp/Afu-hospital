<template>
  <footer class="footer-luxe pt-5 pb-4">
    <div class="footer-top-line"></div>

    <div class="container container-custom">
      <div class="row gy-5 mb-5">
        <!-- 1-Ustun: Brend va Shior -->
        <div class="col-12 col-lg-4 pe-lg-5">
          <div class="footer-brand mb-4">
            <router-link
              to="/"
              class="d-flex align-items-center gap-2 text-decoration-none"
            >
              <img
                src="/logo-white.png"
                alt="Alfraganus Hospital"
                class="footer-logo"
              />
            </router-link>
          </div>
          <p class="footer-about-text mb-4">
            Alfraganus University Hospital – yuksak texnologiyalar va malakali
            mutaxassislar orqali o'z sog'lig'ingizni ishonchli qo'llarga
            topshirish imkoniyati. Premium xizmat, mukammal natija.
          </p>

          <!-- Ijtimoiy tarmoqlar -->
          <div class="social-links d-flex gap-2">
            <a
              v-for="social in socialLinks"
              :key="social.name"
              :href="social.url"
              class="social-btn-luxe"
              :class="social.name.toLowerCase()"
              :title="social.name"
              target="_blank"
            >
              <i :class="social.icon"></i>
            </a>
          </div>
        </div>

        <!-- 2-Ustun: Tezkor Linklar -->
        <div class="col-6 col-md-4 col-lg-2">
          <h6 class="footer-heading mb-4">Klinika</h6>
          <ul class="list-unstyled footer-nav">
            <li v-for="link in clinicLinks" :key="link.path" class="mb-2">
              <router-link
                :to="link.path"
                class="footer-link d-inline-flex align-items-center"
              >
                <span class="link-dot"
                  ><i class="bi bi-chevron-right"></i
                ></span>
                <span class="link-text">{{ link.name }}</span>
              </router-link>
            </li>
          </ul>
        </div>

        <!-- 3-Ustun: Mutaxassisliklar -->
        <div class="col-12 col-md-4 col-lg-3">
          <h6 class="footer-heading mb-4">Bizning mutaxassislar</h6>
          <div class="row g-2">
            <!-- 1-Ustun -->
            <div class="col-6">
              <ul class="list-unstyled footer-nav">
                <li
                  v-for="(spec, index) in firstColumnSpecs"
                  :key="
                    typeof spec === 'object'
                      ? spec.id || spec.slug || index
                      : index
                  "
                  class="mb-2"
                >
                  <router-link
                    :to="{
                      path: '/doctors',
                      query: {
                        specialization:
                          typeof spec === 'object'
                            ? spec.slug || spec.id || spec.name
                            : spec,
                      },
                    }"
                    class="footer-link d-inline-flex align-items-center"
                  >
                    <span class="link-dot"
                      ><i class="bi bi-chevron-right"></i
                    ></span>
                    <span
                      class="link-text text-truncate"
                      style="max-width: 110px"
                    >
                      {{
                        typeof spec === "object"
                          ? spec.name || spec.title
                          : spec
                      }}
                    </span>
                  </router-link>
                </li>
              </ul>
            </div>

            <!-- 2-Ustun -->
            <div class="col-6">
              <ul class="list-unstyled footer-nav">
                <li
                  v-for="(spec, index) in secondColumnSpecs"
                  :key="
                    typeof spec === 'object'
                      ? spec.id || spec.slug || index
                      : index
                  "
                  class="mb-2"
                >
                  <router-link
                    :to="{
                      path: '/doctors',
                      query: {
                        specialization:
                          typeof spec === 'object'
                            ? spec.slug || spec.id || spec.name
                            : spec,
                      },
                    }"
                    class="footer-link d-inline-flex align-items-center"
                  >
                    <span class="link-dot"
                      ><i class="bi bi-chevron-right"></i
                    ></span>
                    <span
                      class="link-text text-truncate"
                      style="max-width: 110px"
                    >
                      {{
                        typeof spec === "object"
                          ? spec.name || spec.title
                          : spec
                      }}
                    </span>
                  </router-link>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- 4-Ustun: Aloqa -->
        <div class="col-12 col-md-4 col-lg-3">
          <h6 class="footer-heading mb-4">Bog'lanish</h6>
          <div class="contact-info-wrapper">
            <div class="contact-item-luxe mb-3">
              <div class="contact-icon-box">
                <i class="bi bi-geo-alt-fill"></i>
              </div>
              <span class="contact-text"
                >Toshkent sh., Yunusobod tumani, Yuqori Qoraqamish ko'chasi,
                5-uy</span
              >
            </div>

            <div class="contact-item-luxe mb-3">
              <div class="contact-icon-box">
                <i class="bi bi-telephone-fill"></i>
              </div>
              <a href="tel:+998781222244" class="contact-text text-link"
                >+998 78 122 22 44</a
              >
            </div>

            <div class="contact-item-luxe">
              <div class="contact-icon-box">
                <i class="bi bi-envelope-at-fill"></i>
              </div>
              <a
                href="mailto:info@alfraganushospital.uz"
                class="contact-text text-link"
                >info@alfraganushospital.uz</a
              >
            </div>
          </div>
        </div>
      </div>

      <!-- Mualliflik huquqi -->
      <div class="footer-bottom pt-4 mt-5">
        <div class="row align-items-center">
          <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
            <p class="text-footer-muted small m-0">
              &copy; {{ new Date().getFullYear() }} Alfraganus University
              Hospital. Barcha huquqlar himoylangan.
            </p>
          </div>
          <div class="col-md-6 text-center text-md-end">
            <div class="dev-credit">
              <span class="text-footer-muted small">Crafted with ❤️ by </span>
              <a
                href="https://shaxzod-portfolio-uz.netlify.app/#/"
                target="_blank"
                class="designer-link fw-bold text-decoration-none"
              >
                Isomiddinov.uz
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { useDoctorsStore } from "../../stores/doctors"; // store yo'lingizni tekshiring

const doctorsStore = useDoctorsStore();

const clinicLinks = [
  { name: "Bosh sahifa", path: "/" },
  { name: "Biz haqimizda", path: "/about" },
  { name: "Xizmatlar", path: "/services" },
  { name: "Jarrohlik bo'limi", path: "/surgeries" },
  { name: "Yangiliklar", path: "/news" },
  { name: "Aloqa", path: "/contact" },
];

// Store'dagi 'specialties' state'ini xavfsiz olish
const specializationsList = computed(() => {
  return Array.isArray(doctorsStore.specialties)
    ? doctorsStore.specialties
    : [];
});

// Ro'yxatni ikkita ustunga teng bo'lish
const halfLength = computed(() =>
  Math.ceil(specializationsList.value.length / 2)
);

const firstColumnSpecs = computed(() =>
  specializationsList.value.slice(0, halfLength.value)
);

const secondColumnSpecs = computed(() =>
  specializationsList.value.slice(halfLength.value)
);

const socialLinks = [
  { name: "Facebook", icon: "bi bi-facebook", url: "#" },
  {
    name: "Instagram",
    icon: "bi bi-instagram",
    url: "http://www.instagram.com/alfraganusuniversity_hospital",
  },
  {
    name: "Telegram",
    icon: "bi bi-telegram",
    url: "http://t.me/ALFRAGANUS_UNIVERSITY_HOSPITAL",
  },
  { name: "YouTube", icon: "bi bi-youtube", url: "#" },
];

onMounted(async () => {
  if (typeof doctorsStore.fetchSpecialties === "function") {
    await doctorsStore.fetchSpecialties();
  }
});
</script>

<style scoped>
.footer-luxe {
  background: rgb(45, 46, 123) !important;
  color: #f8fafc;
  position: relative;
  z-index: 2;
  font-family: "Inter", sans-serif;
}

.footer-top-line {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 2px;
  background: rgba(255, 255, 255, 0.15);
}

.footer-logo {
  height: 46px;
  object-fit: contain;
}

.footer-about-text {
  color: rgba(255, 255, 255, 0.75);
  font-size: 0.9rem;
  line-height: 1.65;
}

.footer-heading {
  font-family: "Outfit", sans-serif;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-size: 0.8rem;
  color: #ffffff;
  position: relative;
}

.footer-link {
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  font-size: 0.88rem;
  padding: 4px 0;
  display: inline-flex;
  align-items: center;
  position: relative;
  transition: color 0.3s ease;
}

.link-dot {
  display: inline-flex;
  align-items: center;
  width: 0px;
  opacity: 0;
  transform: translateX(-5px);
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.9);
  transition: width 0.3s ease, opacity 0.3s ease, transform 0.3s ease;
}

.link-text {
  transition: transform 0.3s ease;
}

.footer-link:hover {
  color: #ffffff;
}

.footer-link:hover .link-dot {
  width: 14px;
  opacity: 1;
  transform: translateX(0);
}

.footer-link:hover .link-text {
  transform: translateX(2px);
}

.contact-info-wrapper {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.contact-item-luxe {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.contact-icon-box {
  width: 32px;
  height: 32px;
  background: rgba(255, 255, 255, 0.08);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  font-size: 0.95rem;
  flex-shrink: 0;
}

.contact-text {
  font-size: 0.88rem;
  color: rgba(255, 255, 255, 0.85);
  line-height: 1.4;
}

.text-link {
  text-decoration: none;
  transition: color 0.25s ease;
}

.text-link:hover {
  color: #ffffff;
  text-decoration: underline;
}

.social-btn-luxe {
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgba(255, 255, 255, 0.8);
  font-size: 1.1rem;
  transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
  text-decoration: none;
}

.social-btn-luxe:hover {
  color: #ffffff;
  transform: translateY(-3px);
}

.social-btn-luxe.telegram:hover {
  background-color: #0088cc;
  border-color: #0088cc;
  box-shadow: 0 8px 20px rgba(0, 136, 204, 0.35);
}

.social-btn-luxe.instagram:hover {
  background: linear-gradient(
    45deg,
    #f09433 0%,
    #e6683c 25%,
    #dc2743 50%,
    #cc2366 75%,
    #bc1888 100%
  );
  border-color: transparent;
  box-shadow: 0 8px 20px rgba(220, 39, 67, 0.35);
}

.social-btn-luxe.facebook:hover {
  background-color: #1877f2;
  border-color: #1877f2;
  box-shadow: 0 8px 20px rgba(24, 119, 242, 0.35);
}

.social-btn-luxe.youtube:hover {
  background-color: #ff0000;
  border-color: #ff0000;
  box-shadow: 0 8px 20px rgba(255, 0, 0, 0.35);
}

.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.text-footer-muted {
  color: rgba(255, 255, 255, 0.5);
}

.designer-link {
  color: rgba(255, 255, 255, 0.7);
  transition: color 0.25s ease;
}

.designer-link:hover {
  color: #ffffff;
}

.container-custom {
  max-width: 1320px;
}
</style>
