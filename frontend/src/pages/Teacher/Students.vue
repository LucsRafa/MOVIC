<template>
  <div class="space-y-4">
    <div class="rounded-2xl bg-white/5 p-4">
      <h3 class="text-lg font-semibold">Alunos Ativos</h3>
      <p class="text-sm text-white/60">Gerencie seus alunos cadastrados</p>
      <div class="mt-3">
        <input
          v-model="search"
          class="app-input"
          placeholder="Buscar aluno..."
          @input="handleSearch"
        />
      </div>
    </div>

    <div v-if="students.length === 0" class="text-sm text-white/60">Nenhum aluno encontrado.</div>

    <div v-for="student in students" :key="student.id" class="rounded-2xl bg-white/5 p-4">
      <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        <div class="flex min-w-0 items-center gap-3">
          <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-sm font-semibold">
            {{ initials(student.name) }}
          </div>
          <div class="min-w-0">
            <p class="font-semibold">{{ student.name }}</p>
            <p class="text-xs text-white/50">Último treino: {{ student.last_workout_date || '-' }}</p>
            <div class="mt-2">
              <span :class="badgeClass(student.status)">{{ statusLabel(student.status) }}</span>
            </div>
          </div>
        </div>
        <div class="grid w-full gap-2 sm:grid-cols-2 xl:w-auto xl:grid-cols-[minmax(0,auto)_minmax(0,auto)_minmax(180px,220px)_minmax(0,auto)]">
          <button
            class="rounded-xl bg-white/10 px-3 py-2.5 text-xs hover:bg-white/15"
            :disabled="isPending(`overview-${student.id}`)"
            @click="openOverview(student)"
          >
            {{ isPending(`overview-${student.id}`) ? 'Carregando...' : 'Histórico' }}
          </button>
          <button
            class="rounded-xl bg-white/10 px-3 py-2.5 text-xs hover:bg-white/15"
            :disabled="isPending(`reset-${student.id}`)"
            @click="resetPassword(student.id)"
          >
            {{ isPending(`reset-${student.id}`) ? 'Enviando...' : 'Resetar senha' }}
          </button>
          <select
            class="app-select app-select--compact w-full"
            :value="student.status"
            :disabled="isPending(`status-${student.id}`)"
            @change="updateStatus(student.id, ($event.target as HTMLSelectElement).value)"
          >
            <option value="active">Ativo</option>
            <option value="trial">Experimental</option>
            <option value="pending_payment">Pendente</option>
            <option value="inactive">Inativo</option>
          </select>
          <button
            class="rounded-xl border border-red-400/60 px-3 py-2.5 text-xs text-red-200 hover:bg-red-500/10"
            :disabled="isPending(`remove-${student.id}`)"
            @click="removeStudent(student.id)"
          >
            {{ isPending(`remove-${student.id}`) ? 'Removendo...' : 'Remover' }}
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
import { useToastStore } from '../../stores/toast'

const teacher = useTeacherStore()
const toast = useToastStore()
const students = computed(() => teacher.students)
const overview = ref<any>(null)
const selectedStudent = ref<any>(null)
const overviewOpen = ref(false)
const search = ref('')
const pending = ref<Record<string, boolean>>({})

const setPending = (key: string, value: boolean) => {
  pending.value = {
    ...pending.value,
    [key]: value
  }
}

const isPending = (key: string) => Boolean(pending.value[key])

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
  const key = `overview-${student.id}`
  setPending(key, true)
  try {
    selectedStudent.value = student
    overview.value = await teacher.fetchStudentOverview(student.id)
    overviewOpen.value = true
  } finally {
    setPending(key, false)
  }
}

const resetPassword = async (id: number) => {
  const key = `reset-${id}`
  setPending(key, true)
  try {
    await teacher.resetStudentPassword(id)
    toast.push('Link de redefinição enviado para o aluno.', 'success')
  } finally {
    setPending(key, false)
  }
}

const updateStatus = async (id: number, status: string) => {
  const key = `status-${id}`
  setPending(key, true)
  try {
    await teacher.updateStudentStatus(id, status)
    await teacher.fetchStudents(search.value)
    toast.push('Status do aluno atualizado com sucesso.', 'success')
  } finally {
    setPending(key, false)
  }
}

const removeStudent = async (id: number) => {
  const key = `remove-${id}`
  setPending(key, true)
  try {
    await teacher.removeStudent(id)
    toast.push('Aluno removido com sucesso.', 'success')
  } finally {
    setPending(key, false)
  }
}

onMounted(async () => {
  await teacher.fetchStudents()
})
</script>

