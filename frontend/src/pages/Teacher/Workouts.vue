<template>
  <div class="space-y-4">
    <div class="rounded-2xl bg-white/5 p-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-semibold">Treinos Personalizados</h3>
          <p class="text-sm text-white/60">Crie e organize treinos para seus alunos</p>
        </div>
        <button class="rounded-xl bg-emerald-500 px-4 py-2 text-xs font-semibold" @click="showCreate = true">
          Novo Treino
        </button>
      </div>
      <div class="mt-3">
        <select v-model="selectedStudentId" class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm">
          <option value="">Selecione o aluno</option>
          <option v-for="student in students" :key="student.id" :value="String(student.id)">
            {{ student.name }}
          </option>
        </select>
      </div>
    </div>

    <div v-if="!selectedStudentId" class="text-sm text-white/60">Selecione um aluno para visualizar os treinos.</div>

    <div v-if="selectedStudentId" class="space-y-4">
      <div
        v-for="day in workouts.days"
        :key="day.id"
        class="rounded-2xl bg-white/5 p-4"
      >
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-white/60">{{ weekdayLabel(day.weekday) }}</p>
            <p class="font-semibold">{{ day.title }}</p>
            <p class="text-xs text-white/50">{{ day.items?.length || 0 }} exercicios</p>
          </div>
          <button class="rounded-xl bg-white/10 px-3 py-2 text-xs" type="button" @click="openAddItem(day)">
            Adicionar exercicio
          </button>
        </div>

        <div class="mt-3 space-y-3">
          <div
            v-for="item in day.items"
            :key="item.id"
            class="flex items-center justify-between rounded-xl bg-white/5 px-3 py-2 text-xs"
          >
            <div>
              <p class="font-semibold">{{ item.exercise?.name }}</p>
              <p class="text-white/50">{{ item.sets }}x{{ item.reps }} · Descanso {{ item.rest_seconds || '-' }}s</p>
            </div>
            <button class="rounded-full bg-white/10 px-3 py-1" @click="removeItem(item.id)">Excluir</button>
          </div>
        </div>
      </div>
    </div>

    <WorkoutCreateModal
      v-if="showCreate"
      :students="students"
      @close="showCreate = false"
      @create="createWorkout"
    />

    <ExercisePickerModal
      v-if="showPicker"
      :exercises="exercises"
      @close="showPicker = false"
      @select="addItem"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { useTeacherStore } from '../../stores/teacher'
import WorkoutCreateModal from '../../components/WorkoutCreateModal.vue'
import ExercisePickerModal from '../../components/ExercisePickerModal.vue'

const teacher = useTeacherStore()
const students = computed(() => teacher.students)
const workouts = computed(() => teacher.workouts)
const exercises = computed(() => teacher.exercises)

const selectedStudentId = ref('')
const showCreate = ref(false)
const showPicker = ref(false)
const activeDayId = ref<number | null>(null)

const weekdayLabel = (weekday: number) => {
  const labels = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab']
  return labels[weekday] || 'Dia'
}

const openAddItem = async (day: any) => {
  activeDayId.value = day.id
  showPicker.value = true
  if (!exercises.value.length) {
    try {
      await teacher.loadExercises()
    } catch (error) {
      // Keep modal open so the user can see the empty state.
    }
  }
}

const addItem = async (payload: any) => {
  if (!activeDayId.value) return
  await teacher.addWorkoutItem(activeDayId.value, payload)
  showPicker.value = false
  if (selectedStudentId.value) {
    await teacher.fetchWorkouts(Number(selectedStudentId.value))
  }
}

const removeItem = async (itemId: number) => {
  await teacher.deleteWorkoutItem(itemId)
  if (selectedStudentId.value) {
    await teacher.fetchWorkouts(Number(selectedStudentId.value))
  }
}

const createWorkout = async (payload: any) => {
  await teacher.createWorkoutDay(payload)
  showCreate.value = false
  if (selectedStudentId.value) {
    await teacher.fetchWorkouts(Number(selectedStudentId.value))
  }
}

watch(selectedStudentId, async (value) => {
  if (value) {
    await teacher.fetchWorkouts(Number(value))
  }
})

onMounted(async () => {
  await teacher.fetchStudents()
})
</script>
