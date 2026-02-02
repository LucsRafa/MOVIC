<template>
  <div class="min-h-screen bg-gradient-to-b from-[#0b1020] via-[#111a2e] to-[#0b1325] px-4 py-6 text-white">
    <div class="mx-auto max-w-3xl space-y-6">
      <button class="text-sm text-white/70" @click="goBack"><- Voltar</button>

      <TodayWorkoutCard
        v-if="workoutDay"
        :day-name="dayLabel"
        :title="workoutDay.title"
        :subtitle="workoutDay.subtitle || undefined"
        :items="items"
        :progress="progressPercent"
        :show-finish="true"
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

const student = useStudentStore()
const route = useRoute()
const router = useRouter()
const videoItem = ref<any | null>(null)

const weekday = computed(() => Number(route.params.weekday))
const workoutDay = computed(() => {
  const planDays = student.activePlan?.days || []
  return planDays.find((d: any) => d.weekday === weekday.value) || null
})

const items = ref<any[]>([])

watch(
  workoutDay,
  (day) => {
    if (!day?.items) {
      items.value = []
      return
    }
    if (student.dashboard?.today?.weekday === weekday.value && student.dashboard?.today?.workout_day?.items) {
      items.value = student.dashboard.today.workout_day.items
      return
    }
    items.value = day.items.map((item: any) => ({
      workout_item_id: item.id,
      exercise: item.exercise,
      sets: item.sets,
      reps: item.reps,
      rest_seconds: item.rest_seconds,
      is_checked: false
    }))
  },
  { immediate: true }
)

const progressPercent = computed(() => {
  if (!items.value.length) return 0
  const done = items.value.filter((i: any) => i.is_checked).length
  return Math.round((done / items.value.length) * 100)
})

const dayLabel = computed(() => {
  const labels = ['Domingo', 'Segunda-feira', 'Terca-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sabado']
  return labels[weekday.value] || ''
})

const toggleItem = async (item: any) => {
  if (!student.session) {
    const session = await student.startSession(workoutDay.value?.id)
    student.session = session
  }
  item.is_checked = !item.is_checked
  try {
    await student.checkItem(student.session.id, item.workout_item_id, item.is_checked)
  } catch (e) {
    item.is_checked = !item.is_checked
  }
}

const finish = async () => {
  if (student.session) {
    await student.finishSession(student.session.id)
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
