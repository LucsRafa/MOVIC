<template>
  <div class="space-y-4">
    <div class="rounded-2xl bg-white/5 p-4">
      <h3 class="text-lg font-semibold">Controle de Pagamentos</h3>
      <p class="text-sm text-white/60">Acompanhe os pagamentos dos alunos</p>
    </div>

    <div v-for="payment in payments" :key="payment.student_id" class="rounded-2xl bg-white/5 p-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-sm font-semibold">
            {{ initials(payment.name) }}
          </div>
          <div>
            <p class="font-semibold">{{ payment.name }}</p>
            <p class="text-xs text-white/50">Mensalidade - R$ {{ formatPrice(payment.amount_cents) }}</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <span :class="payment.status === 'paid' ? paidClass : pendingClass">
            {{ payment.status === 'paid' ? 'Pago' : 'Pendente' }}
          </span>
          <button class="rounded-xl bg-white/10 px-3 py-2 text-xs" @click="openRegister(payment)">
            Registrar
          </button>
        </div>
      </div>
    </div>

    <PaymentRegisterModal
      v-if="selectedPayment"
      :payment="selectedPayment"
      @close="selectedPayment = null"
      @submit="registerPayment"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useTeacherStore } from '../../stores/teacher'
import PaymentRegisterModal from '../../components/PaymentRegisterModal.vue'

const teacher = useTeacherStore()
const payments = computed(() => teacher.payments)
const selectedPayment = ref<any>(null)

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
  await teacher.registerPayment(payload)
  selectedPayment.value = null
  await teacher.fetchPayments()
}

onMounted(async () => {
  await teacher.fetchPayments()
})
</script>
