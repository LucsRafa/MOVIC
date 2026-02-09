<template>
  <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/70 px-4 py-8">
    <div class="w-full max-w-lg rounded-2xl bg-[#0f172a] p-4 text-white shadow-2xl">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">Registrar Pagamento</h3>
        <button class="text-white/70 hover:text-white" @click="$emit('close')">x</button>
      </div>
      <p class="text-sm text-white/60">Aluno: {{ payment.name }}</p>

      <div class="mt-4 space-y-3">
        <input v-model.number="form.amount_cents" type="number" class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm" />
        <select v-model="form.method" class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm">
          <option value="pix">Pix</option>
          <option value="card">Cartao</option>
        </select>
        <input v-model="form.description" class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm" placeholder="Descricao" />
        <input v-model="form.transaction_id" class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm" placeholder="ID da transacao" />
        <button class="w-full rounded-xl bg-emerald-500 px-4 py-3 text-sm font-semibold" @click="submit">
          Registrar Pagamento
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue'

const props = defineProps<{ payment: any }>()
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
