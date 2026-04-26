<template>
  <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/70 px-4 py-8">
    <div class="w-full max-w-lg rounded-2xl bg-[#0f172a] p-4 text-white shadow-2xl sm:p-5">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">Cadastrar exercício</h3>
        <button class="text-white/70 hover:text-white" @click="$emit('close')">x</button>
      </div>
      <p class="text-sm text-white/60">Adicione um novo exercício com vídeo instrutivo</p>

      <p v-if="error || localError" class="mt-3 rounded-xl bg-red-500/10 px-3 py-2 text-xs text-red-200">
        {{ localError || error }}
      </p>

      <div class="mt-4 space-y-3">
        <input v-model="form.name" class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm" placeholder="Nome do exercício" />
        <input v-model="form.category" class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm" placeholder="Grupo muscular" />
        <textarea v-model="form.description" class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm" placeholder="Instruções de execução"></textarea>
        <input
          v-model="form.video_url"
          class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm"
          placeholder="URL do vídeo"
          :disabled="!!fileRef"
        />
        <label class="block rounded-xl bg-white/5 px-4 py-3 text-xs">
          Vídeo (opcional)
          <input class="mt-2 w-full" type="file" accept="video/mp4,video/quicktime,video/x-msvideo" @change="onFile" />
          <span v-if="fileRef" class="mt-2 block text-white/60">{{ fileRef.name }}</span>
        </label>
        <button
          class="w-full rounded-xl bg-emerald-500 px-4 py-3 text-sm font-semibold hover:bg-emerald-400 disabled:hover:bg-emerald-500"
          type="button"
          :disabled="loading"
          @click="submit"
        >
          {{ loading ? 'Salvando...' : 'Cadastrar exercício' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'

defineProps<{ loading?: boolean; error?: string }>()
const emit = defineEmits(['close', 'create'])
const fileRef = ref<File | null>(null)
const localError = ref('')

const form = reactive({
  name: '',
  category: '',
  description: '',
  video_url: ''
})

const onFile = (event: Event) => {
  fileRef.value = (event.target as HTMLInputElement).files?.[0] || null
  localError.value = ''
  if (fileRef.value) {
    form.video_url = ''
  }
}

const submit = () => {
  localError.value = ''
  if (!form.name) {
    localError.value = 'Informe o nome do exercício.'
    return
  }
  if (!fileRef.value && form.video_url && !/^https?:\/\//i.test(form.video_url)) {
    localError.value = 'A URL do vídeo deve começar com http ou https.'
    return
  }
  const payload = new FormData()
  payload.append('name', form.name)
  if (form.category) payload.append('category', form.category)
  if (form.description) payload.append('description', form.description)
  if (form.video_url && !fileRef.value) payload.append('video_url', form.video_url)
  if (fileRef.value) payload.append('video_file', fileRef.value)
  emit('create', payload)
}
</script>
