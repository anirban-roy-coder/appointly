<template>
  <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
      <div class="flex items-center justify-between h-16">
        <!-- Logo -->
        <RouterLink to="/dashboard" class="flex items-center gap-2 no-underline">
          <svg width="32" height="32" viewBox="0 0 32 32" fill="none" aria-label="AppointLy">
            <rect width="32" height="32" rx="8" fill="#0d9488"/>
            <rect x="8" y="10" width="16" height="14" rx="2" stroke="white" stroke-width="1.5" fill="none"/>
            <path d="M11 8v4M21 8v4M8 15h16" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="12" cy="20" r="1.5" fill="white"/>
            <circle cx="16" cy="20" r="1.5" fill="white"/>
            <circle cx="20" cy="20" r="1.5" fill="white"/>
          </svg>
          <span class="font-semibold text-gray-900 text-lg">AppointLy</span>
        </RouterLink>

        <!-- Desktop Nav Links -->
        <div class="hidden sm:flex items-center gap-1">
          <RouterLink
            v-for="link in navLinks"
            :key="link.to"
            :to="link.to"
            class="px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150"
            :class="$route.path.startsWith(link.to)
              ? 'bg-primary-50 text-primary-700'
              : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
          >{{ link.label }}</RouterLink>
        </div>

        <!-- User + Logout -->
        <div class="flex items-center gap-3">
          <span class="hidden sm:block text-sm text-gray-500">
            Hi, <span class="font-medium text-gray-800">{{ auth.user?.name?.split(' ')[0] }}</span>
          </span>
          <button @click="handleLogout" class="btn-secondary text-sm py-1.5 px-3">Logout</button>
        </div>
      </div>
    </div>

    <!-- Mobile bottom nav -->
    <div class="sm:hidden border-t border-gray-100 flex">
      <RouterLink
        v-for="link in navLinks"
        :key="link.to"
        :to="link.to"
        class="flex-1 text-center py-2.5 text-xs font-medium transition-colors"
        :class="$route.path.startsWith(link.to)
          ? 'text-primary-700 bg-primary-50'
          : 'text-gray-500 hover:text-gray-700'"
      >{{ link.label }}</RouterLink>
    </div>
  </nav>
</template>

<script setup>
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/store/auth.js'

const auth   = useAuthStore()
const router = useRouter()
const $route = useRoute()

const navLinks = [
  { to: '/dashboard', label: 'Dashboard' },
  { to: '/book',      label: 'Book' },
  { to: '/profile',   label: 'Profile' },
]

function handleLogout() {
  auth.logout()
  router.push('/login')
}
</script>
