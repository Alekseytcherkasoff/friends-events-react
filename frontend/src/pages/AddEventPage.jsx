import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { createEvent } from '../api/api'
import { TIME_SLOTS, isoToRu, todayIso } from '../utils/date'

const emptyForm = () => ({ time: TIME_SLOTS[0], date: todayIso(), title: '', description: '' })

export default function AddEventPage() {
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
      await createEvent({
        time: form.time,
        date: isoToRu(form.date), // в базе дата хранится как дд.мм.гггг
        title: form.title,
        description: form.description,
      })
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
          <h2>Создать мероприятие</h2>
        </div>

        <form onSubmit={handleSubmit}>
          {error && <div className="alert alert-danger">{error}</div>}

          <div className="row gutters-sm">
            <div className="col-md-4 mb-4">
              <div className="card-body">
                <label className="form-label" htmlFor="eventTime">
                  Время
                </label>
                <select
                  id="eventTime"
                  className="form-select mb-3"
                  name="time"
                  value={form.time}
                  onChange={handleChange}
                >
                  {TIME_SLOTS.map((slot) => (
                    <option key={slot} value={slot}>
                      {slot}
                    </option>
                  ))}
                </select>

                <label className="form-label" htmlFor="eventDate">
                  Дата
                </label>
                <input
                  id="eventDate"
                  type="date"
                  className="form-control"
                  name="date"
                  min={todayIso()}
                  value={form.date}
                  onChange={handleChange}
                  required
                />
              </div>
            </div>

            <div className="col-md-8">
              <div className="card mb-3">
                <div className="card-body">
                  <div className="mb-3">
                    <label className="form-label" htmlFor="eventTitle">
                      Название мероприятия
                    </label>
                    <input
                      id="eventTitle"
                      type="text"
                      className="form-control"
                      name="title"
                      placeholder="К примеру, поход в кино"
                      maxLength={255}
                      value={form.title}
                      onChange={handleChange}
                      required
                    />
                  </div>
                  <div className="mb-3">
                    <label className="form-label" htmlFor="eventDescription">
                      Краткое описание мероприятия
                    </label>
                    <textarea
                      id="eventDescription"
                      className="form-control"
                      rows="3"
                      name="description"
                      maxLength={255}
                      value={form.description}
                      onChange={handleChange}
                      required
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <hr />
          <p className="text-center">
            <button type="submit" className="btn btn-info me-2" disabled={submitting}>
              Добавить
            </button>
            <button type="button" className="btn btn-secondary" onClick={() => setForm(emptyForm())}>
              Очистить
            </button>
          </p>
        </form>
      </div>
    </div>
  )
}
