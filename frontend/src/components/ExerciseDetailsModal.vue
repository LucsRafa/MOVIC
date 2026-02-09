<template>
  <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/70 px-4 py-8">
    <div class="w-full max-w-lg rounded-2xl bg-[#0f172a] p-4 text-white shadow-2xl">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">{{ exercise.name }}</h3>
        <button class="text-white/70 hover:text-white" @click="$emit('close')">x</button>
      </div>
      <p class="text-sm text-white/60">{{ exercise.category || 'Grupo muscular' }}</p>

      <div class="mt-4">
        <div class="rounded-2xl bg-white/5 p-3 text-center">
          <video v-if="exercise.video_url" class="w-full rounded-xl" controls>
            <source :src="exercise.video_url" />
          </video>
          <div v-else class="text-sm text-white/50">Sem video cadastrado.</div>
        </div>
        <p class="mt-3 text-sm text-white/70">{{ exercise.description || 'Sem descricao.' }}</p>
      </div>

      <div class="mt-4 flex items-center gap-2">
        <button class="flex-1 rounded-xl bg-white/10 px-4 py-3 text-sm" @click="$emit('close')">Fechar</button>
        <button class="flex-1 rounded-xl border border-red-400/60 px-4 py-3 text-sm text-red-200" @click="$emit('delete', exercise.id)">
          Excluir
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{ exercise: any }>()
</script>
