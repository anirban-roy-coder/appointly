import { ref, computed } from 'vue'
import { appointmentService } from '@/services/appointmentService.js'

export function useAppointments() {
  const appointments = ref([])
  const loading      = ref(false)
  const error        = ref('')

  const pending   = computed(() => appointments.value.filter(a => a.status === 'pending'))
  const confirmed = computed(() => appointments.value.filter(a => a.status === 'confirmed'))
  const cancelled = computed(() => appointments.value.filter(a => a.status === 'cancelled'))

  async function fetch() {
    loading.value = true
    error.value   = ''
    try {
      const res = await appointmentService.getAll()
      appointments.value = res.data ?? []
    } catch (err) {
      error.value = err.message || 'Could not load appointments.'
    } finally {
      loading.value = false
    }
  }

  async function cancel(id) {
    await appointmentService.cancel(id)
    const idx = appointments.value.findIndex(a => a.id === id)
    if (idx !== -1) {
      appointments.value[idx] = { ...appointments.value[idx], status: 'cancelled' }
    }
  }

  return { appointments, loading, error, pending, confirmed, cancelled, fetch, cancel }
}
