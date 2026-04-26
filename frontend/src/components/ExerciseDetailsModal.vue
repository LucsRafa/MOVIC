<template>
  <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/70 px-4 py-8">
    <div class="w-full max-w-lg rounded-2xl bg-[#0f172a] p-4 text-white shadow-2xl sm:p-5">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">Detalhes do exercício</h3>
        <button class="text-white/70 hover:text-white" @click="$emit('close')">x</button>
      </div>

      <p v-if="error" class="mt-3 rounded-xl bg-red-500/10 px-3 py-2 text-xs text-red-200">
        {{ error }}
      </p>

      <div class="mt-4 space-y-3">
        <input v-model="form.name" class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm" placeholder="Nome do exercício" />
        <input v-model="form.category" class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm" placeholder="Grupo muscular" />
        <textarea v-model="form.description" class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm" placeholder="Instruções de execução"></textarea>
        <input
          v-model="form.video_url"
          class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm disabled:opacity-60"
          placeholder="URL do vídeo"
          :disabled="!!fileRef"
        />

        <label class="block rounded-xl bg-white/5 px-4 py-3 text-xs">
          Trocar vídeo (opcional)
          <input class="mt-2 w-full" type="file" accept="video/mp4,video/quicktime,video/x-msvideo" @change="onFile" />
          <span v-if="fileRef" class="mt-2 block text-white/60">{{ fileRef.name }}</span>
        </label>

        <label class="flex items-center gap-2 text-xs text-white/70">
          <input v-model="form.remove_video" type="checkbox" />
          Remover vídeo atual
        </label>

        <div class="rounded-2xl bg-white/5 p-3 text-center">
          <video v-if="previewUrl" class="w-full rounded-xl" controls>
            <source :src="previewUrl" />
          </video>
          <div v-else class="text-sm text-white/50">Sem vídeo cadastrado.</div>
        </div>
      </div>

      <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center">
        <button class="flex-1 rounded-xl bg-white/10 px-4 py-3 text-sm" @click="$emit('close')">Fechar</button>
        <button
          class="flex-1 rounded-xl bg-emerald-500 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-400 disabled:hover:bg-emerald-500"
          :disabled="loading"
          @click="submitUpdate"
        >
          {{ loading ? 'Salvando...' : 'Salvar' }}
        </button>
        <button class="flex-1 rounded-xl border border-red-400/60 px-4 py-3 text-sm text-red-200 hover:bg-red-500/10" @click="$emit('delete', exercise.id)">
          Excluir
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue'

const props = defineProps<{ exercise: any; loading?: boolean; error?: string }>()
const emit = defineEmits(['close', 'delete', 'update'])

const fileRef = ref<File | null>(null)

const form = reactive({
  name: '',
  category: '',
  description: '',
  video_url: '',
  remove_video: false
})

watch(
  () => props.exercise,
  (exercise) => {
    form.name = exercise?.name || ''
    form.category = exercise?.category || ''
    form.description = exercise?.description || ''
    form.video_url = exercise?.video_url || ''
    form.remove_video = false
    fileRef.value = null
  },
  { immediate: true }
)

const previewUrl = computed(() => {
  if (fileRef.value) {
    return URL.createObjectURL(fileRef.value)
  }

  if (form.remove_video) {
    return ''
  }

  return form.video_url
})

const onFile = (event: Event) => {
  fileRef.value = (event.target as HTMLInputElement).files?.[0] || null
  if (fileRef.value) {
    form.video_url = ''
    form.remove_video = false
  }
}

const submitUpdate = () => {
  const payload = new FormData()
  payload.append('name', form.name)
  payload.append('category', form.category)
  payload.append('description', form.description)

  if (form.video_url && !fileRef.value && !form.remove_video) {
    payload.append('video_url', form.video_url)
  }

  if (fileRef.value) {
    payload.append('video_file', fileRef.value)
  }

  if (form.remove_video) {
    payload.append('remove_video', '1')
  }

  emit('update', props.exercise.id, payload)
}
</script>
