<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4">
    <div class="w-full max-w-xl rounded-2xl bg-[#0f172a] p-4 text-white shadow-2xl">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">Video do Exercicio</h3>
        <button class="text-white/70 hover:text-white" @click="$emit('close')">x</button>
      </div>
      <div class="mt-4 aspect-video w-full overflow-hidden rounded-xl bg-black">
        <iframe
          v-if="videoUrl"
          :src="videoUrl"
          class="h-full w-full"
          frameborder="0"
          allowfullscreen
        ></iframe>
      </div>
      <p class="mt-3 text-sm text-white/70" v-if="description">{{ description }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue'

defineProps<{ videoUrl?: string; description?: string }>()

onMounted(() => {
  document.body.style.overflow = 'hidden'
})

onUnmounted(() => {
  document.body.style.overflow = ''
})
</script>
