<template>
  <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/70 px-4 py-8">
    <div class="w-full max-w-lg rounded-2xl bg-[#0f172a] p-4 text-white shadow-2xl">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">Cadastrar Exercicio</h3>
        <button class="text-white/70 hover:text-white" @click="$emit('close')">x</button>
      </div>
      <p class="text-sm text-white/60">Adicione um novo exercicio com video instrutivo</p>

      <p v-if="error" class="mt-3 rounded-xl bg-red-500/10 px-3 py-2 text-xs text-red-200">
        {{ error }}
      </p>

      <div class="mt-4 space-y-3">
        <input v-model="form.name" class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm" placeholder="Nome do exercicio" />
        <input v-model="form.category" class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm" placeholder="Grupo muscular" />
        <textarea v-model="form.description" class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm" placeholder="Instrucoes de execucao"></textarea>
        <input v-model="form.video_url" class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm" placeholder="URL do video" />
        <label class="block rounded-xl bg-white/5 px-4 py-3 text-xs">
          Video (opcional)
          <input class="mt-2 w-full" type="file" accept="video/*" @change="onFile" />
        </label>
        <button
          class="w-full rounded-xl bg-emerald-500 px-4 py-3 text-sm font-semibold disabled:opacity-50"
          type="button"
          :disabled="loading"
          @click="submit"
        >
          {{ loading ? 'Salvando...' : 'Cadastrar Exercicio' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'

const props = defineProps<{ loading?: boolean; error?: string }>()
const emit = defineEmits(['close', 'create'])
const fileRef = ref<File | null>(null)

const form = reactive({
  name: '',
  category: '',
  description: '',
  video_url: ''
})

const onFile = (event: Event) => {
  fileRef.value = (event.target as HTMLInputElement).files?.[0] || null
}

const submit = () => {
  if (!form.name) return
  const payload = new FormData()
  payload.append('name', form.name)
  if (form.category) payload.append('category', form.category)
  if (form.description) payload.append('description', form.description)
  if (form.video_url) payload.append('video_url', form.video_url)
  if (fileRef.value) payload.append('video_file', fileRef.value)
  emit('create', payload)
}
</script>
