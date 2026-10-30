// Строка списка событий (профиль и «Я в теме!»). Кнопка корзины вызывает onDelete(id).
export default function EventRow({ event, onDelete }) {
  return (
    <>
      <div className="row">
        <div className="col-sm-3">
          <h6 className="mb-0">
            <i className="fa fa-calendar" aria-hidden="true"></i> {event.date}
          </h6>
          <i className="fa fa-clock-o"></i> {event.time}
        </div>
        <div className="col-sm-3">
          <p>
            <b>{event.title}</b>
          </p>
        </div>
        <div className="col-sm-5">
          <p>{event.description}</p>
        </div>
        <div className="col-sm-1">
          <button
            type="button"
            className="btn btn-link p-0"
            onClick={() => onDelete(event.id)}
            title="Удалить"
            aria-label="Удалить"
          >
            <i className="fa fa-trash fa-2x text-info" aria-hidden="true"></i>
          </button>
        </div>
      </div>
      <hr />
    </>
  )
}
