<template>
  <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/70 px-4 py-8">
    <div class="w-full max-w-2xl rounded-2xl bg-[#0f172a] p-4 text-white shadow-2xl">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">Anexar Exercicio</h3>
        <button class="text-white/70 hover:text-white" @click="$emit('close')">x</button>
      </div>
      <p class="text-sm text-white/60">Selecione um exercicio cadastrado e configure series e repeticoes.</p>

      <div class="mt-4 grid gap-4 md:grid-cols-[1.2fr_1fr]">
        <div>
          <input
            v-model="search"
            class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm"
            placeholder="Buscar exercicio..."
          />
          <div class="mt-3 max-h-[360px] space-y-2 overflow-y-auto">
            <div v-if="filteredExercises.length === 0" class="rounded-xl bg-white/5 p-3 text-sm text-white/60">
              Nenhum exercicio cadastrado.
            </div>
            <button
              v-for="exercise in filteredExercises"
              :key="exercise.id"
              class="flex w-full items-center justify-between rounded-xl border px-4 py-3 text-left text-sm transition"
              :class="
                form.exercise_id === exercise.id
                  ? 'border-emerald-400 bg-emerald-500/10'
                  : 'border-white/10 bg-white/5 hover:bg-white/10'
              "
              @click="form.exercise_id = exercise.id"
            >
              <div>
                <p class="font-semibold">{{ exercise.name }}</p>
                <p class="text-xs text-white/50">{{ exercise.category || 'Grupo' }}</p>
              </div>
              <span class="text-xs text-emerald-300">Selecionar</span>
            </button>
          </div>
        </div>

        <div class="rounded-2xl bg-white/5 p-4">
          <p class="text-sm text-white/60">Exercicio selecionado</p>
          <p class="mt-1 text-sm font-semibold">
            {{ selectedExercise?.name || 'Nenhum' }}
          </p>

          <div class="mt-4 grid grid-cols-3 gap-2">
            <input v-model.number="form.sets" type="number" class="rounded-xl bg-white/10 px-3 py-2 text-sm" placeholder="Sets" />
            <input v-model="form.reps" class="rounded-xl bg-white/10 px-3 py-2 text-sm" placeholder="Reps" />
            <input v-model.number="form.rest_seconds" type="number" class="rounded-xl bg-white/10 px-3 py-2 text-sm" placeholder="Descanso" />
          </div>
          <input v-model.number="form.item_order" type="number" class="mt-3 w-full rounded-xl bg-white/10 px-4 py-3 text-sm" placeholder="Ordem" />
          <textarea v-model="form.notes" class="mt-3 w-full rounded-xl bg-white/10 px-4 py-3 text-sm" placeholder="Observacoes"></textarea>

          <button
            class="mt-4 w-full rounded-xl bg-emerald-500 px-4 py-3 text-sm font-semibold disabled:opacity-40"
            :disabled="!form.exercise_id"
            @click="submit"
          >
            Adicionar exercicio
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, reactive, ref } from 'vue'

const props = defineProps<{ exercises: any[] }>()
const emit = defineEmits(['close', 'select'])

const search = ref('')
const form = reactive({
  exercise_id: null as number | null,
  sets: 4,
  reps: '12',
  rest_seconds: 60,
  item_order: 1,
  notes: ''
})

const filteredExercises = computed(() => {
  if (!search.value) return props.exercises
  return props.exercises.filter((exercise) =>
    exercise.name.toLowerCase().includes(search.value.toLowerCase())
  )
})

const selectedExercise = computed(() =>
  props.exercises.find((exercise) => exercise.id === form.exercise_id)
)

const submit = () => {
  if (!form.exercise_id) return
  emit('select', { ...form })
}
</script>
