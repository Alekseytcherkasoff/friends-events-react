import { useCallback, useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import EventCard from '../components/EventCard'
import { useAuth } from '../context/AuthContext'
import { getEvents, joinEvent } from '../api/api'

export default function HomePage() {
  const { user, loading: authLoading } = useAuth()
  const [events, setEvents] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [notice, setNotice] = useState(null) // { type: 'success' | 'warning' | 'danger', text }

  const loadEvents = useCallback(async () => {
    try {
      const data = await getEvents()
      setEvents(data.events)
      setError('')
    } catch (e) {
      setError(e.message)
    } finally {
      setLoading(false)
    }
  }, [])

  // Загружаем список, когда стало известно, кто вошёл (чтобы пришёл признак joined),
  // и заново после входа или выхода.
  useEffect(() => {
    if (!authLoading) {
      loadEvents()
    }
  }, [authLoading, user, loadEvents])

  const handleSignUp = async (id) => {
    if (!user) {
      setNotice({
        type: 'warning',
        text: 'Нужно войти в систему, чтобы записаться на мероприятие.',
      })
      return
    }

    try {
      await joinEvent(id)
      setNotice({ type: 'success', text: 'Поздравляю! Вы зарегистрировались.' })
      await loadEvents()
    } catch (e) {
      setNotice({ type: 'danger', text: e.message })
    }
  }

  return (
    <div className="container">
      <div className="schedules-area pd-top-110 pd-bottom-120">
        <div className="container">
          <div className="row justify-content-center">
            <div className="col-xl-12 col-lg-12">
              <div className="section-title text-center mt-4">
                <h2>Расписание мероприятий</h2>
                <p>
                  Есть желание хорошо провести время? Посетить интересные развлечения? И у Вас еще
                  нет компании? Наш проект &quot;Единомышленники&quot; поможет найти друзей и
                  принимать участие в совместных развлечениях!
                </p>
              </div>
            </div>
          </div>

          {notice && (
            <div className={`alert alert-${notice.type} alert-dismissible`} role="alert">
              {notice.text}{' '}
              {notice.type === 'warning' && <Link to="/login">Войти</Link>}
              <button
                type="button"
                className="btn-close"
                aria-label="Закрыть"
                onClick={() => setNotice(null)}
              ></button>
            </div>
          )}

          {loading && <p className="text-center">Загрузка…</p>}
          {error && <div className="alert alert-danger">{error}</div>}
          {!loading && !error && events.length === 0 && (
            <p className="text-center text-body-secondary">Пока нет ни одного мероприятия.</p>
          )}

          <div className="row">
            {events.map((event) => (
              <EventCard key={event.id} event={event} onSignUp={handleSignUp} />
            ))}
          </div>
        </div>
      </div>
    </div>
  )
}
