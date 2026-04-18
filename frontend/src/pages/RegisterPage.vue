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
        <p class="text-gray-500 mt-1 text-sm">Create your account to get started</p>
      </div>

      <div class="card">
        <h2 class="text-lg font-semibold text-gray-900 mb-5">Get started</h2>

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

        <form @submit.prevent="handleRegister" class="space-y-4" novalidate>
          <div>
            <label class="form-label" for="name">Full name</label>
            <input
              id="name" v-model.trim="form.name" type="text" class="input-field"
              placeholder="John Doe" required minlength="2" autocomplete="name"
            />
          </div>
          <div>
            <label class="form-label" for="reg-email">Email</label>
            <input
              id="reg-email" v-model.trim="form.email" type="email" class="input-field"
              placeholder="you@example.com" required autocomplete="email"
            />
          </div>
          <div>
            <label class="form-label" for="phone">
              Phone <span class="text-gray-400 font-normal text-xs">(optional)</span>
            </label>
            <input
              id="phone" v-model.trim="form.phone" type="tel" class="input-field"
              placeholder="+91 98765 43210" autocomplete="tel"
            />
          </div>
          <div>
            <label class="form-label" for="reg-pw">Password</label>
            <input
              id="reg-pw" v-model="form.password" type="password" class="input-field"
              placeholder="At least 6 characters" required minlength="6" autocomplete="new-password"
            />
            <!-- password strength bar -->
            <div v-if="form.password" class="mt-1.5 flex gap-1">
              <div
                v-for="i in 4" :key="i"
                class="h-1 flex-1 rounded-full transition-colors duration-300"
                :class="i <= pwStrength ? strengthColor : 'bg-gray-200'"
              ></div>
            </div>
            <p v-if="form.password" class="text-xs mt-1" :class="strengthTextColor">
              {{ strengthLabel }}
            </p>
          </div>
          <button type="submit" class="btn-primary w-full" :disabled="loading">
            <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            {{ loading ? 'Creating account…' : 'Create account' }}
          </button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-600">
          Already have an account?
          <RouterLink to="/login" class="text-primary-600 hover:text-primary-700 font-medium">Sign in</RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '@/store/auth.js'

const auth     = useAuthStore()
const router   = useRouter()
const loading  = ref(false)
const errorMsg = ref('')
const form     = reactive({ name: '', email: '', phone: '', password: '' })

// Simple password strength
const pwStrength = computed(() => {
  const p = form.password
  if (!p) return 0
  let score = 0
  if (p.length >= 6)  score++
  if (p.length >= 10) score++
  if (/[A-Z]/.test(p) || /[0-9]/.test(p)) score++
  if (/[^A-Za-z0-9]/.test(p)) score++
  return Math.max(1, score)
})
const strengthColor = computed(() => {
  return ['', 'bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-green-500'][pwStrength.value]
})
const strengthTextColor = computed(() => {
  return ['', 'text-red-500', 'text-orange-500', 'text-yellow-600', 'text-green-600'][pwStrength.value]
})
const strengthLabel = computed(() => {
  return ['', 'Too short', 'Weak', 'Good', 'Strong'][pwStrength.value]
})

async function handleRegister() {
  loading.value  = true
  errorMsg.value = ''
  try {
    await auth.register(form)
    router.push('/dashboard')
  } catch (err) {
    errorMsg.value = err.message || 'Registration failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>
