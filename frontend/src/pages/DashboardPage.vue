<template>
  <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">My Appointments</h1>
        <p class="text-gray-500 mt-0.5 text-sm">Manage all your bookings</p>
      </div>
      <RouterLink to="/book" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M12 5v14M5 12h14" stroke-width="2.5" stroke-linecap="round"/>
        </svg>
        Book New
      </RouterLink>
    </div>

    <!-- Filter Tabs -->
    <div class="flex gap-2 mb-6 overflow-x-auto pb-1">
      <button
        v-for="tab in tabs" :key="tab.value"
        @click="activeTab = tab.value"
        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap"
        :class="activeTab === tab.value
          ? 'bg-primary-600 text-white shadow-sm'
          : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
      >
        {{ tab.label }}
        <span
          class="ml-1.5 text-xs rounded-full px-1.5 py-0.5 font-medium"
          :class="activeTab === tab.value ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600'"
        >{{ countFor(tab.value) }}</span>
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="space-y-4">
      <div v-for="i in 3" :key="i" class="card animate-pulse">
        <div class="flex justify-between gap-4">
          <div class="flex-1 space-y-2">
            <div class="h-5 bg-gray-200 rounded w-1/3"></div>
            <div class="h-4 bg-gray-200 rounded w-2/3"></div>
            <div class="h-4 bg-gray-200 rounded w-1/4"></div>
          </div>
          <div class="w-16 h-8 bg-gray-200 rounded"></div>
        </div>
      </div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="card text-center py-12">
      <svg class="w-10 h-10 text-red-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="9" stroke-width="1.5"/>
        <path d="M12 8v4M12 16h.01" stroke-width="2" stroke-linecap="round"/>
      </svg>
      <p class="text-red-600 text-sm mb-4">{{ error }}</p>
      <button @click="fetch" class="btn-secondary text-sm">Try again</button>
    </div>

    <!-- Empty -->
    <div v-else-if="filtered.length === 0" class="card text-center py-16">
      <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.5"/>
        <path d="M16 2v4M8 2v4M3 10h18" stroke-width="1.5" stroke-linecap="round"/>
        <path d="M8 15h.01M12 15h.01M16 15h.01" stroke-width="2" stroke-linecap="round"/>
      </svg>
      <h3 class="text-gray-700 font-medium mb-1">
        No {{ activeTab === 'all' ? '' : activeTab + ' ' }}appointments
      </h3>
      <p class="text-gray-400 text-sm mb-6">
        {{ activeTab === 'all' ? "You haven't booked anything yet." : "Nothing here." }}
      </p>
      <RouterLink v-if="activeTab === 'all'" to="/book" class="btn-primary text-sm">
        Book your first appointment
      </RouterLink>
    </div>

    <!-- List -->
    <div v-else class="space-y-4">
      <AppointmentCard
        v-for="appt in filtered"
        :key="appt.id"
        :appointment="appt"
        :cancelling="cancellingId === appt.id"
        @edit="goEdit"
        @cancel="promptCancel"
      />
    </div>

    <!-- Confirm Cancel Modal -->
    <Transition name="fade">
      <div
        v-if="confirmTarget"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4"
        @click.self="confirmTarget = null"
      >
        <div class="bg-white rounded-xl p-6 max-w-sm w-full shadow-xl">
          <h3 class="font-semibold text-gray-900 text-lg mb-2">Cancel this appointment?</h3>
          <p class="text-gray-500 text-sm mb-6">
            <strong class="text-gray-800">"{{ confirmTarget.title }}"</strong>
            on {{ formatDate(confirmTarget.appointment_date) }} at {{ formatTime(confirmTarget.appointment_time) }}
            will be cancelled and can't be recovered.
          </p>
          <div class="flex gap-3">
            <button @click="confirmTarget = null" class="btn-secondary flex-1">Keep it</button>
            <button @click="doCancel" class="btn-danger flex-1" :disabled="cancellingId !== null">
              {{ cancellingId ? 'Cancelling…' : 'Yes, cancel' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import AppointmentCard from '@/components/AppointmentCard.vue'
import { useAppointments } from '@/composables/useAppointments.js'
import { useToast }        from '@/composables/useToast.js'
import { formatDate, formatTime } from '@/utils/helpers.js'

const router = useRouter()
const toast  = useToast()

const { appointments, loading, error, fetch, cancel } = useAppointments()

const activeTab    = ref('all')
const cancellingId = ref(null)
const confirmTarget = ref(null)

const tabs = [
  { label: 'All',       value: 'all' },
  { label: 'Pending',   value: 'pending' },
  { label: 'Confirmed', value: 'confirmed' },
  { label: 'Cancelled', value: 'cancelled' },
]

const filtered = computed(() => {
  if (activeTab.value === 'all') return appointments.value
  return appointments.value.filter(a => a.status === activeTab.value)
})

function countFor(status) {
  if (status === 'all') return appointments.value.length
  return appointments.value.filter(a => a.status === status).length
}

function goEdit(appt) {
  router.push(`/book/${appt.id}`)
}

function promptCancel(appt) {
  confirmTarget.value = appt
}

async function doCancel() {
  if (!confirmTarget.value) return
  cancellingId.value = confirmTarget.value.id
  try {
    await cancel(confirmTarget.value.id)
    toast.success('Appointment cancelled.')
    confirmTarget.value = null
  } catch (err) {
    toast.error(err.message || 'Failed to cancel.')
  } finally {
    cancellingId.value = null
  }
}

onMounted(fetch)
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
