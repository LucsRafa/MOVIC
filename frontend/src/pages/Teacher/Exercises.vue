<template>
  <section>
    <h1 class="text-3xl font-semibold">Exercicios</h1>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1.1fr_1fr]">
      <div class="rounded-2xl bg-white/80 p-6 shadow">
        <h2 class="text-lg font-semibold">Biblioteca</h2>
        <ul class="mt-4 space-y-3">
          <li v-for="exercise in teacher.exercises" :key="exercise.id" class="flex items-center justify-between rounded-xl border border-black/5 px-4 py-3">
            <div>
              <p class="font-medium">{{ exercise.name }}</p>
              <p class="text-xs text-black/50">{{ exercise.category }}</p>
            </div>
            <button class="text-sm text-ember" @click="remove(exercise.id)">Remover</button>
          </li>
        </ul>
      </div>

      <div class="rounded-2xl bg-white/80 p-6 shadow">
        <h2 class="text-lg font-semibold">Novo exercicio</h2>
        <form class="mt-4 space-y-3" @submit.prevent="create">
          <input v-model="form.name" class="w-full rounded-xl border border-black/10 px-4 py-3" placeholder="Nome" />
          <input v-model="form.category" class="w-full rounded-xl border border-black/10 px-4 py-3" placeholder="Categoria" />
          <input v-model="form.video_url" class="w-full rounded-xl border border-black/10 px-4 py-3" placeholder="Video URL" />
          <button class="w-full rounded-xl bg-ember px-4 py-3 text-white">Salvar</button>
        </form>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { onMounted, reactive } from 'vue'
import { useTeacherStore } from '../../stores/teacher'

const teacher = useTeacherStore()

const form = reactive({
  name: '',
  category: '',
  video_url: ''
})

const create = async () => {
  await teacher.createExercise({
    name: form.name,
    category: form.category || null,
    video_url: form.video_url
  })
  form.name = ''
  form.category = ''
  form.video_url = ''
}

const remove = async (id: number) => {
  await teacher.deleteExercise(id)
}

onMounted(() => {
  teacher.loadExercises()
})
</script>
