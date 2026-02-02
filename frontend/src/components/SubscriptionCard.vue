<template>
  <div class="rounded-2xl bg-white/5 p-4">
    <div class="flex items-center gap-2 text-emerald-400">
      <span>PAY</span>
      <h3 class="text-lg font-semibold text-white">Mensalidade</h3>
    </div>

    <div class="mt-4 rounded-xl border border-amber-500/30 bg-amber-500/10 p-4">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm text-white/60">Status</p>
          <p class="mt-1 text-sm text-white">{{ statusLabel }}</p>
        </div>
        <span class="rounded-full bg-amber-500/20 px-3 py-1 text-xs text-amber-200">{{ statusLabel }}</span>
      </div>
      <div class="mt-3 text-sm text-white/70">Expira em <span class="text-white">{{ expiresAt || '-' }}</span></div>
    </div>

    <button
      class="mt-4 w-full rounded-xl bg-emerald-500 px-4 py-3 text-sm font-semibold text-white"
      @click="$emit('activate')"
    >
      Ativar Plano - R$ 150,00/mes
    </button>
    <button
      class="mt-3 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm font-semibold text-white"
      @click="$emit('receipts')"
    >
      Ver Comprovantes
    </button>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{ status?: string; expiresAt?: string | null }>()

const statusLabel = props.status || 'Experimental'
</script>
