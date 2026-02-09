<template>
  <div class="space-y-4">
    <div class="rounded-2xl bg-white/5 p-4">
      <h3 class="text-lg font-semibold">Solicitacoes de Cadastro</h3>
      <p class="text-sm text-white/60">Aprove novos alunos e ative periodo experimental.</p>
    </div>

    <div v-if="requests.length === 0" class="text-sm text-white/60">Nenhuma solicitacao no momento.</div>

    <div v-for="request in requests" :key="request.student_id" class="rounded-2xl bg-white/5 p-4">
      <div class="flex items-center justify-between">
        <div>
          <p class="font-semibold">{{ request.name }}</p>
          <p class="text-xs text-white/60">{{ request.email }} · {{ request.phone || 'Sem telefone' }}</p>
          <p class="mt-1 text-xs text-white/50">Solicitacao em: {{ request.requested_at }}</p>
        </div>
        <div class="flex items-center gap-2">
          <button class="rounded-xl bg-emerald-500 px-4 py-2 text-xs font-semibold" @click="approve(request.student_id)">
            Ativar
          </button>
          <button class="rounded-xl border border-white/20 px-4 py-2 text-xs" @click="reject(request.student_id)">
            Rejeitar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useTeacherStore } from '../../stores/teacher'

const teacher = useTeacherStore()
const requests = computed(() => teacher.requests)

const approve = async (id: number) => {
  await teacher.approveRequest(id)
}

const reject = async (id: number) => {
  await teacher.rejectRequest(id)
}

onMounted(async () => {
  await teacher.fetchRequests()
})
</script>
