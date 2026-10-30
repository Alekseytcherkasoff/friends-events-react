import { useCallback, useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import EventRow from '../components/EventRow'
import { deleteParticipation, getMyParticipations } from '../api/api'

export default function MyEventsPage() {
  const [items, setItems] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  const load = useCallback(async () => {
    try {
      const data = await getMyParticipations()
      setItems(data.participations)
      setError('')
    } catch (e) {
      setError(e.message)
    } finally {
      setLoading(false)
    }
  }, [])

  useEffect(() => {
    load()
  }, [load])

  // id здесь — номер записи участия, а не события
  const handleDelete = async (id) => {
    if (!window.confirm('Отменить запись на это мероприятие?')) return

    try {
      await deleteParticipation(id)
      await load()
    } catch (e) {
      setError(e.message)
    }
  }

  return (
    <div className="container">
      <div className="main-body">
        <div className="text-center mt-4 pb-4">
          <h2>Я в теме!</h2>
        </div>

        <div className="row gutters-sm">
          <div className="col-md-12">
            <div className="card mb-3">
              <div className="card-body">
                {loading && <p className="text-center">Загрузка…</p>}
                {error && <div className="alert alert-danger">{error}</div>}
                {!loading && !error && items.length === 0 && (
                  <p className="text-center text-body-secondary mb-0">
                    Вы пока ни на что не записались. <Link to="/">К списку мероприятий</Link>
                  </p>
                )}
                {items.map((item) => (
                  <EventRow key={item.id} event={item} onDelete={handleDelete} />
                ))}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}
