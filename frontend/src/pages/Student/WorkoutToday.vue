<template>
  <section>
    <h1 class="text-3xl font-semibold">Treino de hoje</h1>

    <div v-if="!day" class="mt-6 rounded-2xl bg-white/80 p-6 shadow">
      <p class="text-black/70">Nenhum treino configurado para hoje.</p>
    </div>

    <WorkoutDayCard v-else class="mt-6" :day="day" @toggle="toggleItem">
      <template #actions>
        <button v-if="!session" class="rounded-full bg-ember px-4 py-2 text-white" @click="start">Iniciar</button>
        <button v-else class="rounded-full border border-black/10 px-4 py-2" @click="finish">Finalizar</button>
      </template>
    </WorkoutDayCard>
  </section>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import WorkoutDayCard from '../../components/WorkoutDayCard.vue'
import { useStudentStore } from '../../stores/student'

const student = useStudentStore()

const day = computed(() => {
  const weekday = new Date().getDay()
  return student.activePlan?.days?.find((d: any) => d.weekday === weekday)
})

const session = computed(() => student.session)

const start = async () => {
  if (!day.value) return
  await student.startSession(day.value.id)
}

const toggleItem = async (item: any, value: boolean) => {
  if (!session.value) return
  await student.checkItem(session.value.id, item.id, value)
}

const finish = async () => {
  if (!session.value) return
  await student.finishSession(session.value.id)
}

onMounted(() => {
  student.loadActivePlan()
})
</script>
