<template>
  <div class="rounded-2xl border border-black/10 bg-white/80 p-6 shadow">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm uppercase tracking-[0.2em] text-black/50">Dia {{ day.weekday }}</p>
        <h3 class="text-xl font-semibold">{{ day.title }}</h3>
      </div>
      <slot name="actions" />
    </div>
    <div class="mt-4 space-y-2">
      <WorkoutItemRow v-for="item in day.items" :key="item.id" :item="item" @toggle="onToggle" />
    </div>
  </div>
</template>

<script setup lang="ts">
import WorkoutItemRow from './WorkoutItemRow.vue'

defineProps<{ day: any }>()
const emit = defineEmits<{ (e: 'toggle', item: any, value: boolean): void }>()

const onToggle = (item: any, value: boolean) => {
  emit('toggle', item, value)
}
</script>
