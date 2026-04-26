<template>
  <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/70 px-4 py-8">
    <div class="w-full max-w-2xl rounded-2xl bg-[#0f172a] p-4 text-white shadow-2xl sm:p-5">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10">
            {{ initials(student?.name || '') }}
          </div>
          <div>
            <p class="font-semibold">{{ student?.name }}</p>
            <p class="text-xs text-white/50">Histórico e progresso</p>
          </div>
        </div>
        <button class="text-white/70 hover:text-white" @click="$emit('close')">x</button>
      </div>

      <div class="mt-4 grid gap-2 text-center sm:grid-cols-3">
        <div class="rounded-xl bg-white/5 p-3">
          <p class="text-xs text-white/60">Treinos</p>
          <p class="text-lg font-semibold">{{ overview?.treinos_realizados ?? 0 }}</p>
        </div>
        <div class="rounded-xl bg-white/5 p-3">
          <p class="text-xs text-white/60">Taxa</p>
          <p class="text-lg font-semibold">{{ overview?.taxa_conclusao ?? 0 }}%</p>
        </div>
        <div class="rounded-xl bg-white/5 p-3">
          <p class="text-xs text-white/60">Último</p>
          <p class="text-sm font-semibold">{{ overview?.ultimo_treino || '-' }}</p>
        </div>
      </div>

      <div class="mt-4">
        <h4 class="text-sm font-semibold">Histórico de treinos</h4>
        <div v-if="!overview?.historico_de_treinos?.length" class="text-sm text-white/50">
          Nenhum treino registrado.
        </div>
        <div v-for="session in overview?.historico_de_treinos" :key="session.date" class="mt-3 rounded-2xl bg-white/5 p-3">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <p class="text-sm font-semibold">{{ session.date }}</p>
              <p class="text-xs text-white/60">{{ session.workout_title }}</p>
            </div>
            <div class="flex flex-wrap gap-2 text-xs">
              <span class="rounded-full bg-emerald-500/20 px-2 py-1 text-emerald-200">
                {{ session.done }}/{{ session.total }}
              </span>
              <span class="rounded-full bg-yellow-500/20 px-2 py-1 text-yellow-200">{{ session.percent }}%</span>
            </div>
          </div>
          <div class="mt-3 space-y-2">
            <div
              v-for="item in session.items"
              :key="item.name"
              class="flex flex-col gap-2 rounded-xl bg-white/5 px-3 py-2 text-xs sm:flex-row sm:items-center sm:justify-between"
            >
              <div>
                <p class="font-semibold">{{ item.name }}</p>
                <p class="text-white/50">{{ item.sets }}x{{ item.reps }}</p>
              </div>
              <span
                :class="item.status === 'completed'
                  ? 'rounded-full bg-emerald-500/20 px-2 py-1 text-emerald-200'
                  : 'rounded-full bg-white/10 px-2 py-1 text-white/50'"
              >
                {{ item.status === 'completed' ? 'Concluído' : 'Não realizado' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue'

defineProps<{ student: any; overview: any }>()

const initials = (name: string) => {
  const parts = name.split(' ')
  return parts.length ? (parts[0][0] + (parts[1]?.[0] || '')).toUpperCase() : 'U'
}

onMounted(() => {
  document.body.style.overflow = 'hidden'
})

onUnmounted(() => {
  document.body.style.overflow = ''
})
</script>
