<template>
  <nav class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-3">
    <RouterLink
      v-for="item in items"
      :key="item.path"
      :class="linkClass(item.path)"
      :to="item.path"
    >
      <span
        v-if="item.badge"
        class="absolute right-3 top-3 rounded-full bg-red-500 px-2 py-0.5 text-[11px] font-semibold text-white"
      >
        {{ item.badge }}
      </span>

      <component :is="item.icon" class="h-6 w-6" />
      <span class="text-sm font-medium">{{ item.label }}</span>
    </RouterLink>
  </nav>
</template>

<script setup lang="ts">
import {
  BanknotesIcon,
  BoltIcon,
  CalendarDaysIcon,
  ClockIcon,
  HomeIcon,
  UserGroupIcon
} from '@heroicons/vue/24/outline'
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps<{ requests: number }>()
const route = useRoute()

const items = computed(() => [
  { label: 'Dashboard', path: '/teacher/dashboard', icon: HomeIcon, badge: props.requests || null },
  { label: 'Alunos', path: '/teacher/students', icon: UserGroupIcon, badge: null },
  { label: 'Treinos', path: '/teacher/workouts', icon: CalendarDaysIcon, badge: null },
  { label: 'Exercícios', path: '/teacher/exercises', icon: BoltIcon, badge: null },
  { label: 'Pagamentos', path: '/teacher/payments', icon: BanknotesIcon, badge: null },
  { label: 'Histórico', path: '/teacher/history', icon: ClockIcon, badge: null }
])

const linkClass = (path: string) => {
  const active = path === '/teacher/dashboard'
    ? route.path.startsWith('/teacher/dashboard') || route.path.startsWith('/teacher/requests')
    : route.path.startsWith(path)

  return [
    'relative flex min-h-[92px] flex-col items-center justify-center gap-2 rounded-2xl border px-4 py-5 text-center transition',
    active
      ? 'border-emerald-400/50 bg-emerald-500/15 text-white shadow-[0_0_0_1px_rgba(16,185,129,0.24)]'
      : 'border-white/10 bg-white/5 text-white/75 hover:border-white/15 hover:bg-white/10 hover:text-white'
  ]
}
</script>
