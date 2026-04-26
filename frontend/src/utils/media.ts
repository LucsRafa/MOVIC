const configuredBase = (import.meta.env.VITE_API_URL || '/api').trim()
const normalizedBase = configuredBase.replace(/\/+$/, '')

const resolveApiOrigin = () => {
  if (/^https?:\/\//i.test(normalizedBase)) {
    try {
      return new URL(normalizedBase).origin
    } catch {
      return window.location.origin
    }
  }

  return window.location.origin
}

export const resolveMediaUrl = (value?: string | null) => {
  if (!value) {
    return null
  }

  if (/^(https?:)?\/\//i.test(value) || value.startsWith('data:') || value.startsWith('blob:')) {
    return value
  }

  if (value.startsWith('/')) {
    return `${resolveApiOrigin()}${value}`
  }

  return `${resolveApiOrigin()}/${value.replace(/^\/+/, '')}`
}
