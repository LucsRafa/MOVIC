<template>
  <div class="min-h-screen bg-gradient-to-b from-[#0b1020] via-[#111a2e] to-[#0b1325] px-4 py-6 text-white">
    <div class="mx-auto max-w-4xl space-y-6">
      <HeaderBar :name="userName" @settings="showSettings = true" @logout="logout" />

      <TrialBanner v-if="showTrial" :date="dashboard.subscription?.expires_at" />

      <SummaryCards v-if="dashboard" :summary="dashboard.summary" />

      <TodayWorkoutCard
        v-if="dashboard?.today?.workout_day"
        :day-name="dayLabel"
        :title="dashboard.today.workout_day.title"
        :subtitle="dashboard.today.workout_day.subtitle || undefined"
        :items="dashboard.today.workout_day.items"
        :progress="dashboard.today.workout_day.progress_percent"
        :show-finish="true"
        @toggle="toggleItem"
        @video="openVideo"
        @finish="finish"
      />

      <WeekProgress v-if="dashboard?.week?.days" :days="dashboard.week.days" @select="goToDay" />

      <SubscriptionCard
        v-if="dashboard"
        :status="dashboard.subscription?.status"
        :expires-at="dashboard.subscription?.expires_at"
        @activate="showPayment = true"
        @receipts="openReceipts"
      />

      <div class="rounded-2xl bg-white/5 p-4">
        <h3 class="text-lg font-semibold">Estatisticas</h3>
        <div class="mt-3 space-y-3 text-sm text-white/70">
          <div>
            <div class="flex justify-between">
              <span>Frequencia Mensal</span>
              <span>{{ dashboard?.week?.monthly_frequency_percent ?? 0 }}%</span>
            </div>
            <div class="mt-2 h-2 w-full rounded-full bg-white/10">
              <div
                class="h-2 rounded-full bg-emerald-400"
                :style="{ width: `${dashboard?.week?.monthly_frequency_percent ?? 0}%` }"
              ></div>
            </div>
          </div>
          <div>
            <div class="flex justify-between">
              <span>Meta Semanal</span>
              <span>{{ dashboard?.week?.weekly_goal?.done ?? 0 }}/{{ dashboard?.week?.weekly_goal?.total ?? 0 }} treinos</span>
            </div>
            <div class="mt-2 h-2 w-full rounded-full bg-white/10">
              <div class="h-2 rounded-full bg-sky-400" :style="{ width: weeklyGoalPercent + '%' }"></div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center text-white/60">Carregando...</div>
    </div>

    <VideoModal
      v-if="videoItem"
      :video-url="videoItem.exercise.video_url"
      :description="videoItem.exercise.description"
      @close="videoItem = null"
    />

    <ReceiptsModal
      v-if="showReceipts"
      :payments="payments"
      @close="showReceipts = false"
      @detail="openReceiptDetail"
    />

    <ReceiptDetailModal
      v-if="receiptDetail"
      :payment="receiptDetail"
      @close="receiptDetail = null"
      @pdf="downloadPdf"
      @email="emailReceipt"
    />

    <SettingsModal
      v-if="showSettings"
      :user="dashboard?.user || {}"
      @close="showSettings = false"
      @uploadAvatar="uploadAvatar"
      @updateProfile="updateProfile"
      @updatePassword="updatePassword"
    />

    <PaymentModal v-if="showPayment" @close="showPayment = false" />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import HeaderBar from '../../components/HeaderBar.vue'
import TrialBanner from '../../components/TrialBanner.vue'
import SummaryCards from '../../components/SummaryCards.vue'
import TodayWorkoutCard from '../../components/TodayWorkoutCard.vue'
import WeekProgress from '../../components/WeekProgress.vue'
import SubscriptionCard from '../../components/SubscriptionCard.vue'
import VideoModal from '../../components/VideoModal.vue'
import ReceiptsModal from '../../components/ReceiptsModal.vue'
import ReceiptDetailModal from '../../components/ReceiptDetailModal.vue'
import SettingsModal from '../../components/SettingsModal.vue'
import PaymentModal from '../../components/PaymentModal.vue'
import api from '../../api/http'
import { useAuthStore } from '../../stores/auth'
import { useStudentStore } from '../../stores/student'

const student = useStudentStore()
const auth = useAuthStore()
const router = useRouter()

const videoItem = ref<any | null>(null)
const showReceipts = ref(false)
const receiptDetail = ref<any | null>(null)
const showSettings = ref(false)
const showPayment = ref(false)

const dashboard = computed(() => student.dashboard)
const loading = computed(() => student.loading)
const payments = computed(() => student.payments)
const userName = computed(() => dashboard.value?.user?.name || 'Aluno')

const showTrial = computed(() => ['trial', 'experimental'].includes(dashboard.value?.subscription?.status))

const weeklyGoalPercent = computed(() => {
  const done = dashboard.value?.week?.weekly_goal?.done || 0
  const total = dashboard.value?.week?.weekly_goal?.total || 0
  return total ? Math.round((done / total) * 100) : 0
})

const dayLabel = computed(() => {
  const labels = ['Domingo', 'Segunda-feira', 'Terca-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sabado']
  return labels[dashboard.value?.today?.weekday ?? 0]
})

const toggleItem = async (item: any) => {
  if (!student.session) {
    const session = await student.startSession(dashboard.value?.today?.workout_day?.id)
    student.session = session
  }
  item.is_checked = !item.is_checked
  if (dashboard.value?.today?.workout_day?.items) {
    const total = dashboard.value.today.workout_day.items.length
    const done = dashboard.value.today.workout_day.items.filter((i: any) => i.is_checked).length
    dashboard.value.today.workout_day.progress_percent = total ? Math.round((done / total) * 100) : 0
  }
  try {
    await student.checkItem(student.session.id, item.workout_item_id, item.is_checked)
  } catch (e) {
    item.is_checked = !item.is_checked
  }
}

const finish = async () => {
  if (student.session) {
    await student.finishSession(student.session.id)
  }
}

const openVideo = (item: any) => {
  videoItem.value = item
}

const openReceipts = async () => {
  await student.loadPayments()
  showReceipts.value = true
}

const openReceiptDetail = async (payment: any) => {
  const detail = await student.fetchPayment(payment.id)
  receiptDetail.value = detail
}

const downloadPdf = async () => {
  if (!receiptDetail.value) return
  const blob = await student.downloadPdf(receiptDetail.value.id)
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `comprovante-${receiptDetail.value.id}.pdf`
  a.click()
  URL.revokeObjectURL(url)
}

const emailReceipt = async () => {
  if (!receiptDetail.value) return
  await student.emailReceipt(receiptDetail.value.id)
}

const updateProfile = async (payload: any) => {
  await api.patch('/user/profile', payload)
  await student.fetchDashboard()
}

const updatePassword = async (payload: any) => {
  await api.patch('/user/password', payload)
}

const uploadAvatar = async (file: File) => {
  const form = new FormData()
  form.append('avatar', file)
  await api.post('/user/avatar', form)
  await student.fetchDashboard()
}

const logout = async () => {
  await auth.logout()
  router.push('/login')
}

const goToDay = (weekday: number) => {
  router.push(`/student/workout/${weekday}`)
}

onMounted(async () => {
  await student.fetchDashboard()
})
</script>
