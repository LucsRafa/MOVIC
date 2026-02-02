<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4">
    <div class="w-full max-w-lg rounded-2xl bg-[#0f172a] p-4 text-white shadow-2xl">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-semibold">Comprovantes de Pagamento</h3>
          <p class="text-sm text-white/60">Visualize e baixe seus comprovantes anteriores</p>
        </div>
        <button class="text-white/70 hover:text-white" @click="$emit('close')">x</button>
      </div>

      <div class="mt-4 space-y-4">
        <div v-for="payment in payments" :key="payment.id" class="rounded-xl border border-white/10 bg-white/5 p-4">
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full border border-emerald-400/40 bg-emerald-500/10 text-center text-emerald-300">ok</div>
            <div>
              <p class="text-sm text-white">{{ formatDate(payment.paid_at) }}</p>
              <p class="text-xs text-white/60">{{ payment.method }}</p>
              <p class="text-sm text-white">R$ {{ formatAmount(payment.amount_cents) }}</p>
            </div>
          </div>
          <div class="mt-3 flex items-center justify-between">
            <span class="rounded-full bg-emerald-500/20 px-3 py-1 text-xs text-emerald-200">{{ payment.status }}</span>
            <button class="rounded-lg bg-white/10 px-3 py-2 text-xs text-sky-200" @click="$emit('detail', payment)">
              Ver Comprovante
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue'

defineProps<{ payments: any[] }>()

const formatDate = (date?: string) => (date ? new Date(date).toLocaleDateString('pt-BR') : '-')
const formatAmount = (cents: number) => (cents / 100).toFixed(2).replace('.', ',')

onMounted(() => {
  document.body.style.overflow = 'hidden'
})

onUnmounted(() => {
  document.body.style.overflow = ''
})
</script>
