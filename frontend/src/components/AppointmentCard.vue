<template>
  <div class="card hover:shadow-md transition-shadow duration-200">
    <div class="flex items-start justify-between gap-3">
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 flex-wrap mb-1">
          <h3 class="font-semibold text-gray-900 truncate">{{ appointment.title }}</h3>
          <span
            class="text-xs font-medium px-2 py-0.5 rounded-full capitalize"
            :class="statusColor(appointment.status)"
          >{{ appointment.status }}</span>
        </div>

        <p v-if="appointment.description" class="text-sm text-gray-500 mb-2 line-clamp-2">
          {{ appointment.description }}
        </p>

        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
          <span class="flex items-center gap-1.5">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.5"/>
              <path d="M16 2v4M8 2v4M3 10h18" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            {{ formatDate(appointment.appointment_date) }}
          </span>
          <span class="flex items-center gap-1.5">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="9" stroke-width="1.5"/>
              <path d="M12 7v5l3 3" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            {{ formatTime(appointment.appointment_time) }}
          </span>
        </div>
      </div>

      <!-- Actions (only for pending) -->
      <div v-if="appointment.status === 'pending'" class="flex flex-col gap-2 shrink-0">
        <button @click="$emit('edit', appointment)" class="btn-secondary text-xs py-1.5 px-3">
          Edit
        </button>
        <button
          @click="$emit('cancel', appointment)"
          class="btn-danger text-xs py-1.5 px-3"
          :disabled="cancelling"
        >
          {{ cancelling ? '…' : 'Cancel' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { formatDate, formatTime, statusColor } from '@/utils/helpers.js'

defineProps({
  appointment: { type: Object,  required: true },
  cancelling:  { type: Boolean, default: false },
})
defineEmits(['edit', 'cancel'])
</script>
