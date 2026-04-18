import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/store/auth.js'

import LoginPage       from '@/pages/LoginPage.vue'
import RegisterPage    from '@/pages/RegisterPage.vue'
import DashboardPage   from '@/pages/DashboardPage.vue'
import BookAppointment from '@/pages/BookAppointmentPage.vue'
import ProfilePage     from '@/pages/ProfilePage.vue'

const routes = [
  { path: '/',          redirect: '/dashboard' },
  { path: '/login',     component: LoginPage,       meta: { guest: true } },
  { path: '/register',  component: RegisterPage,    meta: { guest: true } },
  { path: '/dashboard', component: DashboardPage,   meta: { auth: true } },
  { path: '/book',      component: BookAppointment, meta: { auth: true } },
  { path: '/book/:id',  component: BookAppointment, meta: { auth: true } },
  { path: '/profile',   component: ProfilePage,     meta: { auth: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, _from, next) => {
  const auth = useAuthStore()
  if (to.meta.auth && !auth.isLoggedIn) return next('/login')
  if (to.meta.guest && auth.isLoggedIn) return next('/dashboard')
  next()
})

export default router
