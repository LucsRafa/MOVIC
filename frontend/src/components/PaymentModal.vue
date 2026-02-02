<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4">
    <div class="w-full max-w-md rounded-2xl bg-[#0f172a] p-6 text-white shadow-2xl">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">Ativar Plano</h3>
        <button class="text-white/70 hover:text-white" @click="$emit('close')">x</button>
      </div>
      <p class="mt-3 text-sm text-white/70">Pagamento em breve. Use Pix/manual no MVP.</p>
      <button class="mt-4 w-full rounded-xl bg-emerald-500 px-4 py-3 text-sm font-semibold" @click="$emit('close')">
        Ok
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue'

onMounted(() => {
  document.body.style.overflow = 'hidden'
})

onUnmounted(() => {
  document.body.style.overflow = ''
})
</script>
