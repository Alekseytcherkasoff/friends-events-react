import { useCallback, useEffect, useState } from 'react'
import { deleteAdminEvent, getAdminEvents } from '../api/api'

export default function AdminEventsPage() {
  const [events, setEvents] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  const loadEvents = useCallback(async () => {
    try {
      const data = await getAdminEvents()
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
    if (!window.confirm('Вы уверены? Участники этого мероприятия тоже будут удалены.')) return

    try {
      await deleteAdminEvent(id)
      await loadEvents()
    } catch (e) {
      setError(e.message)
    }
  }

  return (
    <div className="container">
      <div className="text-center mt-4 pb-4">
        <h2>Мероприятия</h2>
      </div>

      <div className="col-md-12">
        <div className="card mb-3">
          <div className="card-body">
            {loading && <p className="text-center">Загрузка…</p>}
            {error && <div className="alert alert-danger">{error}</div>}
            {!loading && !error && events.length === 0 && (
              <p className="text-center text-body-secondary mb-0">Мероприятий пока нет.</p>
            )}

            {events.map((event) => (
              <div key={event.id}>
                <div className="row">
                  <div className="col-sm-2">
                    <h6 className="mb-0">
                      <i className="fa fa-calendar" aria-hidden="true"></i> {event.date}
                    </h6>
                    <i className="fa fa-clock-o"></i> {event.time}
                  </div>
                  <div className="col-sm-2">
                    <p>
                      <b>{event.title}</b>
                    </p>
                  </div>
                  <div className="col-sm-5">
                    <p>{event.description}</p>
                  </div>
                  <div className="col-sm-2">
                    <p>
                      <b>{event.surname}</b>
                      <br />
                      <small>
                        {event.name} {event.patronymic}
                      </small>
                    </p>
                  </div>
                  <div className="col-sm-1">
                    <button
                      type="button"
                      className="btn btn-link p-0"
                      onClick={() => handleDelete(event.id)}
                      title="Удалить"
                      aria-label="Удалить"
                    >
                      <i className="fa fa-trash fa-2x text-info" aria-hidden="true"></i>
                    </button>
                  </div>
                </div>
                <hr />
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  )
}
