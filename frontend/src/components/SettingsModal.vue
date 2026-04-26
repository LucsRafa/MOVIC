<template>
  <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/70 px-4 py-8">
    <div class="w-full max-w-lg rounded-2xl bg-[#0f172a] p-4 text-white shadow-2xl sm:p-5">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path
              d="M10.325 4.317a1.724 1.724 0 0 1 3.35 0l.147.68a1.724 1.724 0 0 0 2.591 1.066l.602-.355a1.724 1.724 0 0 1 2.29.63l.38.658a1.724 1.724 0 0 1-.63 2.29l-.601.355a1.724 1.724 0 0 0 0 2.978l.602.355a1.724 1.724 0 0 1 .63 2.29l-.38.658a1.724 1.724 0 0 1-2.29.63l-.602-.355a1.724 1.724 0 0 0-2.59 1.066l-.148.68a1.724 1.724 0 0 1-3.35 0l-.147-.68a1.724 1.724 0 0 0-2.591-1.066l-.602.355a1.724 1.724 0 0 1-2.29-.63l-.38-.658a1.724 1.724 0 0 1 .63-2.29l.602-.355a1.724 1.724 0 0 0 0-2.978l-.601-.355a1.724 1.724 0 0 1-.63-2.29l.38-.658a1.724 1.724 0 0 1 2.29-.63l.601.355a1.724 1.724 0 0 0 2.592-1.066l.147-.68Z"
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
            />
            <path
              d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
            />
          </svg>
          <h3 class="text-lg font-semibold">Configurações</h3>
        </div>
        <button class="text-white/70 hover:text-white" @click="$emit('close')">x</button>
      </div>
      <p class="text-sm text-white/60">Gerencie suas informações de conta</p>

      <div class="mt-5">
        <h4 class="text-sm font-semibold text-white/80">Foto de perfil</h4>
        <div class="mt-3 flex flex-col items-center gap-3">
          <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-full bg-emerald-500 text-xl font-semibold text-white">
            <img v-if="resolvedAvatarUrl" :src="resolvedAvatarUrl" alt="Foto de perfil" class="h-full w-full object-cover" />
            <span v-else>{{ initials }}</span>
          </div>
          <label class="w-full cursor-pointer rounded-xl bg-fuchsia-600 px-4 py-3 text-center text-sm font-semibold hover:bg-fuchsia-500">
            Escolher foto
            <input class="hidden" type="file" accept=".jpg,.jpeg,.png,image/jpeg,image/png" @change="onFile" />
          </label>
          <p class="text-xs text-white/50">Formatos aceitos: JPG, JPEG e PNG até 5 MB</p>
        </div>
      </div>

      <div class="mt-6 border-t border-white/10 pt-4">
        <h4 class="text-sm font-semibold text-white/80">Alterar nome</h4>
        <input v-model="name" class="mt-2 w-full rounded-xl bg-white/5 px-4 py-3 text-sm" />
        <button class="mt-3 w-full rounded-xl bg-orange-500 px-4 py-3 text-sm font-semibold hover:bg-orange-400" @click="updateName">
          Atualizar nome
        </button>
      </div>

      <div class="mt-6 border-t border-white/10 pt-4">
        <h4 class="text-sm font-semibold text-white/80">Alterar e-mail</h4>
        <input class="mt-2 w-full rounded-xl bg-white/5 px-4 py-3 text-sm text-white/50" :value="user.email" readonly />
        <input v-model="email" class="mt-2 w-full rounded-xl bg-white/5 px-4 py-3 text-sm" placeholder="novo@email.com" />
        <button class="mt-3 w-full rounded-xl bg-sky-600 px-4 py-3 text-sm font-semibold hover:bg-sky-500" @click="updateEmail">
          Atualizar e-mail
        </button>
      </div>

      <div class="mt-6 border-t border-white/10 pt-4">
        <h4 class="text-sm font-semibold text-white/80">Alterar senha</h4>
        <input v-model="currentPassword" class="mt-2 w-full rounded-xl bg-white/5 px-4 py-3 text-sm" type="password" placeholder="Senha atual" />
        <input v-model="newPassword" class="mt-2 w-full rounded-xl bg-white/5 px-4 py-3 text-sm" type="password" placeholder="Nova senha" />
        <input v-model="confirmPassword" class="mt-2 w-full rounded-xl bg-white/5 px-4 py-3 text-sm" type="password" placeholder="Confirmar nova senha" />
        <button class="mt-3 w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold hover:bg-emerald-500" @click="updatePassword">
          Atualizar senha
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { resolveMediaUrl } from '../utils/media'

const props = defineProps<{ user: any }>()
const emit = defineEmits(['close', 'uploadAvatar', 'updateProfile', 'updatePassword'])

const name = ref(props.user?.name || '')
const email = ref('')
const currentPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')

const initials = computed(() => {
  const parts = (props.user?.name || '').trim().split(/\s+/).filter(Boolean)
  return parts.length ? (parts[0][0] + (parts[1]?.[0] || '')).toUpperCase() : 'U'
})

const resolvedAvatarUrl = computed(() => resolveMediaUrl(props.user?.avatar_url))

const onFile = (event: Event) => {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0]

  if (file) {
    emit('uploadAvatar', file)
  }

  input.value = ''
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
