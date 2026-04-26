<template>
  <div class="space-y-4">
    <div class="rounded-2xl bg-white/5 p-4">
      <h3 class="text-lg font-semibold">Controle de Pagamentos</h3>
      <p class="text-sm text-white/60">Acompanhe os pagamentos dos alunos</p>
    </div>

    <div v-for="payment in payments" :key="payment.student_id" class="rounded-2xl bg-white/5 p-4">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex min-w-0 items-center gap-3">
          <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-sm font-semibold">
            {{ initials(payment.name) }}
          </div>
          <div class="min-w-0">
            <p class="font-semibold">{{ payment.name }}</p>
            <p class="text-xs text-white/50">Mensalidade - R$ {{ formatPrice(payment.amount_cents) }}</p>
          </div>
        </div>
        <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
          <span :class="payment.status === 'paid' ? paidClass : pendingClass">
            {{ payment.status === 'paid' ? 'Pago' : 'Pendente' }}
          </span>
          <button
            class="rounded-xl bg-white/10 px-3 py-2.5 text-xs hover:bg-white/15"
            :disabled="registeringStudentId === payment.student_id"
            @click="openRegister(payment)"
          >
            {{ registeringStudentId === payment.student_id ? 'Registrando...' : 'Registrar' }}
          </button>
        </div>
      </div>
    </div>

    <PaymentRegisterModal
      v-if="selectedPayment"
      :payment="selectedPayment"
      :loading="registering"
      @close="selectedPayment = null"
      @submit="registerPayment"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useTeacherStore } from '../../stores/teacher'
import PaymentRegisterModal from '../../components/PaymentRegisterModal.vue'
import { useToastStore } from '../../stores/toast'

const teacher = useTeacherStore()
const toast = useToastStore()
const payments = computed(() => teacher.payments)
const selectedPayment = ref<any>(null)
const registering = ref(false)
const registeringStudentId = ref<number | null>(null)

const paidClass = 'rounded-full bg-emerald-500/20 px-2 py-1 text-xs text-emerald-200'
const pendingClass = 'rounded-full bg-red-500/20 px-2 py-1 text-xs text-red-200'

const initials = (name: string) => {
  const parts = name.split(' ')
  return parts.length ? (parts[0][0] + (parts[1]?.[0] || '')).toUpperCase() : 'U'
}

const formatPrice = (cents: number) => (cents / 100).toFixed(2).replace('.', ',')

const openRegister = (payment: any) => {
  selectedPayment.value = payment
}

const registerPayment = async (payload: any) => {
  registering.value = true
  registeringStudentId.value = payload.student_id
  try {
    await teacher.registerPayment(payload)
    selectedPayment.value = null
    await teacher.fetchPayments()
    toast.push('Pagamento registrado com sucesso.', 'success')
  } finally {
    registering.value = false
    registeringStudentId.value = null
  }
}

onMounted(async () => {
  await teacher.fetchPayments()
})
</script>
