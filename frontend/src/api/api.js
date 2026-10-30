// Все запросы к PHP-API собраны здесь. Адреса начинаются с /api, дальше их
// перенаправляет прокси Vite (см. vite.config.js).

async function request(path, { method = 'GET', body } = {}) {
  const res = await fetch(`/api${path}`, {
    method,
    credentials: 'include', // отправляем cookie сессии
    headers: body ? { 'Content-Type': 'application/json' } : undefined,
    body: body ? JSON.stringify(body) : undefined,
  })

  let data = null
  try {
    data = await res.json()
  } catch {
    // ответ не JSON (например, страница с ошибкой PHP)
  }

  if (!res.ok) {
    const error = new Error((data && data.error) || `Ошибка запроса (${res.status})`)
    error.status = res.status
    throw error
  }
  return data
}

// --- общие ---
export const getEvents = () => request('/events')
export const getMe = () => request('/me')
export const login = (loginValue, password) =>
  request('/login', { method: 'POST', body: { login: loginValue, password } })
export const logout = () => request('/logout', { method: 'POST' })
export const register = (fields) => request('/register', { method: 'POST', body: fields })
export const joinEvent = (id) => request(`/events/${id}/join`, { method: 'POST' })

// --- обычный пользователь ---
export const getMyEvents = () => request('/user/events')
export const createEvent = (fields) => request('/user/events', { method: 'POST', body: fields })
export const deleteMyEvent = (id) => request(`/user/events/${id}`, { method: 'DELETE' })
export const getMyParticipations = () => request('/user/participations')
export const deleteParticipation = (id) =>
  request(`/user/participations/${id}`, { method: 'DELETE' })
export const changeAvatar = (avatar) =>
  request('/user/avatar', { method: 'PUT', body: { avatar } })

// --- администратор ---
export const getAdminEvents = () => request('/admin/events')
export const deleteAdminEvent = (id) => request(`/admin/events/${id}`, { method: 'DELETE' })
export const getAdminUsers = () => request('/admin/users')
export const deleteAdminUser = (id) => request(`/admin/users/${id}`, { method: 'DELETE' })
