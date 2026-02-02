<template>
  <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/70 px-4 py-8">
    <div class="w-full max-w-lg rounded-2xl bg-[#0f172a] p-4 text-white shadow-2xl">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="text-emerald-400">CFG</span>
          <h3 class="text-lg font-semibold">Configuracoes</h3>
        </div>
        <button class="text-white/70 hover:text-white" @click="$emit('close')">x</button>
      </div>
      <p class="text-sm text-white/60">Gerencie suas informacoes de conta</p>

      <div class="mt-5">
        <h4 class="text-sm font-semibold text-white/80">Foto de Perfil</h4>
        <div class="mt-3 flex flex-col items-center gap-3">
          <div class="flex h-20 w-20 items-center justify-center rounded-full bg-emerald-500 text-xl font-semibold">
            {{ initials }}
          </div>
          <label class="w-full cursor-pointer rounded-xl bg-fuchsia-600 px-4 py-3 text-center text-sm font-semibold">
            Escolher Foto
            <input class="hidden" type="file" accept="image/png,image/jpeg" @change="onFile" />
          </label>
          <p class="text-xs text-white/50">Formatos aceitos: JPG, PNG (Max: 5MB)</p>
        </div>
      </div>

      <div class="mt-6 border-t border-white/10 pt-4">
        <h4 class="text-sm font-semibold text-white/80">Alterar Nome</h4>
        <input v-model="name" class="mt-2 w-full rounded-xl bg-white/5 px-4 py-3 text-sm" />
        <button class="mt-3 w-full rounded-xl bg-orange-500 px-4 py-3 text-sm font-semibold" @click="updateName">
          Atualizar Nome
        </button>
      </div>

      <div class="mt-6 border-t border-white/10 pt-4">
        <h4 class="text-sm font-semibold text-white/80">Alterar Email</h4>
        <input class="mt-2 w-full rounded-xl bg-white/5 px-4 py-3 text-sm text-white/50" :value="user.email" readonly />
        <input v-model="email" class="mt-2 w-full rounded-xl bg-white/5 px-4 py-3 text-sm" placeholder="novo@email.com" />
        <button class="mt-3 w-full rounded-xl bg-sky-600 px-4 py-3 text-sm font-semibold" @click="updateEmail">
          Atualizar Email
        </button>
      </div>

      <div class="mt-6 border-t border-white/10 pt-4">
        <h4 class="text-sm font-semibold text-white/80">Alterar Senha</h4>
        <input v-model="currentPassword" class="mt-2 w-full rounded-xl bg-white/5 px-4 py-3 text-sm" type="password" placeholder="Senha atual" />
        <input v-model="newPassword" class="mt-2 w-full rounded-xl bg-white/5 px-4 py-3 text-sm" type="password" placeholder="Nova senha" />
        <input v-model="confirmPassword" class="mt-2 w-full rounded-xl bg-white/5 px-4 py-3 text-sm" type="password" placeholder="Confirmar nova senha" />
        <button class="mt-3 w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold" @click="updatePassword">
          Atualizar Senha
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue'

const props = defineProps<{ user: any }>()
const emit = defineEmits(['close', 'uploadAvatar', 'updateProfile', 'updatePassword'])

const name = ref(props.user?.name || '')
const email = ref('')
const currentPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')

const initials = computed(() => {
  const parts = (props.user?.name || '').split(' ')
  return parts.length ? (parts[0][0] + (parts[1]?.[0] || '')).toUpperCase() : 'U'
})

const onFile = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (file) emit('uploadAvatar', file)
}

const updateName = () => emit('updateProfile', { name: name.value })
const updateEmail = () => emit('updateProfile', { email: email.value })
const updatePassword = () =>
  emit('updatePassword', {
    current_password: currentPassword.value,
    password: newPassword.value,
    password_confirmation: confirmPassword.value
  })

onMounted(() => {
  document.body.style.overflow = 'hidden'
})

onUnmounted(() => {
  document.body.style.overflow = ''
})
</script>
