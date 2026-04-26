<template>
  <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/70 px-4 py-8">
    <div class="w-full max-w-lg rounded-2xl bg-[#0f172a] p-4 text-white shadow-2xl sm:p-5">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">Registrar Pagamento</h3>
        <button class="text-white/70 hover:text-white" @click="$emit('close')">x</button>
      </div>
      <p class="text-sm text-white/60">Aluno: {{ payment.name }}</p>

      <div class="mt-4 space-y-3">
        <input v-model.number="form.amount_cents" type="number" class="app-input" />
        <select v-model="form.method" class="app-select">
          <option value="pix">Pix</option>
          <option value="card">Cartão</option>
        </select>
        <input v-model="form.description" class="app-input" placeholder="Descrição" />
        <input v-model="form.transaction_id" class="app-input" placeholder="ID da transação" />
        <button
          class="w-full rounded-xl bg-emerald-500 px-4 py-3 text-sm font-semibold hover:bg-emerald-400 disabled:hover:bg-emerald-500"
          :disabled="loading"
          @click="submit"
        >
          {{ loading ? 'Registrando...' : 'Registrar pagamento' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue'

const props = defineProps<{ payment: any; loading?: boolean }>()
const emit = defineEmits(['close', 'submit'])

const form = reactive({
  student_id: props.payment.student_id,
  amount_cents: props.payment.amount_cents || 15000,
  method: 'pix',
  description: 'Mensalidade',
  transaction_id: ''
})

const submit = () => {
  emit('submit', { ...form })
}
</script>
