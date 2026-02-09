<template>
  <div class="space-y-4">
    <div class="rounded-2xl bg-white/5 p-4">
      <h3 class="text-lg font-semibold">Alunos Ativos</h3>
      <p class="text-sm text-white/60">Gerencie seus alunos cadastrados</p>
      <div class="mt-3">
        <input
          v-model="search"
          class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm"
          placeholder="Buscar aluno..."
          @input="handleSearch"
        />
      </div>
    </div>

    <div v-if="students.length === 0" class="text-sm text-white/60">Nenhum aluno encontrado.</div>

    <div v-for="student in students" :key="student.id" class="rounded-2xl bg-white/5 p-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-sm font-semibold">
            {{ initials(student.name) }}
          </div>
          <div>
            <p class="font-semibold">{{ student.name }}</p>
            <p class="text-xs text-white/50">Ultimo treino: {{ student.last_workout_date || '-' }}</p>
            <div class="mt-2">
              <span :class="badgeClass(student.status)">{{ statusLabel(student.status) }}</span>
            </div>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <button class="rounded-xl bg-white/10 px-3 py-2 text-xs" @click="openOverview(student)">
            Historico
          </button>
          <button class="rounded-xl bg-white/10 px-3 py-2 text-xs" @click="resetPassword(student.id)">
            Resetar senha
          </button>
          <select
            class="rounded-xl bg-white/10 px-3 py-2 text-xs"
            :value="student.status"
            @change="updateStatus(student.id, ($event.target as HTMLSelectElement).value)"
          >
            <option value="active">Ativo</option>
            <option value="trial">Experimental</option>
            <option value="pending_payment">Pendente</option>
            <option value="inactive">Inativo</option>
          </select>
          <button class="rounded-xl border border-red-400/60 px-3 py-2 text-xs text-red-200" @click="removeStudent(student.id)">
            Remover
          </button>
        </div>
      </div>
    </div>

    <StudentOverviewModal
      v-if="overviewOpen"
      :student="selectedStudent"
      :overview="overview"
      @close="overviewOpen = false"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useTeacherStore } from '../../stores/teacher'
import StudentOverviewModal from '../../components/StudentOverviewModal.vue'

const teacher = useTeacherStore()
const students = computed(() => teacher.students)
const overview = ref<any>(null)
const selectedStudent = ref<any>(null)
const overviewOpen = ref(false)
const search = ref('')

const handleSearch = async () => {
  await teacher.fetchStudents(search.value)
}

const initials = (name: string) => {
  const parts = name.split(' ')
  return parts.length ? (parts[0][0] + (parts[1]?.[0] || '')).toUpperCase() : 'U'
}

const statusLabel = (status: string) => {
  const map: Record<string, string> = {
    active: 'Ativo',
    trial: 'Experimental',
    pending_payment: 'Pendente',
    inactive: 'Inativo'
  }
  return map[status] || status
}

const badgeClass = (status: string) => {
  if (status === 'active') return 'rounded-full bg-emerald-500/20 px-3 py-1 text-xs text-emerald-200'
  if (status === 'trial') return 'rounded-full bg-yellow-500/20 px-3 py-1 text-xs text-yellow-200'
  if (status === 'pending_payment') return 'rounded-full bg-red-500/20 px-3 py-1 text-xs text-red-200'
  return 'rounded-full bg-white/10 px-3 py-1 text-xs text-white/60'
}

const openOverview = async (student: any) => {
  selectedStudent.value = student
  overview.value = await teacher.fetchStudentOverview(student.id)
  overviewOpen.value = true
}

const resetPassword = async (id: number) => {
  await teacher.resetStudentPassword(id)
}

const updateStatus = async (id: number, status: string) => {
  await teacher.updateStudentStatus(id, status)
  await teacher.fetchStudents(search.value)
}

const removeStudent = async (id: number) => {
  await teacher.removeStudent(id)
}

onMounted(async () => {
  await teacher.fetchStudents()
})
</script>
