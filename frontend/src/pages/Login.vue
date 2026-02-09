<template>
  <div class="text-center">
    <div class="mx-auto h-20 w-20 overflow-hidden rounded-full ring-4 ring-white/80 shadow-lg">
      <img
        alt="Avatar"
        class="h-full w-full object-cover"
        src="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?q=80&w=200&auto=format&fit=crop"
      />
    </div>
    <p class="mt-3 text-sm text-slate-500">@jefftrainer</p>
    <h1 class="mt-3 text-2xl font-semibold text-[#2C3E60]">Bem-vindo</h1>
    <p class="mt-1 text-sm text-slate-500">Faca login para continuar</p>
  </div>

  <div class="mt-6 rounded-full bg-slate-100 p-1">
    <button
      class="w-1/2 rounded-full px-4 py-2 text-sm font-semibold"
      :class="role === 'student' ? 'bg-gradient-to-r from-[#27AE60] to-[#229954] text-white shadow' : 'text-slate-600'"
      @click="role = 'student'"
    >
      Aluno
    </button>
    <button
      class="w-1/2 rounded-full px-4 py-2 text-sm font-semibold"
      :class="role === 'teacher' ? 'bg-gradient-to-r from-[#2C3E60] to-[#1a2332] text-white shadow' : 'text-slate-600'"
      @click="role = 'teacher'"
    >
      Professor
    </button>
  </div>

  <form class="mt-6 space-y-4" @submit.prevent="submit">
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

    <label class="block text-left text-sm font-medium text-slate-600">Senha:</label>
    <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
      <span class="text-slate-400">*</span>
      <input
        v-model="password"
        class="w-full bg-transparent text-sm outline-none"
        :type="showPassword ? 'text' : 'password'"
        placeholder="******"
      />
      <button
        class="text-slate-400 hover:text-slate-600"
        type="button"
        @click="showPassword = !showPassword"
        aria-label="Mostrar senha"
      >
        <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
          <path
            d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"
            stroke="currentColor"
            stroke-width="1.5"
          />
          <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5" />
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
          <path
            d="M3 5l18 14"
            stroke="currentColor"
            stroke-width="1.5"
            stroke-linecap="round"
          />
          <path
            d="M2 12s3.5-6 10-6c2.1 0 3.9.6 5.4 1.5M22 12s-3.5 6-10 6c-2.3 0-4.3-.7-5.9-1.7"
            stroke="currentColor"
            stroke-width="1.5"
            stroke-linecap="round"
          />
        </svg>
      </button>
    </div>

    <RouterLink
      class="block text-right text-sm font-semibold"
      :class="role === 'teacher' ? 'text-[#2C3E60] hover:text-[#1a2332]' : 'text-[#27AE60] hover:text-[#229954]'"
      to="/forgot-password"
    >
      Esqueci minha senha
    </RouterLink>

    <button
      class="w-full rounded-xl px-4 py-3 text-sm font-semibold text-white shadow"
      :class="
        role === 'teacher'
          ? 'bg-gradient-to-r from-[#2C3E60] to-[#1a2332]'
          : 'bg-gradient-to-r from-[#27AE60] to-[#229954]'
      "
      type="submit"
    >
      Entrar
    </button>
  </form>

  <p class="mt-6 text-center text-sm text-slate-500">
    Primeiro acesso?
    <RouterLink
      class="font-semibold"
      :class="role === 'teacher' ? 'text-[#2C3E60]' : 'text-[#27AE60]'"
      to="/register"
    >
      Cadastre-se aqui
    </RouterLink>
  </p>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const role = ref<'teacher' | 'student'>('student')
const auth = useAuthStore()
const router = useRouter()
const errorMessage = ref('')

const submit = async () => {
  errorMessage.value = ''
  await auth.login({ email: email.value, password: password.value })
  const actualRole = auth.user?.role
  if (actualRole && actualRole !== role.value) {
    await auth.logout()
    errorMessage.value =
      role.value === 'student'
        ? 'Este usuario e professor. Use a opcao Professor para entrar.'
        : 'Este usuario e aluno. Use a opcao Aluno para entrar.'
    return
  }
  if (actualRole === 'teacher') {
    router.push('/teacher/dashboard')
  } else {
    router.push('/student/dashboard')
  }
}
</script>
