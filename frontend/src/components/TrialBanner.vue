<template>
  <div class="rounded-2xl border border-amber-400/30 bg-amber-500/10 p-4 text-amber-200">
    <div class="flex items-start gap-3">
      <div class="mt-1 h-7 w-7 rounded-full border border-amber-400/40 bg-amber-500/20 text-center text-sm font-bold">!</div>
      <div>
        <p class="font-semibold">Periodo Experimental</p>
        <p class="text-sm text-amber-100/80">
          Voce esta em periodo experimental ate {{ date }}. Realize o pagamento para continuar usando o sistema apos esta data.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{ date: string }>()
</script>
