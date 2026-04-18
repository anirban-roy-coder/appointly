<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-12">
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <div class="flex justify-center mb-3">
          <svg width="48" height="48" viewBox="0 0 32 32" fill="none" aria-label="AppointEase">
            <rect width="32" height="32" rx="8" fill="#0d9488"/>
            <rect x="8" y="10" width="16" height="14" rx="2" stroke="white" stroke-width="1.5" fill="none"/>
            <path d="M11 8v4M21 8v4M8 15h16" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="12" cy="20" r="1.5" fill="white"/>
            <circle cx="16" cy="20" r="1.5" fill="white"/>
            <circle cx="20" cy="20" r="1.5" fill="white"/>
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">AppointLy</h1>
        <p class="text-gray-500 mt-1 text-sm">Sign in to manage your appointment</p>
      </div>

      <div class="card">
        <h2 class="text-lg font-semibold text-gray-900 mb-5">Welcome back</h2>

        <div
          v-if="errorMsg"
          class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-4 flex items-center gap-2"
        >
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="9" stroke-width="2"/>
            <path d="M12 8v4M12 16h.01" stroke-width="2" stroke-linecap="round"/>
          </svg>
          {{ errorMsg }}
        </div>

        <form @submit.prevent="handleLogin" class="space-y-4" novalidate>
          <div>
            <label class="form-label" for="email">Email</label>
            <input
              id="email" v-model.trim="form.email" type="email" class="input-field"
              placeholder="you@example.com" autocomplete="email" required
            />
          </div>
          <div>
            <label class="form-label" for="password">Password</label>
            <div class="relative">
              <input
                id="password" v-model="form.password"
                :type="showPw ? 'text' : 'password'"
                class="input-field pr-10"
                placeholder="••••••••"
                autocomplete="current-password" required
              />
              <button
                type="button"
                @click="showPw = !showPw"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                :aria-label="showPw ? 'Hide password' : 'Show password'"
              >
                <svg v-if="!showPw" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke-width="2"/>
                  <circle cx="12" cy="12" r="3" stroke-width="2"/>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24" stroke-width="2" stroke-linecap="round"/>
                  <line x1="1" y1="1" x2="23" y2="23" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </button>
            </div>
          </div>
          <button type="submit" class="btn-primary w-full" :disabled="loading">
            <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            {{ loading ? 'Signing in…' : 'Sign in' }}
          </button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-600">
          No account?
          <RouterLink to="/register" class="text-primary-600 hover:text-primary-700 font-medium">Create one</RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '@/store/auth.js'

const auth     = useAuthStore()
const router   = useRouter()
const loading  = ref(false)
const errorMsg = ref('')
const showPw   = ref(false)
const form     = reactive({ email: '', password: '' })

async function handleLogin() {
  loading.value  = true
  errorMsg.value = ''
  try {
    await auth.login(form.email, form.password)
    router.push('/dashboard')
  } catch (err) {
    errorMsg.value = err.message || 'Incorrect email or password.'
  } finally {
    loading.value = false
  }
}
</script>
