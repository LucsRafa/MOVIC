<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4">
    <div class="w-full max-w-lg rounded-2xl bg-[#0f172a] p-4 text-white shadow-2xl">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="text-sky-300">DOC</span>
          <h3 class="text-lg font-semibold">Comprovante de Pagamento</h3>
        </div>
        <button class="text-white/70 hover:text-white" @click="$emit('close')">x</button>
      </div>
      <p class="mt-1 text-sm text-white/60">{{ date }}</p>

      <div class="mt-4 rounded-xl border border-white/10 bg-white/5 p-4 text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-emerald-400/40 bg-emerald-500/10 text-emerald-300">ok</div>
        <p class="mt-3 text-lg">Pagamento Confirmado</p>
        <p class="text-sm text-white/60">Academia Movic</p>
      </div>

      <div class="mt-4 space-y-2 text-sm text-white/70">
        <div class="flex items-center justify-between border-b border-white/10 pb-2">
          <span>Data do Pagamento</span>
          <span class="text-white">{{ date }}</span>
        </div>
        <div class="flex items-center justify-between border-b border-white/10 pb-2">
          <span>Metodo de Pagamento</span>
          <span class="text-white">{{ payment.method }}</span>
        </div>
        <div class="flex items-center justify-between border-b border-white/10 pb-2">
          <span>Descricao</span>
          <span class="text-white">{{ payment.description || 'Mensalidade' }}</span>
        </div>
        <div class="flex items-center justify-between border-b border-white/10 pb-2">
          <span>Valor Total</span>
          <span class="text-white">R$ {{ formatAmount(payment.amount_cents) }}</span>
        </div>
        <div class="flex items-center justify-between border-b border-white/10 pb-2">
          <span>Status</span>
          <span class="rounded-full bg-emerald-500/20 px-2 py-1 text-xs text-emerald-200">{{ payment.status }}</span>
        </div>
        <div class="flex items-center justify-between">
          <span>ID da Transacao</span>
          <span class="text-white/60">{{ payment.transaction_id || '#000001-2025' }}</span>
        </div>
      </div>

      <div class="mt-4 grid gap-3 sm:grid-cols-2">
        <button class="rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm" @click="$emit('pdf')">
          Salvar em PDF
        </button>
        <button class="rounded-xl bg-sky-600 px-4 py-3 text-sm text-white" @click="$emit('email')">
          Enviar por Email
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue'

const props = defineProps<{ payment: any }>()

const formatAmount = (cents: number) => (cents / 100).toFixed(2).replace('.', ',')
const date = props.payment?.paid_at ? new Date(props.payment.paid_at).toLocaleDateString('pt-BR') : '-'

onMounted(() => {
  document.body.style.overflow = 'hidden'
})

onUnmounted(() => {
  document.body.style.overflow = ''
})
</script>
