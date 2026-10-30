import { useCallback, useEffect, useState } from 'react'
import { deleteAdminUser, getAdminUsers } from '../api/api'

export default function AdminUsersPage() {
  const [users, setUsers] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  const loadUsers = useCallback(async () => {
    try {
      const data = await getAdminUsers()
      setUsers(data.users)
      setError('')
    } catch (e) {
      setError(e.message)
    } finally {
      setLoading(false)
    }
  }, [])

  useEffect(() => {
    loadUsers()
  }, [loadUsers])

  const handleDelete = async (user) => {
    const text = `Удалить пользователя ${user.login}? Его мероприятия и записи на мероприятия тоже будут удалены.`
    if (!window.confirm(text)) return

    try {
      await deleteAdminUser(user.id)
      await loadUsers()
    } catch (e) {
      setError(e.message)
    }
  }

  return (
    <div className="container">
      <h2 className="mt-4 text-center">Пользователи</h2>

      {loading && <p className="text-center">Загрузка…</p>}
      {error && <div className="alert alert-danger">{error}</div>}
      {!loading && !error && users.length === 0 && (
        <p className="text-center text-body-secondary">Пользователей пока нет.</p>
      )}

      {users.length > 0 && (
        <div className="table-responsive">
          <table className="table align-middle table-hover mb-0">
            <thead>
              <tr>
                <th scope="col">№</th>
                <th scope="col">Пользователь</th>
                <th scope="col">ФИО</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              {users.map((user, index) => (
                <tr key={user.id}>
                  <td>{index + 1}</td>
                  <td>
                    <p className="fw-medium mb-0">{user.login}</p>
                  </td>
                  <td>
                    <p className="mb-0">
                      {user.surname} {user.name} {user.patronymic}
                    </p>
                  </td>
                  <td className="text-end">
                    <button
                      type="button"
                      className="btn btn-link p-0"
                      onClick={() => handleDelete(user)}
                      title="Удалить"
                      aria-label="Удалить"
                    >
                      <i className="fa fa-trash fa-lg text-info" aria-hidden="true"></i>
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  )
}
