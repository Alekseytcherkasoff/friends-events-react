import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { register } from '../api/api'

const emptyForm = { login: '', password: '', surname: '', name: '', patronymic: '' }

export default function RegisterPage() {
  const navigate = useNavigate()
  const [form, setForm] = useState(emptyForm)
  const [error, setError] = useState('')
  const [submitting, setSubmitting] = useState(false)

  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value })
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setError('')
    setSubmitting(true)

    try {
      await register(form)
      navigate('/login', { state: { registered: true } })
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
          <h1 className="h3 mb-3 fw-normal">Регистрация</h1>

          {error && <div className="alert alert-danger">{error}</div>}

          <div className="form-floating mb-2">
            <input
              name="login"
              type="text"
              className="form-control"
              id="regLogin"
              placeholder="Логин"
              value={form.login}
              onChange={handleChange}
              autoComplete="username"
              required
            />
            <label htmlFor="regLogin">Логин</label>
          </div>
          <div className="form-floating mb-2">
            <input
              name="password"
              type="password"
              className="form-control"
              id="regPassword"
              placeholder="Пароль"
              value={form.password}
              onChange={handleChange}
              autoComplete="new-password"
              required
            />
            <label htmlFor="regPassword">Пароль (не короче 6 символов)</label>
          </div>
          <div className="form-floating mb-2">
            <input
              name="surname"
              type="text"
              className="form-control"
              id="regSurname"
              placeholder="Фамилия"
              value={form.surname}
              onChange={handleChange}
              required
            />
            <label htmlFor="regSurname">Фамилия</label>
          </div>
          <div className="form-floating mb-2">
            <input
              name="name"
              type="text"
              className="form-control"
              id="regName"
              placeholder="Имя"
              value={form.name}
              onChange={handleChange}
              required
            />
            <label htmlFor="regName">Имя</label>
          </div>
          <div className="form-floating mb-2">
            <input
              name="patronymic"
              type="text"
              className="form-control"
              id="regPatronymic"
              placeholder="Отчество"
              value={form.patronymic}
              onChange={handleChange}
              required
            />
            <label htmlFor="regPatronymic">Отчество</label>
          </div>

          <button className="btn btn-primary w-100 py-2 mt-2" type="submit" disabled={submitting}>
            {submitting ? 'Отправляем…' : 'Отправить'}
          </button>

          <p className="mt-3 text-center">
            Уже есть аккаунт? <Link to="/login">Войти</Link>
          </p>
        </form>
      </div>
    </div>
  )
}
