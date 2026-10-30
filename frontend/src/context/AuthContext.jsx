import { createContext, useCallback, useContext, useEffect, useState } from 'react'
import * as api from '../api/api'

const AuthContext = createContext(null)

// Хранит, кто сейчас вошёл. При загрузке приложения спрашивает у PHP /api/me,
// поэтому после обновления страницы (F5) пользователь остаётся вошедшим.
export function AuthProvider({ children }) {
  const [user, setUser] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    api
      .getMe()
      .then((data) => setUser(data.user))
      .catch(() => setUser(null))
      .finally(() => setLoading(false))
  }, [])

  const login = useCallback(async (loginValue, password) => {
    const data = await api.login(loginValue, password)
    setUser(data.user)
    return data.user
  }, [])

  const logout = useCallback(async () => {
    await api.logout()
    setUser(null)
  }, [])

  const value = { user, loading, login, logout, setUser }
  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}

// eslint-disable-next-line react-refresh/only-export-components
export function useAuth() {
  return useContext(AuthContext)
}
