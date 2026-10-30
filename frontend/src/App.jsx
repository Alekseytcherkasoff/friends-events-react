import { Routes, Route } from 'react-router-dom'

import Header from './components/Header'
import Footer from './components/Footer'
import ProtectedRoute from './components/ProtectedRoute'

import HomePage from './pages/HomePage'
import LoginPage from './pages/LoginPage'
import RegisterPage from './pages/RegisterPage'
import ProfilePage from './pages/ProfilePage'
import ChangeAvatarPage from './pages/ChangeAvatarPage'
import AddEventPage from './pages/AddEventPage'
import MyEventsPage from './pages/MyEventsPage'
import AdminEventsPage from './pages/AdminEventsPage'
import AdminUsersPage from './pages/AdminUsersPage'
import NotFoundPage from './pages/NotFoundPage'

export default function App() {
  return (
    <>
      <Header />
      <main className="flex-shrink-0">
        <Routes>
          <Route path="/" element={<HomePage />} />
          <Route path="/login" element={<LoginPage />} />
          <Route path="/registration" element={<RegisterPage />} />

          {/* Страницы пользователя */}
          <Route path="/user" element={<ProtectedRoute role="user"><ProfilePage /></ProtectedRoute>} />
          <Route path="/user/change-avatar" element={<ProtectedRoute role="user"><ChangeAvatarPage /></ProtectedRoute>} />
          <Route path="/user/add-event" element={<ProtectedRoute role="user"><AddEventPage /></ProtectedRoute>} />
          <Route path="/user/my-event" element={<ProtectedRoute role="user"><MyEventsPage /></ProtectedRoute>} />

          {/* Страницы администратора */}
          <Route path="/admin" element={<ProtectedRoute role="admin"><AdminEventsPage /></ProtectedRoute>} />
          <Route path="/admin/users" element={<ProtectedRoute role="admin"><AdminUsersPage /></ProtectedRoute>} />

          <Route path="*" element={<NotFoundPage />} />
        </Routes>
      </main>
      <Footer />
    </>
  )
}
