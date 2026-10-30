import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import { changeAvatar } from '../api/api'

const AVATARS = Array.from({ length: 8 }, (_, i) => `avatar${i + 1}`)

export default function ChangeAvatarPage() {
  const { user, setUser } = useAuth()
  const navigate = useNavigate()

  const [selected, setSelected] = useState(AVATARS.includes(user.avatar) ? user.avatar : AVATARS[0])
  const [error, setError] = useState('')
  const [submitting, setSubmitting] = useState(false)

  const handleSubmit = async (e) => {
    e.preventDefault()
    setError('')
    setSubmitting(true)

    try {
      await changeAvatar(selected)
      setUser((current) => ({ ...current, avatar: selected })) // обновляем шапку и профиль без перезагрузки
      navigate('/user')
    } catch (err) {
      setError(err.message)
      setSubmitting(false)
    }
  }

  return (
    <div className="container">
      <div className="main-body">
        <div className="text-center mt-4 pb-4">
          <h2>Сменить аватар</h2>
        </div>

        <div className="row gutters-sm">
          <div className="col-md-12 mb-3">
            <div className="card">
              <div className="card-body">
                <form onSubmit={handleSubmit}>
                  {error && <div className="alert alert-danger">{error}</div>}

                  {AVATARS.map((avatar) => (
                    <div className="form-check form-check-inline" key={avatar}>
                      <input
                        className="form-check-input"
                        type="radio"
                        name="avatar"
                        id={`radio-${avatar}`}
                        value={avatar}
                        checked={selected === avatar}
                        onChange={() => setSelected(avatar)}
                      />
                      <label className="form-check-label" htmlFor={`radio-${avatar}`}>
                        <img
                          src={`/assets/img/${avatar}.png`}
                          alt={avatar}
                          className="rounded-circle"
                          width="100"
                        />
                      </label>
                    </div>
                  ))}

                  <hr />
                  <p className="text-center mb-0">
                    <button type="submit" className="btn btn-info me-2" disabled={submitting}>
                      Сохранить
                    </button>
                    <Link to="/user" className="btn btn-secondary">
                      Отмена
                    </Link>
                  </p>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}
