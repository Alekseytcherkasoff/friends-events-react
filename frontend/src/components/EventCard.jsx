// Карточка одного мероприятия. Разметка и классы взяты из старой страницы views/main/index.php
export default function EventCard({ event, onSignUp }) {
  const { manager } = event

  const handleClick = () => {
    if (window.confirm('Вы уверены?')) {
      onSignUp(event.id)
    }
  }

  return (
    <div className="col-lg-4 col-md-6">
      <div className="single-schedules-inner">
        <div className="date">
          <i className="fa fa-clock-o fa-2x text-primary"></i> {event.time}
          <br />
          <i className="fa fa-calendar fa-2x text-primary" aria-hidden="true"></i> {event.date}
        </div>

        <h5>{event.title}</h5>
        <p>{event.description}</p>

        <div className="media row">
          <div className="col-lg-5 col-md-5">
            <div className="media-left">
              <img src={`/assets/img/${manager.avatar}.png`} alt="avatar" />
            </div>
            <div className="media-body align-self-center">
              <h6>{manager.surname}</h6>
              <p>
                {manager.name} {manager.patronymic}
              </p>
            </div>
          </div>

          <div className="col-lg-7 col-md-7">
            {event.joined ? (
              <button type="button" className="mt-1 me-2 btn btn-success" disabled>
                <i className="fa fa-check" aria-hidden="true"></i> Вы записаны
              </button>
            ) : (
              <button type="button" className="mt-1 me-2 btn btn-primary" onClick={handleClick}>
                <i className="fa fa-plus-circle" aria-hidden="true"></i> Я в теме!
              </button>
            )}
            <span className="mt-1 btn btn-light" title="Участников">
              {event.participant}
            </span>
          </div>
        </div>
      </div>
    </div>
  )
}
