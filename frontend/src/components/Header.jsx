import { Link, NavLink, useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'

const linkClass = ({ isActive }) => 'nav-link' + (isActive ? ' active' : '')

export default function Header() {
  const { user, logout } = useAuth()
  const navigate = useNavigate()

  const handleLogout = async () => {
    await logout()
    navigate('/')
  }

  return (
    <header>
      <nav className="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
        <div className="container-fluid">
          <Link className="navbar-brand" to="/">
            Единомышленники
          </Link>

          <button
            className="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarCollapse"
            aria-controls="navbarCollapse"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span className="navbar-toggler-icon"></span>
          </button>

          <div className="collapse navbar-collapse" id="navbarCollapse">
            <ul className="navbar-nav me-auto mb-2 mb-md-0">
              <li className="nav-item">
                <NavLink className={linkClass} to="/" end>
                  Мероприятия
                </NavLink>
              </li>

              {user && user.role === 'user' && (
                <>
                  <li className="nav-item">
                    <NavLink className={linkClass} to="/user" end>
                      Профиль
                    </NavLink>
                  </li>
                  <li className="nav-item">
                    <NavLink className={linkClass} to="/user/add-event">
                      Создать мероприятие
                    </NavLink>
                  </li>
                  <li className="nav-item">
                    <NavLink className={linkClass} to="/user/my-event">
                      Я в теме!
                    </NavLink>
                  </li>
                </>
              )}

              {user && user.role === 'admin' && (
                <>
                  <li className="nav-item">
                    <NavLink className={linkClass} to="/admin" end>
                      Events
                    </NavLink>
                  </li>
                  <li className="nav-item">
                    <NavLink className={linkClass} to="/admin/users">
                      Пользователи
                    </NavLink>
                  </li>
                </>
              )}
            </ul>

            <div className="d-flex">
              {user ? (
                <button type="button" className="btn btn-outline-success" onClick={handleLogout}>
                  Выход
                </button>
              ) : (
                <Link className="btn btn-outline-success" to="/login">
                  Вход
                </Link>
              )}
            </div>
          </div>
        </div>
      </nav>
    </header>
  )
}
