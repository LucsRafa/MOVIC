<template>
  <div class="space-y-4">
    <div class="rounded-2xl bg-white/5 p-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-semibold">Biblioteca de Exercicios</h3>
          <p class="text-sm text-white/60">Cadastre exercicios com videos instrutivos</p>
        </div>
        <button class="rounded-xl bg-emerald-500 px-4 py-2 text-xs font-semibold" @click="openCreate">
          Novo Exercicio
        </button>
      </div>
    </div>

    <div v-for="exercise in exercises" :key="exercise.id" class="rounded-2xl bg-white/5 p-4">
      <div class="flex items-center justify-between">
        <div>
          <p class="font-semibold">{{ exercise.name }}</p>
          <p class="text-xs text-white/50">{{ exercise.category || 'Grupo' }}</p>
        </div>
        <button class="rounded-xl bg-white/10 px-4 py-2 text-xs" @click="openDetails(exercise)">
          Ver Detalhes
        </button>
      </div>
    </div>

    <ExerciseCreateModal
      v-if="showCreate"
      :loading="creating"
      :error="createError"
      @close="showCreate = false"
      @create="create"
    />
    <ExerciseDetailsModal
      v-if="selected"
      :exercise="selected"
      @close="selected = null"
      @delete="deleteExercise"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useTeacherStore } from '../../stores/teacher'
import ExerciseCreateModal from '../../components/ExerciseCreateModal.vue'
import ExerciseDetailsModal from '../../components/ExerciseDetailsModal.vue'

const teacher = useTeacherStore()
const exercises = computed(() => teacher.exercises)
const showCreate = ref(false)
const selected = ref<any>(null)
const creating = ref(false)
const createError = ref('')

const openCreate = () => {
  createError.value = ''
  showCreate.value = true
}

const openDetails = (exercise: any) => {
  selected.value = exercise
}

const create = async (payload: any) => {
  creating.value = true
  createError.value = ''
  try {
    await teacher.createExercise(payload)
    showCreate.value = false
  } catch (error: any) {
    const message = error?.response?.data?.message || 'Nao foi possivel cadastrar o exercicio.'
    createError.value = message
  } finally {
    creating.value = false
  }
}

const deleteExercise = async (id: number) => {
  await teacher.deleteExercise(id)
  selected.value = null
}

onMounted(async () => {
  await teacher.loadExercises()
})
</script>
