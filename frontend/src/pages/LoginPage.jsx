import { useState } from 'react'
import { Link, useLocation, useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'

export default function LoginPage() {
  const { login } = useAuth()
  const navigate = useNavigate()
  const location = useLocation()

  const [loginValue, setLoginValue] = useState('')
  const [password, setPassword] = useState('')
  const [error, setError] = useState('')
  const [submitting, setSubmitting] = useState(false)

  const justRegistered = Boolean(location.state && location.state.registered)

  const handleSubmit = async (e) => {
    e.preventDefault()
    setError('')
    setSubmitting(true)

    try {
      const user = await login(loginValue, password)
      navigate(user.role === 'admin' ? '/admin' : '/user')
    } catch (err) {
      setError(err.message)
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <div className="container">
      <div className="form-signin w-100 m-auto">
        <form onSubmit={handleSubmit}>
          <h1 className="h3 mb-3 fw-normal">Пожалуйста, войдите</h1>

          {justRegistered && (
            <div className="alert alert-success">Вы зарегистрированы. Теперь войдите.</div>
          )}
          {error && <div className="alert alert-danger">{error}</div>}

          <div className="form-floating">
            <input
              type="text"
              className="form-control first"
              id="floatingLogin"
              placeholder="Логин"
              value={loginValue}
              onChange={(e) => setLoginValue(e.target.value)}
              autoComplete="username"
              required
            />
            <label htmlFor="floatingLogin">Логин</label>
          </div>
          <div className="form-floating">
            <input
              type="password"
              className="form-control"
              id="floatingPassword"
              placeholder="Пароль"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              autoComplete="current-password"
              required
            />
            <label htmlFor="floatingPassword">Пароль</label>
          </div>

          <button className="btn btn-primary w-100 py-2 mt-3" type="submit" disabled={submitting}>
            {submitting ? 'Входим…' : 'Войти'}
          </button>

          <p className="mt-3 text-center">
            Нет аккаунта? <Link to="/registration">Зарегистрироваться</Link>
          </p>
        </form>
      </div>
    </div>
  )
}
