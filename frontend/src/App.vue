<template>
  <div class="min-h-screen bg-gray-50">
    <NavBar v-if="auth.isLoggedIn" />
    <main>
      <RouterView />
    </main>
    <ToastContainer />
  </div>
</template>

<script setup>
import { RouterView } from 'vue-router'
import NavBar        from '@/components/NavBar.vue'
import ToastContainer from '@/components/ToastContainer.vue'
import { useAuthStore } from '@/store/auth.js'

const auth = useAuthStore()
</script>
