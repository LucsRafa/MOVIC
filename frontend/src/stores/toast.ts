import { defineStore } from 'pinia'

type ToastType = 'success' | 'error' | 'info'

type Toast = {
  id: number
  type: ToastType
  message: string
}

let counter = 1

export const useToastStore = defineStore('toast', {
  state: () => ({
    items: [] as Toast[]
  }),
  actions: {
    push(message: string, type: ToastType = 'info') {
      const id = counter++
      this.items.push({ id, type, message })
      setTimeout(() => {
        this.remove(id)
      }, 4000)
    },
    remove(id: number) {
      this.items = this.items.filter((item) => item.id !== id)
    }
  }
})
