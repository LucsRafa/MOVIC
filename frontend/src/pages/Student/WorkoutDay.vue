<template>
  <div class="min-h-screen bg-gradient-to-b from-[#0b1020] via-[#111a2e] to-[#0b1325] px-4 py-6 text-white">
    <div class="mx-auto max-w-3xl space-y-6">
      <button class="text-sm text-white/70 hover:text-white" @click="goBack">&larr; Voltar</button>

      <TodayWorkoutCard
        v-if="workoutDay"
        :day-name="dayLabel"
        :title="workoutDay.title"
        :subtitle="workoutDay.subtitle || undefined"
        :items="items"
        :progress="progressPercent"
        :show-finish="!isWorkoutCompleted"
        :is-finishing="finishing"
        :disable-actions="isWorkoutCompleted || finishing"
        @toggle="toggleItem"
        @video="openVideo"
        @finish="finish"
      />

      <div v-else class="rounded-2xl bg-white/5 p-4 text-white/60">Sem treino para este dia.</div>
    </div>

    <VideoModal
      v-if="videoItem"
      :video-url="videoItem.exercise.video_url"
      :description="videoItem.exercise.description"
      @close="videoItem = null"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import TodayWorkoutCard from '../../components/TodayWorkoutCard.vue'
import VideoModal from '../../components/VideoModal.vue'
import { useStudentStore } from '../../stores/student'
import { useToastStore } from '../../stores/toast'

const student = useStudentStore()
const route = useRoute()
const router = useRouter()
const toast = useToastStore()
const videoItem = ref<any | null>(null)
const finishing = ref(false)

const weekday = computed(() => Number(route.params.weekday))
const workoutDay = computed(() => {
  const planDays = student.activePlan?.days || []
  return planDays.find((d: any) => d.weekday === weekday.value) || null
})

const items = ref<any[]>([])
const isWorkoutCompleted = computed(() => {
  const isTodayWorkout = student.dashboard?.today?.weekday === weekday.value
  return isTodayWorkout && student.dashboard?.today?.session?.status === 'completed'
})

const mapWorkoutItem = (item: any) => {
  const completedAt = item.completed_at ?? item.checked_at ?? null

  return {
    workout_item_id: item.workout_item_id ?? item.id,
    exercise: item.exercise,
    sets: item.sets,
    reps: item.reps,
    rest_seconds: item.rest_seconds,
    completed_at: completedAt,
    is_checked: Boolean(completedAt ?? item.is_checked)
  }
}

const syncItems = () => {
  const day = workoutDay.value

  if (!day?.items) {
    items.value = []
    return
  }

  if (student.dashboard?.today?.weekday === weekday.value && student.dashboard?.today?.workout_day?.items) {
    items.value = student.dashboard.today.workout_day.items.map((item: any) => mapWorkoutItem(item))
    return
  }

  items.value = day.items.map((item: any) => mapWorkoutItem(item))
}

watch(
  [workoutDay, () => student.dashboard?.today?.workout_day?.items, () => student.dashboard?.today?.session?.id],
  syncItems,
  { immediate: true, deep: true }
)

const progressPercent = computed(() => {
  if (!items.value.length) return 0
  const done = items.value.filter((i: any) => i.is_checked).length
  return Math.round((done / items.value.length) * 100)
})

const dayLabel = computed(() => {
  const labels = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado']
  return labels[weekday.value] || ''
})

const updateWorkoutProgress = () => {
  if (!items.value.length) {
    return
  }

  const done = items.value.filter((entry: any) => entry.is_checked).length
  const percent = Math.round((done / items.value.length) * 100)

  if (student.dashboard?.today?.weekday === weekday.value && student.dashboard?.today?.workout_day) {
    student.dashboard.today.workout_day.progress_percent = percent
  }
}

const toggleItem = async (item: any) => {
  if (isWorkoutCompleted.value || finishing.value) {
    return
  }

  const workoutDayId = workoutDay.value?.id
  if (!workoutDayId) {
    return
  }

  const previousCompletedAt = item.completed_at ?? null
  item.completed_at = previousCompletedAt ? null : new Date().toISOString()
  item.is_checked = Boolean(item.completed_at)
  updateWorkoutProgress()

  try {
    const check = await student.toggleWorkoutItem(workoutDayId, item.workout_item_id)
    item.completed_at = check?.completed_at ?? check?.checked_at ?? null
    item.is_checked = Boolean(item.completed_at)
    updateWorkoutProgress()
  } catch (e) {
    item.completed_at = previousCompletedAt
    item.is_checked = Boolean(previousCompletedAt)
    updateWorkoutProgress()
  }
}

const finish = async () => {
  if (!student.session || finishing.value) {
    return
  }

  finishing.value = true

  try {
    await student.finishSession(student.session.id, { skipErrorToast: true })
    await student.fetchDashboard()
    toast.push('Treino finalizado com sucesso! 💪', 'success')
    await router.push('/student/dashboard')
  } catch (error) {
    toast.push('Erro ao finalizar treino. Tente novamente.', 'error')
  } finally {
    finishing.value = false
  }
}

const openVideo = (item: any) => {
  videoItem.value = item
}

const goBack = () => router.push('/student/dashboard')

onMounted(async () => {
  if (!student.dashboard) {
    await student.fetchDashboard()
  }
  if (!student.activePlan) {
    await student.loadActivePlan()
  }
})
</script>
