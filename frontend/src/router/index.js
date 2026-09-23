  import { createRouter, createWebHistory } from 'vue-router'

  const router = createRouter({
    history: createWebHistory(),
    routes: [
      // Public Routes
      {
        path: '/',
        name: 'home',
        component: () => import('../pages/public/homepage/HomePage.vue')
      },
      {
        path: '/about',
        name: 'about',
        component: () => import('../pages/public/aboutpage/AboutPage.vue')
      },
      {
        path: '/doctors',
        name: 'doctors',
        component: () => import('../pages/public/doctors/DoctorsPage.vue')
      },
      {
        path: '/doctors/:slug',
        name: 'doctor-detail',
        component: () => import('../pages/public/doctors/DoctorDetail.vue')
      },
      {
        path: '/services',
        name: 'services',
        component: () => import('../pages/public/services/Services.vue')
      },
      {
        path: '/services/:slug',
        name: 'service-detail',
        component: () => import('../pages/public/services/ServiceDetail.vue')
      },
      {
        path: '/news',
        name: 'news',
        component: () => import('../pages/public/news/NewsPage.vue')
      },
      {
        path: '/news/:slug',
        name: 'news-detail',
        component: () => import('../pages/public/news/NewspageDetail.vue')
      },
      {
        path: '/surgeries',
        name: 'surgery',
        component: () => import('../pages/public/surgery/SurgeryPage.vue')
      },
      {
        path: '/surgeries/:slug',
        name: 'surgery-detail',
        component: () => import('../pages/public/surgery/SurgeryDetail.vue')
      },
      {
        path: '/statsionar',
        name: 'statsionar',
        component: () => import('../pages/public/statsionar/StatsionarPage.vue')
      },
      {
        path: '/contact',
        name: 'contact',
        component: () => import('../pages/public/ContactPage.vue')
      },

      // Admin Login Route (YANGI QO'SHILADIGAN QISM)
      {
        path: `/${import.meta.env.VITE_ADMIN_PATH || 'admin'}/login`,
        name: 'admin-login',
        component: () => import('../pages/admin/AdminLogin.vue'), // Fayl manzili o'zingizda qanday bo'lsa shunga moslang
        meta: { guestOnly: true }
      },
      // Change VITE_ADMIN_PATH in frontend/.env to obscure the admin URL.
      {
        path: `/${import.meta.env.VITE_ADMIN_PATH || 'admin'}`,
        component: () => import('../pages/admin/AdminLayout.vue'),
        meta: { requiresAdmin: true },
        children: [
          {
            path: '',
            name: 'admin-dashboard',
            component: () => import('../pages/admin/AdminDashboard.vue'),
            meta: { title: 'Boshqaruv Paneli', icon: 'fas fa-tachograph-digital', showInMenu: true }
          },

          // Specializations (Admin)
          {
            path: 'specializations',
            name: 'admin-specializations',
            component: () => import('../pages/admin/specializations/SpecializationsList.vue'),
            meta: { title: 'Mutaxassisliklar', icon: 'fas fa-stethoscope', showInMenu: true }
          },

          // Doctors (Admin) - Faqat ro'yxat menyuda ko'rinadi
          {
            path: 'doctors',
            name: 'admin-doctors',
            component: () => import('../pages/admin/doctors/DoctorsList.vue'),
            meta: { title: 'Shifokorlar', icon: 'fas fa-user-md', showInMenu: true }
          },
          {
            path: 'doctors/create',
            name: 'admin-doctors-create',
            component: () => import('../pages/admin/doctors/DoctorCreate.vue')
          },
          {
            path: 'doctors/:id',
            name: 'admin-doctors-detail',
            component: () => import('../pages/admin/doctors/DoctorDetail.vue')
          },
          {
            path: 'doctors/:id/edit',
            name: 'admin-doctors-edit',
            component: () => import('../pages/admin/doctors/DoctorUpdate.vue')
          },
          {
            path: 'doctors/:id/treatment-logs/create',
            name: 'admin-doctors-treatment-logs',
            component: () => import('../pages/admin/doctors/TreatmentLogsCreate.vue')
          },

          // News (Admin)
          {
            path: 'news',
            name: 'admin-news',
            component: () => import('../pages/admin/news/NewsList.vue'),
            meta: { title: 'Yangiliklar', icon: 'fas fa-newspaper', showInMenu: true }
          },
          {
            path: 'news/create',
            name: 'admin-news-create',
            component: () => import('../pages/admin/news/NewsCreate.vue')
          },
          {
            path: 'news/:id/edit',
            name: 'admin-news-edit',
            component: () => import('../pages/admin/news/NewsUpdate.vue')
          },

          // Services (Admin)
          {
            path: 'services',
            name: 'admin-services',
            component: () => import('../pages/admin/services/ServicesList.vue'),
            meta: { title: 'Xizmatlar', icon: 'fas fa-gear', showInMenu: true }
          },
          {
            path: 'services/create',
            name: 'admin-services-create',
            component: () => import('../pages/admin/services/ServiceCreate.vue')
          },
          {
            path: 'services/:id/edit',
            name: 'admin-services-edit',
            component: () => import('../pages/admin/services/ServiceUpdate.vue')
          },

          // Statsionar (Admin)
          {
            path: 'statsionar',
            name: 'admin-statsionar',
            component: () => import('../pages/admin/statsionar/StatsionarList.vue'),
            meta: { title: 'Statsionar', icon: 'fas fa-bed', showInMenu: true }
          },
          {
            path: 'statsionar/create',
            name: 'admin-statsionar-create',
            component: () => import('../pages/admin/statsionar/StatsionarCreate.vue')
          },
          {
            path: 'statsionar/:id/edit',
            name: 'admin-statsionar-edit',
            component: () => import('../pages/admin/statsionar/StatsionarUpdate.vue')
          },

          // Surgery (Admin)
          {
            path: 'surgeries',
            name: 'admin-surgeries',
            component: () => import('../pages/admin/surgery/SurgeryList.vue'),
            meta: { title: "Operatsion bo'lim", icon: 'fas fa-briefcase-medical', showInMenu: true }
          },
          {
            path: 'surgeries/create',
            name: 'admin-surgeries-create',
            component: () => import('../pages/admin/surgery/SurgeryCreate.vue')
          },
          {
            path: 'surgeries/:id/edit',
            name: 'admin-surgeries-edit',
            component: () => import('../pages/admin/surgery/SurgeryUpdate.vue')
          },

          // Contacts (Admin)
          {
            path: 'contacts',
            name: 'admin-contacts',
            component: () => import('../pages/admin/AdminContact.vue'),
            meta: { title: 'Murojaatlar', icon: 'fas fa-envelope', showInMenu: true }
          },
          {
            path: 'profile',
            name: 'admin-profile',
            component: () => import('../pages/admin/AdminProfile.vue'),
            meta: { title: 'Profile', icon: 'fas fa-user-gear', showInMenu: true }
          }
        ]
      },

      // 404
      {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('../pages/public/NotFound.vue')
      }
    ],
    scrollBehavior() {
      return { top: 0 }
    }
  })

  // ---------------------------------------------------------------------------
  // Navigation Guard
  // ---------------------------------------------------------------------------

  router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('admin_token')

    // Parse stored user safely; any parse failure is treated as unauthenticated.
    let user = null
    try {
      const raw = localStorage.getItem('admin_user')
      if (raw) user = JSON.parse(raw)
    } catch {
      // Corrupt data — clear storage so the user must log in again.
      localStorage.removeItem('admin_user')
      localStorage.removeItem('admin_token')
    }

    // A user is only considered an admin when BOTH conditions are met:
    //   1. A Sanctum bearer token is present in storage.
    //   2. The cached user object carries role === 'admin'.
    // This prevents an authenticated non-admin from entering the admin area
    // by exploiting the old guard which only checked the token.
    const isAdmin = !!(token && user?.role === 'admin')

    // --- Protected admin area ---
    if (to.meta.requiresAdmin) {
      if (!token) {
        // Not logged in → redirect to login page.
        return next({ name: 'admin-login' })
      }
      if (!isAdmin) {
        // Logged in but wrong role → redirect to public home.
        return next({ name: 'home' })
      }
      return next()
    }

    // --- Guest-only pages (e.g. login screen) ---
    if (to.meta.guestOnly) {
      if (isAdmin) return next({ name: 'admin-dashboard' })
      if (token)   return next({ name: 'home' }) // authenticated but not admin
      return next()
    }

    // --- All other routes are public ---
    next()
  })

  export default router