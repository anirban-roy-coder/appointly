<template>
  <div class="max-w-2xl mx-auto px-4 sm:px-6 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Profile</h1>

    <div v-if="loading" class="card animate-pulse space-y-4">
      <div class="flex items-center gap-4">
        <div class="w-16 h-16 rounded-full bg-gray-200 shrink-0"></div>
        <div class="flex-1 space-y-2">
          <div class="h-5 bg-gray-200 rounded w-1/3"></div>
          <div class="h-4 bg-gray-200 rounded w-1/2"></div>
        </div>
      </div>
      <div class="h-px bg-gray-200"></div>
      <div class="space-y-3">
        <div class="h-4 bg-gray-200 rounded w-2/3"></div>
        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
      </div>
    </div>

    <div v-else-if="user" class="space-y-4">
      <!-- Profile card -->
      <div class="card">
        <div class="flex items-center gap-4 pb-5 mb-5 border-b border-gray-100">
          <div class="w-14 h-14 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 text-xl font-bold shrink-0 select-none">
            {{ user.name?.charAt(0).toUpperCase() }}
          </div>
          <div>
            <h2 class="text-lg font-semibold text-gray-900">{{ user.name }}</h2>
            <p class="text-gray-400 text-sm">Member since {{ formatDate(user.created_at?.split(' ')[0]) }}</p>
          </div>
        </div>

        <dl class="space-y-3">
          <div class="flex items-start gap-3">
            <dt class="w-16 text-xs font-semibold text-gray-400 uppercase tracking-wide pt-0.5 shrink-0">Email</dt>
            <dd class="text-gray-800 text-sm break-all">{{ user.email }}</dd>
          </div>
          <div class="flex items-start gap-3">
            <dt class="w-16 text-xs font-semibold text-gray-400 uppercase tracking-wide pt-0.5 shrink-0">Phone</dt>
            <dd class="text-gray-800 text-sm">{{ user.phone || '—' }}</dd>
          </div>
          <div class="flex items-start gap-3">
            <dt class="w-16 text-xs font-semibold text-gray-400 uppercase tracking-wide pt-0.5 shrink-0">ID</dt>
            <dd class="text-gray-400 text-xs font-mono pt-0.5">#{{ user.id }}</dd>
          </div>
        </dl>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-3 gap-3">
        <div class="card text-center py-5">
          <div class="text-2xl font-bold text-primary-600 tabular-nums">{{ stats.total }}</div>
          <div class="text-xs text-gray-400 mt-1 uppercase tracking-wide">Total</div>
        </div>
        <div class="card text-center py-5">
          <div class="text-2xl font-bold text-yellow-500 tabular-nums">{{ stats.pending }}</div>
          <div class="text-xs text-gray-400 mt-1 uppercase tracking-wide">Pending</div>
        </div>
        <div class="card text-center py-5">
          <div class="text-2xl font-bold text-red-400 tabular-nums">{{ stats.cancelled }}</div>
          <div class="text-xs text-gray-400 mt-1 uppercase tracking-wide">Cancelled</div>
        </div>
      </div>

      <!-- Quick action -->
      <RouterLink to="/book" class="btn-primary w-full justify-center">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M12 5v14M5 12h14" stroke-width="2.5" stroke-linecap="round"/>
        </svg>
        Book a new appointment
      </RouterLink>
    </div>

    <!-- Footer credits -->
    <footer class="mt-10 pt-6 border-t border-gray-100 text-center">
      <p class="text-sm font-medium text-gray-700">Suprotik Mukherjee</p>
      <div class="flex justify-center gap-6 mt-1 text-xs text-gray-400">
        <a href="https://github.com/suprotik" target="_blank" rel="noopener noreferrer"
          class="hover:text-gray-700 transition-colors inline-flex items-center gap-1">
          <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
          </svg>
          GitHub
        </a>
        <a href="https://linkedin.com/in/suprotik" target="_blank" rel="noopener noreferrer"
          class="hover:text-gray-700 transition-colors inline-flex items-center gap-1">
          <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
          </svg>
          LinkedIn
        </a>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { authService }        from '@/services/authService.js'
import { appointmentService } from '@/services/appointmentService.js'
import { formatDate }         from '@/utils/helpers.js'

const user    = ref(null)
const loading = ref(true)
const stats   = ref({ total: 0, pending: 0, cancelled: 0 })

onMounted(async () => {
  try {
    const [profileRes, apptRes] = await Promise.all([
      authService.profile(),
      appointmentService.getAll(),
    ])
    user.value = profileRes.data
    const appts = apptRes.data ?? []
    stats.value = {
      total:     appts.length,
      pending:   appts.filter(a => a.status === 'pending').length,
      cancelled: appts.filter(a => a.status === 'cancelled').length,
    }
  } catch (e) {
    console.error('Profile load failed:', e)
  } finally {
    loading.value = false
  }
})
</script>
