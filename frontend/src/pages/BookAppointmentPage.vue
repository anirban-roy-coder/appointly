<template>
  <div class="max-w-2xl mx-auto px-4 sm:px-6 py-8">
    <div class="mb-6">
      <RouterLink
        to="/dashboard"
        class="inline-flex items-center gap-1 text-primary-600 hover:text-primary-700 text-sm mb-4 transition-colors"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M19 12H5M12 5l-7 7 7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Back to Dashboard
      </RouterLink>
      <h1 class="text-2xl font-bold text-gray-900">
        {{ isEdit ? 'Edit Appointment' : 'Book an Appointment' }}
      </h1>
      <p class="text-gray-500 mt-0.5 text-sm">
        {{ isEdit ? 'Update the details below.' : 'Fill in the details to schedule your visit.' }}
      </p>
    </div>

    <!-- Loading skeleton (edit mode) -->
    <div v-if="loadingEdit" class="card space-y-4 animate-pulse">
      <div class="h-4 bg-gray-200 rounded w-1/4"></div>
      <div class="h-10 bg-gray-200 rounded"></div>
      <div class="h-4 bg-gray-200 rounded w-1/4"></div>
      <div class="h-24 bg-gray-200 rounded"></div>
      <div class="grid grid-cols-2 gap-4">
        <div class="h-10 bg-gray-200 rounded"></div>
        <div class="h-10 bg-gray-200 rounded"></div>
      </div>
    </div>

    <div v-else class="card">
      <!-- Inline error banner -->
      <div
        v-if="errorMsg"
        class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-5 flex items-start gap-2"
      >
        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="9" stroke-width="2"/>
          <path d="M12 8v4M12 16h.01" stroke-width="2" stroke-linecap="round"/>
        </svg>
        {{ errorMsg }}
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-5" novalidate>
        <div>
          <label class="form-label" for="title">
            Title <span class="text-red-500">*</span>
          </label>
          <input
            id="title"
            v-model.trim="form.title"
            type="text"
            class="input-field"
            :class="{ 'border-red-400 focus:ring-red-400': fieldErrors.title }"
            placeholder="e.g. General Checkup, Hair Cut, Legal Consultation"
            maxlength="200"
            autocomplete="off"
          />
          <p v-if="fieldErrors.title" class="error-text">{{ fieldErrors.title }}</p>
        </div>

        <div>
          <label class="form-label" for="description">
            Notes <span class="text-gray-400 font-normal">(optional)</span>
          </label>
          <textarea
            id="description"
            v-model.trim="form.description"
            rows="3"
            class="input-field resize-none"
            placeholder="Anything the service provider should know in advance…"
            maxlength="1000"
          />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="form-label" for="appt-date">
              Date <span class="text-red-500">*</span>
            </label>
            <input
              id="appt-date"
              v-model="form.appointment_date"
              type="date"
              class="input-field"
              :class="{ 'border-red-400 focus:ring-red-400': fieldErrors.appointment_date }"
              :min="minDate"
            />
            <p v-if="fieldErrors.appointment_date" class="error-text">{{ fieldErrors.appointment_date }}</p>
          </div>

          <div>
            <label class="form-label" for="appt-time">
              Time <span class="text-red-500">*</span>
            </label>
            <select
              id="appt-time"
              v-model="form.appointment_time"
              class="input-field"
              :class="{ 'border-red-400 focus:ring-red-400': fieldErrors.appointment_time }"
            >
              <option value="" disabled>Choose a time slot</option>
              <option v-for="slot in timeSlots" :key="slot.value" :value="slot.value">
                {{ slot.label }}
              </option>
            </select>
            <p v-if="fieldErrors.appointment_time" class="error-text">{{ fieldErrors.appointment_time }}</p>
          </div>
        </div>

        <!-- Slot hint -->
        <p class="text-xs text-gray-400">
          Available slots: 9:00 AM – 6:00 PM, every 30 minutes. Taken slots will be rejected.
        </p>

        <div class="flex gap-3 pt-2">
          <RouterLink to="/dashboard" class="btn-secondary flex-1 text-sm">Cancel</RouterLink>
          <button type="submit" class="btn-primary flex-1 text-sm" :disabled="loading">
            <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            {{ loading
              ? (isEdit ? 'Updating…' : 'Booking…')
              : (isEdit ? 'Update Appointment' : 'Confirm Booking') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { appointmentService } from '@/services/appointmentService.js'
import { useToast }           from '@/composables/useToast.js'
import { getTodayString }     from '@/utils/helpers.js'

const route  = useRoute()
const router = useRouter()
const toast  = useToast()

const loading     = ref(false)
const loadingEdit = ref(false)
const errorMsg    = ref('')
const fieldErrors = ref({})

const isEdit  = computed(() => !!route.params.id)
const minDate = getTodayString()

const form = reactive({
  title:            '',
  description:      '',
  appointment_date: '',
  appointment_time: '',
})

// 09:00–18:00 in 30-min steps with readable labels
const timeSlots = (() => {
  const slots = []
  for (let h = 9; h <= 17; h++) {
    for (const m of [0, 30]) {
      const hh    = String(h).padStart(2, '0')
      const mm    = String(m).padStart(2, '0')
      const val   = `${hh}:${mm}`
      const ampm  = h >= 12 ? 'PM' : 'AM'
      const h12   = h > 12 ? h - 12 : h
      slots.push({ value: val, label: `${h12}:${mm} ${ampm}` })
    }
  }
  slots.push({ value: '18:00', label: '6:00 PM' })
  return slots
})()

function clearErrors() {
  errorMsg.value  = ''
  fieldErrors.value = {}
}

function validateLocally() {
  const errs = {}
  if (!form.title || form.title.length < 3)      errs.title = 'Title must be at least 3 characters.'
  if (!form.appointment_date)                     errs.appointment_date = 'Please pick a date.'
  if (!form.appointment_time)                     errs.appointment_time = 'Please choose a time slot.'
  fieldErrors.value = errs
  return Object.keys(errs).length === 0
}

onMounted(async () => {
  if (!isEdit.value) return
  loadingEdit.value = true
  try {
    const res  = await appointmentService.getAll()
    const appt = (res.data ?? []).find(a => a.id === parseInt(route.params.id))
    if (!appt || appt.status !== 'pending') {
      router.push('/dashboard')
      return
    }
    form.title            = appt.title
    form.description      = appt.description ?? ''
    form.appointment_date = appt.appointment_date
    form.appointment_time = appt.appointment_time.substring(0, 5)
  } catch {
    router.push('/dashboard')
  } finally {
    loadingEdit.value = false
  }
})

async function handleSubmit() {
  clearErrors()
  if (!validateLocally()) return

  loading.value = true
  try {
    if (isEdit.value) {
      await appointmentService.update(route.params.id, form)
      toast.success('Appointment updated successfully!')
    } else {
      await appointmentService.book(form)
      toast.success('Appointment booked! See you then.')
    }
    router.push('/dashboard')
  } catch (err) {
    if (err.errors) {
      fieldErrors.value = err.errors
    }
    errorMsg.value = err.message || 'Something went wrong, please try again.'
  } finally {
    loading.value = false
  }
}
</script>
