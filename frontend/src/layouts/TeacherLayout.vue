<template>
  <div class="min-h-screen bg-gradient-to-b from-[#0b1020] via-[#101a2e] to-[#0b1325] text-white">
    <div class="mx-auto w-full max-w-5xl px-4 pb-10 pt-6">
      <TeacherHeaderBar :name="userName" @settings="showSettings = true" @logout="logout" />
      <TeacherMenuBar :requests="requestsBadge" />
      <div class="mt-6">
        <RouterView />
      </div>
    </div>

    <SettingsModal
      v-if="showSettings"
      :user="auth.user || {}"
      @close="showSettings = false"
      @uploadAvatar="uploadAvatar"
      @updateProfile="updateProfile"
      @updatePassword="updatePassword"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { RouterView, useRouter } from 'vue-router'
import TeacherHeaderBar from '../components/TeacherHeaderBar.vue'
import TeacherMenuBar from '../components/TeacherMenuBar.vue'
import SettingsModal from '../components/SettingsModal.vue'
import api from '../api/http'
import { useAuthStore } from '../stores/auth'
import { useTeacherStore } from '../stores/teacher'

const showSettings = ref(false)
const auth = useAuthStore()
const teacher = useTeacherStore()
const router = useRouter()

const userName = computed(() => auth.user?.name || 'Professor')
const requestsBadge = computed(() => teacher.dashboard?.badges?.requests ?? 0)

const updateProfile = async (payload: any) => {
  await api.patch('/user/profile', payload)
  await auth.fetchMe()
}

const updatePassword = async (payload: any) => {
  await api.patch('/user/password', payload)
}

const uploadAvatar = async (file: File) => {
  const form = new FormData()
  form.append('avatar', file)
  await api.post('/user/avatar', form)
  await auth.fetchMe()
}

const logout = async () => {
  await auth.logout()
  router.push('/login')
}

onMounted(async () => {
  if (!auth.user) {
    await auth.fetchMe()
  }
  await teacher.fetchDashboard()
})
</script>
