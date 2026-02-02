<template>
  <div class="min-h-screen">
    <Navbar v-if="showNavbar" />
    <main :class="mainClass">
      <RouterView />
    </main>
  </div>
</template>

<script setup lang="ts">
import Navbar from '../components/Navbar.vue'
import { RouterView } from 'vue-router'
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const isStudent = computed(() => route.path.startsWith('/student'))
const showNavbar = computed(() => !isStudent.value)
const mainClass = computed(() =>
  isStudent.value ? 'min-h-screen' : 'mx-auto max-w-6xl px-6 py-8'
)
</script>
