<template>
  <section>
    <h1 class="text-3xl font-semibold">Alunos</h1>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1.2fr_1fr]">
      <div class="rounded-2xl bg-white/80 p-6 shadow">
        <h2 class="text-lg font-semibold">Lista</h2>
        <ul class="mt-4 space-y-3">
          <li v-for="student in teacher.students" :key="student.id" class="flex items-center justify-between rounded-xl border border-black/5 px-4 py-3">
            <div>
              <p class="font-medium">{{ student.name }}</p>
              <p class="text-sm text-black/60">{{ student.email }}</p>
              <p class="text-xs text-black/40">Status: {{ student.student_profile?.status }}</p>
            </div>
            <button class="rounded-full border border-black/10 px-4 py-2 text-sm" @click="approve(student.id)">
              Aprovar
            </button>
          </li>
        </ul>
      </div>

      <div class="rounded-2xl bg-white/80 p-6 shadow">
        <h2 class="text-lg font-semibold">Convidar aluno</h2>
        <form class="mt-4 space-y-3" @submit.prevent="invite">
          <input v-model="email" class="w-full rounded-xl border border-black/10 px-4 py-3" type="email" placeholder="Email" />
          <button class="w-full rounded-xl bg-ocean px-4 py-3 text-white">Enviar convite</button>
        </form>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useTeacherStore } from '../../stores/teacher'

const teacher = useTeacherStore()
const email = ref('')

const invite = async () => {
  if (!email.value) return
  await teacher.inviteStudent(email.value)
  email.value = ''
}

const approve = async (id: number) => {
  await teacher.approveStudent(id)
  await teacher.loadStudents()
}

onMounted(() => {
  teacher.loadStudents()
})
</script>
