export const extractApiErrorMessage = (error: any, fallback = 'Não foi possível concluir a solicitação.') => {
  if (error?.response?.data) {
    const data = error.response.data
    const details = data.details || data.errors

    if (details && typeof details === 'object') {
      const messages = Object.values(details)
        .flat()
        .filter(Boolean)

      if (messages.length) {
        return String(messages[0])
      }
    }

    if (typeof data.message === 'string' && data.message.trim()) {
      return data.message
    }
  }

  if (error?.response?.status === 422) {
    return 'Confira os dados informados e tente novamente.'
  }

  if (error?.request) {
    const target = [error?.config?.baseURL, error?.config?.url]
      .filter(Boolean)
      .join('')

    return target
      ? `Erro de conexão com a API ao chamar ${target}.`
      : 'Erro de conexão com a API.'
  }

  if (typeof error?.message === 'string' && error.message.trim()) {
    return error.message
  }

  return fallback
}
