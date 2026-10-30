import { Link } from 'react-router-dom'

export default function NotFoundPage() {
  return (
    <div className="container text-center">
      <h2 className="mt-4">Страница не найдена</h2>
      <p>
        <Link to="/">На главную</Link>
      </p>
    </div>
  )
}
