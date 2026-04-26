<template>
  <RouterLink
    class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 shadow-sm transition hover:border-slate-300 hover:bg-slate-200"
    to="/login"
  >
    <span>&larr;</span> Voltar para login
  </RouterLink>

  <div class="mt-6 text-center">
    <div class="mx-auto h-20 w-20 overflow-hidden rounded-full ring-4 ring-white/80 shadow-lg">
      <img
        alt="Avatar"
        class="h-full w-full object-cover"
        src="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?q=80&w=200&auto=format&fit=crop"
      />
    </div>
    <p class="mt-3 text-sm text-slate-500">@jefftrainer</p>
    <h1 class="mt-3 text-2xl font-semibold text-[#2C3E60]">Recuperar senha</h1>
    <p class="mt-1 text-sm text-slate-500">Digite seu email para receber o link de recuperação</p>
  </div>

  <form class="mt-6 space-y-4" @submit.prevent="submit">
    <p v-if="message" class="rounded-xl bg-emerald-50 px-4 py-2 text-sm text-emerald-700">
      {{ message }}
    </p>
    <p v-if="errorMessage" class="rounded-xl bg-red-50 px-4 py-2 text-sm text-red-600">
      {{ errorMessage }}
    </p>

    <label class="block text-left text-sm font-medium text-slate-600">Email:</label>
    <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
      <span class="text-slate-400">@</span>
      <input
        v-model="email"
        class="w-full bg-transparent text-sm outline-none"
        type="email"
        placeholder="seu@email.com"
      />
    </div>

    <button
      class="w-full rounded-xl bg-gradient-to-r from-[#27AE60] to-[#229954] px-4 py-3 text-sm font-semibold text-white shadow disabled:opacity-60"
      type="submit"
      :disabled="loading"
    >
      {{ loading ? 'Enviando...' : 'Enviar link de recuperação' }}
    </button>
  </form>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { extractApiErrorMessage } from '../utils/apiError'

const auth = useAuthStore()
const email = ref('')
const loading = ref(false)
const errorMessage = ref('')
const message = ref('')

const submit = async () => {
  errorMessage.value = ''
  message.value = ''

  if (!email.value.trim()) {
    errorMessage.value = 'Informe seu email.'
    return
  }

  loading.value = true

  try {
    const data = await auth.forgotPassword(email.value.trim())
    message.value = data.message
  } catch (error) {
    errorMessage.value = extractApiErrorMessage(error, 'Não foi possível enviar o link de recuperação.')
  } finally {
    loading.value = false
  }
}
</script>
