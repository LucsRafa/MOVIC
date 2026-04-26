<template>
  <div class="space-y-4">
    <div class="rounded-2xl bg-white/5 p-4">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h3 class="text-lg font-semibold">Biblioteca de exercícios</h3>
          <p class="text-sm text-white/60">Cadastre exercícios com vídeos instrutivos</p>
        </div>
        <button class="rounded-xl bg-emerald-500 px-4 py-2 text-xs font-semibold hover:bg-emerald-400" @click="openCreate">
          Novo exercício
        </button>
      </div>
    </div>

    <div v-for="exercise in exercises" :key="exercise.id" class="rounded-2xl bg-white/5 p-4">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <p class="font-semibold">{{ exercise.name }}</p>
          <p class="text-xs text-white/50">{{ exercise.category || 'Grupo' }}</p>
        </div>
        <button class="rounded-xl bg-white/10 px-4 py-2 text-xs hover:bg-white/15" @click="openDetails(exercise)">
          Ver detalhes
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
      :loading="updating"
      :error="detailsError"
      @close="selected = null"
      @delete="deleteExercise"
      @update="updateExercise"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useTeacherStore } from '../../stores/teacher'
import ExerciseCreateModal from '../../components/ExerciseCreateModal.vue'
import ExerciseDetailsModal from '../../components/ExerciseDetailsModal.vue'
import { useToastStore } from '../../stores/toast'
import { extractApiErrorMessage } from '../../utils/apiError'

const teacher = useTeacherStore()
const toast = useToastStore()
const exercises = computed(() => teacher.exercises)
const showCreate = ref(false)
const selected = ref<any>(null)
const creating = ref(false)
const updating = ref(false)
const createError = ref('')
const detailsError = ref('')

const openCreate = () => {
  createError.value = ''
  showCreate.value = true
}

const openDetails = (exercise: any) => {
  detailsError.value = ''
  selected.value = exercise
}

const create = async (payload: any) => {
  creating.value = true
  createError.value = ''
  try {
    await teacher.createExercise(payload)
    await teacher.loadExercises()
    toast.push('Exercício cadastrado com sucesso.', 'success')
    showCreate.value = false
  } catch (error: any) {
    createError.value = extractApiErrorMessage(error, 'Não foi possível cadastrar o exercício.')
  } finally {
    creating.value = false
  }
}

const updateExercise = async (id: number, payload: any) => {
  updating.value = true
  detailsError.value = ''
  try {
    const updated = await teacher.updateExercise(id, payload)
    selected.value = updated
    toast.push('Exercício atualizado com sucesso.', 'success')
  } catch (error: any) {
    detailsError.value = extractApiErrorMessage(error, 'Não foi possível atualizar o exercício.')
  } finally {
    updating.value = false
  }
}

const deleteExercise = async (id: number) => {
  await teacher.deleteExercise(id)
  toast.push('Exercício removido com sucesso.', 'success')
  selected.value = null
}

onMounted(async () => {
  await teacher.loadExercises()
})
</script>
