<template>
  <div class="space-y-4">
    <div class="rounded-2xl bg-white/5 p-4">
      <h3 class="text-lg font-semibold">Solicitações de cadastro</h3>
      <p class="text-sm text-white/60">Aprove novos alunos e ative o período experimental.</p>
    </div>

    <div v-if="requests.length === 0" class="text-sm text-white/60">Nenhuma solicitação no momento.</div>

    <div v-for="request in requests" :key="request.student_id" class="rounded-2xl bg-white/5 p-4">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <p class="font-semibold">{{ request.name }}</p>
          <p class="text-xs text-white/60">{{ request.email }} · {{ request.phone || 'Sem telefone' }}</p>
          <p class="mt-1 text-xs text-white/50">Solicitação em: {{ request.requested_at }}</p>
        </div>
        <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
          <button
            class="rounded-xl bg-emerald-500 px-4 py-2.5 text-xs font-semibold hover:bg-emerald-400 disabled:hover:bg-emerald-500"
            :disabled="pendingAction === `approve-${request.student_id}`"
            @click="approve(request.student_id)"
          >
            {{ pendingAction === `approve-${request.student_id}` ? 'Aprovando...' : 'Ativar' }}
          </button>
          <button
            class="rounded-xl border border-white/20 px-4 py-2.5 text-xs hover:bg-white/10"
            :disabled="pendingAction === `reject-${request.student_id}`"
            @click="reject(request.student_id)"
          >
            {{ pendingAction === `reject-${request.student_id}` ? 'Rejeitando...' : 'Rejeitar' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useTeacherStore } from '../../stores/teacher'
import { useToastStore } from '../../stores/toast'

const teacher = useTeacherStore()
const toast = useToastStore()
const requests = computed(() => teacher.requests)
const pendingAction = ref('')

const approve = async (id: number) => {
  pendingAction.value = `approve-${id}`
  try {
    await teacher.approveRequest(id)
    toast.push('Aluno aprovado com sucesso.', 'success')
  } finally {
    pendingAction.value = ''
  }
}

const reject = async (id: number) => {
  pendingAction.value = `reject-${id}`
  try {
    await teacher.rejectRequest(id)
    toast.push('Solicitação rejeitada com sucesso.', 'success')
  } finally {
    pendingAction.value = ''
  }
}

onMounted(async () => {
  await teacher.fetchRequests()
})
</script>
