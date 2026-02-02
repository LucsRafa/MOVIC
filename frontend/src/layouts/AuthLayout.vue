<template>
  <div class="min-h-screen bg-gradient-to-b from-[#1a1f2e] via-[#2C3E60] to-[#27AE60]">
    <div class="mx-auto flex min-h-screen max-w-6xl items-center justify-center px-6 py-12">
      <div class="w-full max-w-md rounded-[28px] bg-white/95 p-8 shadow-2xl">
        <RouterView />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { RouterView } from 'vue-router'
</script>
