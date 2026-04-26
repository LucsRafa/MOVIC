<template>
  <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/70 px-4 py-8">
    <div class="w-full max-w-lg rounded-2xl bg-[#0f172a] p-4 text-white shadow-2xl sm:p-5">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">Criar Novo Treino</h3>
        <button class="text-white/70 hover:text-white" @click="$emit('close')">x</button>
      </div>
      <p class="text-sm text-white/60">Monte um treino personalizado</p>

      <div class="mt-4 space-y-3">
        <select v-model="form.student_id" class="app-select">
          <option value="">Selecione o aluno</option>
          <option v-for="student in students" :key="student.id" :value="String(student.id)">
            {{ student.name }}
          </option>
        </select>
        <input v-model="form.title" class="app-input" placeholder="Nome do treino" />
        <select v-model="form.weekday" class="app-select">
          <option value="">Dia da semana</option>
          <option v-for="(label, index) in weekdays" :key="label" :value="String(index)">
            {{ label }}
          </option>
        </select>
        <textarea v-model="form.notes" class="app-textarea" placeholder="Descrição"></textarea>
        <button
          class="w-full rounded-xl bg-emerald-500 px-4 py-3 text-sm font-semibold hover:bg-emerald-400 disabled:hover:bg-emerald-500"
          :disabled="loading"
          @click="submit"
        >
          {{ loading ? 'Criando...' : 'Criar treino' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue'

defineProps<{ students: any[]; loading?: boolean }>()
const emit = defineEmits(['close', 'create'])

const weekdays = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado']

const form = reactive({
  student_id: '',
  weekday: '',
  title: '',
  notes: ''
})

const submit = () => {
  if (!form.student_id || form.weekday === '' || !form.title) return
  emit('create', {
    student_id: Number(form.student_id),
    weekday: Number(form.weekday),
    title: form.title,
    notes: form.notes
  })
}
</script>
