<template>
  <section>
    <h1 class="text-3xl font-semibold">Planos</h1>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
      <div class="rounded-2xl bg-white/80 p-6 shadow">
        <h2 class="text-lg font-semibold">Criar plano</h2>
        <form class="mt-4 space-y-3" @submit.prevent="createPlan">
          <select v-model.number="planStudentId" class="w-full rounded-xl border border-black/10 px-4 py-3">
            <option :value="0">Selecione o aluno</option>
            <option v-for="student in teacher.students" :key="student.id" :value="student.id">
              {{ student.name }}
            </option>
          </select>
          <input v-model="planTitle" class="w-full rounded-xl border border-black/10 px-4 py-3" placeholder="Titulo do plano" />
          <button class="w-full rounded-xl bg-ocean px-4 py-3 text-white">Criar</button>
        </form>
        <p v-if="createdPlanId" class="mt-3 text-xs text-black/60">Plano criado: #{{ createdPlanId }}</p>
      </div>

      <div class="rounded-2xl bg-white/80 p-6 shadow">
        <h2 class="text-lg font-semibold">Adicionar dia</h2>
        <form class="mt-4 space-y-3" @submit.prevent="addDay">
          <input v-model.number="dayPlanId" class="w-full rounded-xl border border-black/10 px-4 py-3" placeholder="ID do plano" type="number" />
          <input v-model.number="weekday" class="w-full rounded-xl border border-black/10 px-4 py-3" placeholder="Dia (0-6)" type="number" />
          <input v-model="dayTitle" class="w-full rounded-xl border border-black/10 px-4 py-3" placeholder="Titulo do dia" />
          <button class="w-full rounded-xl bg-ember px-4 py-3 text-white">Adicionar dia</button>
        </form>
        <p v-if="createdDayId" class="mt-3 text-xs text-black/60">Dia criado: #{{ createdDayId }}</p>
      </div>

      <div class="rounded-2xl bg-white/80 p-6 shadow">
        <h2 class="text-lg font-semibold">Adicionar item</h2>
        <form class="mt-4 space-y-3" @submit.prevent="addItem">
          <input v-model.number="itemDayId" class="w-full rounded-xl border border-black/10 px-4 py-3" placeholder="ID do dia" type="number" />
          <input v-model.number="exerciseId" class="w-full rounded-xl border border-black/10 px-4 py-3" placeholder="ID do exercicio" type="number" />
          <input v-model.number="sets" class="w-full rounded-xl border border-black/10 px-4 py-3" placeholder="Series" type="number" />
          <input v-model="reps" class="w-full rounded-xl border border-black/10 px-4 py-3" placeholder="Reps" />
          <button class="w-full rounded-xl bg-ocean px-4 py-3 text-white">Adicionar item</button>
        </form>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useTeacherStore } from '../../stores/teacher'

const teacher = useTeacherStore()
const planStudentId = ref(0)
const planTitle = ref('')
const createdPlanId = ref<number | null>(null)

const dayPlanId = ref(0)
const weekday = ref(0)
const dayTitle = ref('')
const createdDayId = ref<number | null>(null)

const itemDayId = ref(0)
const exerciseId = ref(0)
const sets = ref(3)
const reps = ref('10')

const createPlan = async () => {
  if (!planStudentId.value) return
  const plan = await teacher.createPlan(planStudentId.value, { title: planTitle.value })
  createdPlanId.value = plan.id
  dayPlanId.value = plan.id
}

const addDay = async () => {
  if (!dayPlanId.value) return
  const day = await teacher.addDay(dayPlanId.value, { weekday: weekday.value, title: dayTitle.value })
  createdDayId.value = day.id
  itemDayId.value = day.id
}

const addItem = async () => {
  if (!itemDayId.value) return
  await teacher.addItem(itemDayId.value, { exercise_id: exerciseId.value, sets: sets.value, reps: reps.value })
}

onMounted(() => {
  teacher.loadStudents()
  teacher.loadExercises()
})
</script>
