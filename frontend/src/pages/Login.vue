<template>
  <div class="text-center">
    <div class="mx-auto h-20 w-20 overflow-hidden rounded-full ring-4 ring-white/80 shadow-lg">
      <img
        alt="Avatar"
        class="h-full w-full object-cover"
        src="/img/perfil.jpg"
      />
    </div>
    <p class="mt-3 text-sm text-slate-500">@jefftrainer</p>
    <h1 class="mt-3 text-2xl font-semibold text-[#2C3E60]">Bem-vindo</h1>
    <p class="mt-1 text-sm text-slate-500">Faça login para continuar</p>
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
      <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path
          d="M3 6.75 10.94 12a2 2 0 0 0 2.12 0L21 6.75M4.5 19.5h15A1.5 1.5 0 0 0 21 18V6A1.5 1.5 0 0 0 19.5 4.5h-15A1.5 1.5 0 0 0 3 6v12A1.5 1.5 0 0 0 4.5 19.5Z"
          stroke="currentColor"
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="1.5"
        />
      </svg>
      <input
        v-model="email"
        class="w-full bg-transparent text-sm outline-none"
        type="email"
        autocomplete="email"
        placeholder="seu@email.com"
      />
    </div>

    <label class="block text-left text-sm font-medium text-slate-600">Senha:</label>
    <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
      <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path
          d="M16.5 10.5V8.25a4.5 4.5 0 1 0-9 0v2.25M6.75 19.5h10.5A1.5 1.5 0 0 0 18.75 18V12A1.5 1.5 0 0 0 17.25 10.5H6.75A1.5 1.5 0 0 0 5.25 12v6A1.5 1.5 0 0 0 6.75 19.5Z"
          stroke="currentColor"
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="1.5"
        />
      </svg>
      <input
        v-model="password"
        class="w-full bg-transparent text-sm outline-none"
        :type="showPassword ? 'text' : 'password'"
        autocomplete="current-password"
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

    <label class="flex items-center gap-3 text-left text-sm text-slate-600">
      <span class="relative flex h-4 w-4 items-center justify-center">
        <input
          v-model="rememberLogin"
          class="peer h-4 w-4 appearance-none rounded border border-slate-300 bg-white transition-colors focus:outline-none focus:ring-2"
          :class="
            role === 'teacher'
              ? 'checked:border-[#2C3E60] checked:bg-[#2C3E60] focus:ring-[#2C3E60]/30'
              : 'checked:border-[#27AE60] checked:bg-[#27AE60] focus:ring-[#27AE60]/30'
          "
          type="checkbox"
        />
        <svg
          class="pointer-events-none absolute h-3 w-3 text-white opacity-0 transition-opacity peer-checked:opacity-100"
          viewBox="0 0 20 20"
          fill="none"
          aria-hidden="true"
        >
          <path
            d="m5 10 3 3 7-7"
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
          />
        </svg>
      </span>
      <span>Lembrar meu login</span>
    </label>

    <RouterLink
      class="block text-right text-sm font-semibold"
      :class="role === 'teacher' ? 'text-[#2C3E60] hover:text-[#1a2332]' : 'text-[#27AE60] hover:text-[#229954]'"
      to="/forgot-password"
    >
      Esqueci minha senha
    </RouterLink>

    <button
      class="w-full rounded-xl px-4 py-3 text-sm font-semibold text-white shadow transition hover:opacity-95 disabled:cursor-not-allowed disabled:opacity-60"
      :class="
        role === 'teacher'
          ? 'bg-gradient-to-r from-[#2C3E60] to-[#1a2332]'
          : 'bg-gradient-to-r from-[#27AE60] to-[#229954]'
      "
      type="submit"
      :disabled="loading"
    >
      {{ loading ? 'Entrando...' : 'Entrar' }}
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
import { onMounted, ref, watch } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { extractApiErrorMessage } from '../utils/apiError'

const REMEMBERED_EMAIL_KEY = 'movic.remembered_email'

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const rememberLogin = ref(false)
const role = ref<'teacher' | 'student'>('student')
const auth = useAuthStore()
const router = useRouter()
const errorMessage = ref('')
const loading = ref(false)

watch(rememberLogin, (value) => {
  if (!value) {
    localStorage.removeItem(REMEMBERED_EMAIL_KEY)
  }
})

onMounted(() => {
  const rememberedEmail = localStorage.getItem(REMEMBERED_EMAIL_KEY)
  if (rememberedEmail) {
    email.value = rememberedEmail
    rememberLogin.value = true
  }
})

const submit = async () => {
  errorMessage.value = ''

  if (!email.value.trim()) {
    errorMessage.value = 'Informe seu email.'
    return
  }

  if (!password.value) {
    errorMessage.value = 'Informe sua senha.'
    return
  }

  if (rememberLogin.value) {
    localStorage.setItem(REMEMBERED_EMAIL_KEY, email.value.trim())
  } else {
    localStorage.removeItem(REMEMBERED_EMAIL_KEY)
  }

  loading.value = true

  try {
    await auth.login({ email: email.value.trim(), password: password.value })

    const actualRole = auth.user?.role
    if (actualRole && actualRole !== role.value) {
      await auth.logout()
      errorMessage.value =
        role.value === 'student'
          ? 'Este usuário é professor. Use a opção Professor para entrar.'
          : 'Este usuário é aluno. Use a opção Aluno para entrar.'
      return
    }

    if (actualRole === 'teacher') {
      router.push('/teacher/dashboard')
    } else {
      router.push('/student/dashboard')
    }
  } catch (error) {
    errorMessage.value = extractApiErrorMessage(error, 'Não foi possível fazer login.')
  } finally {
    loading.value = false
  }
}
</script>
