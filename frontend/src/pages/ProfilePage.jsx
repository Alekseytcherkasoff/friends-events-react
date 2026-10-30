import { useCallback, useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import EventRow from '../components/EventRow'
import { useAuth } from '../context/AuthContext'
import { deleteMyEvent, getMyEvents } from '../api/api'

export default function ProfilePage() {
  const { user } = useAuth()
  const [events, setEvents] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  const loadEvents = useCallback(async () => {
    try {
      const data = await getMyEvents()
      setEvents(data.events)
      setError('')
    } catch (e) {
      setError(e.message)
    } finally {
      setLoading(false)
    }
  }, [])

  useEffect(() => {
    loadEvents()
  }, [loadEvents])

  const handleDelete = async (id) => {
    if (!window.confirm('Вы уверены, что хотите удалить мероприятие?')) return

    try {
      await deleteMyEvent(id)
      await loadEvents()
    } catch (e) {
      setError(e.message)
    }
  }

  return (
    <div className="container">
      <div className="main-body">
        <div className="text-center mt-4 pb-4">
          <h2>Созданные мероприятия</h2>
        </div>

        <div className="row gutters-sm">
          <div className="col-md-4 mb-3">
            <div className="card">
              <div className="card-body">
                <div className="d-flex flex-column align-items-center text-center">
                  <img
                    src={`/assets/img/${user.avatar}.png`}
                    alt="avatar"
                    className="rounded-circle"
                    width="150"
                  />
                  <div className="mt-3">
                    <h4>{user.surname}</h4>
                    <p className="text-secondary mb-1">{user.name}</p>
                    <p className="text-muted font-size-sm">{user.patronymic}</p>
                    <div className="col-sm-12">
                      <Link className="btn btn-info" to="/user/change-avatar">
                        Сменить аватарку
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div className="col-md-8">
            <div className="card mb-3">
              <div className="card-body">
                {loading && <p className="text-center">Загрузка…</p>}
                {error && <div className="alert alert-danger">{error}</div>}
                {!loading && !error && events.length === 0 && (
                  <p className="text-center text-body-secondary mb-0">
                    Вы пока не создали ни одного мероприятия.{' '}
                    <Link to="/user/add-event">Создать</Link>
                  </p>
                )}
                {events.map((event) => (
                  <EventRow key={event.id} event={event} onDelete={handleDelete} />
                ))}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}
