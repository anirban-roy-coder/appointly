export function formatDate(dateStr) {
  if (!dateStr) return ''
  const [y, m, d] = dateStr.split('-')
  const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
  return `${d} ${months[parseInt(m) - 1]} ${y}`
}

export function formatTime(timeStr) {
  if (!timeStr) return ''
  const parts = timeStr.split(':')
  const hour  = parseInt(parts[0])
  const min   = parts[1] || '00'
  const ampm  = hour >= 12 ? 'PM' : 'AM'
  const h12   = hour % 12 || 12
  return `${h12}:${min} ${ampm}`
}

export function statusColor(status) {
  const map = {
    pending:   'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
  }
  return map[status] || 'bg-gray-100 text-gray-800'
}

export function getTodayString() {
  return new Date().toISOString().split('T')[0]
}
