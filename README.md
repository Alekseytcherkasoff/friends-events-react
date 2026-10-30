# Edinomyshlenniki ("Like-minded people")

A web app for finding company for leisure activities. Users publish events (a movie, a walk, board games…) and other people join them.

This started as my diploma project written in PHP with server-rendered pages. I rebuilt the interface as a **React single-page app** and turned the PHP part into a **JSON API**. The old PHP version is kept in the Git history (tag `php-legacy`).

<!-- Add screenshots to docs/screenshots/ and uncomment:
![Events list](docs/screenshots/home.png)
![Profile](docs/screenshots/profile.png)
![Admin panel](docs/screenshots/admin.png)
-->

## Features

- Browse upcoming events with the organizer and the number of participants
- Sign up and log in (passwords are stored as bcrypt hashes)
- Two roles: **user** and **admin**
- Users: create and delete their own events, join and leave events, choose an avatar
- Admin: view and delete any event, view and delete users

## Tech stack

| Part | Technologies |
|---|---|
| Frontend | React, Vite, React Router, Bootstrap 5, Font Awesome |
| Backend | PHP (no framework, MVC-style), PDO |
| Database | MySQL 8 |

## Project structure

```
├── frontend/          React app (Vite)
│   └── src/
│       ├── api/           all requests to the API
│       ├── context/       AuthContext (who is logged in)
│       ├── components/    Header, Footer, EventCard, ProtectedRoute …
│       └── pages/         pages of the app
├── components/        Db.php, Api.php, ApiRouter.php
├── controllers/       Api*Controller.php (JSON endpoints)
├── models/            database access (PDO, prepared statements)
├── config/            db_params.example.php
├── database/          club.sql (schema and demo data)
└── index.php          entry point of the API
```

## Getting started (development)

Requirements: PHP 7.4+, MySQL 8, Node.js 20+ and a local web server with `mod_rewrite` (I use OpenServer; XAMPP works too).

1. **Database.** Create a database named `club` and import `database/club.sql`.
2. **Config.** Copy `config/db_params.example.php` to `config/db_params.php` and enter your database credentials.
3. **PHP.** Serve the project folder with your web server so the API is available at `http://friends/api/events` (the domain `friends` is configured in `frontend/vite.config.js`; change it there if yours is different).
4. **Frontend.**
   ```bash
   cd frontend
   npm install
   npm run dev
   ```
   Open http://localhost:5173. Requests to `/api/...` are proxied to PHP.

## Production build

```bash
cd frontend
npm run build
```

The build goes to the `web/` folder. The `.htaccess` file sends `/api/...` to PHP and everything else to the React app, so one web server serves both.

## API

All request bodies are JSON (`Content-Type: application/json`). Errors have the form `{ "error": "message" }`.

| Method and path | Access | Description |
|---|---|---|
| `GET /api/events` | everyone | list of events |
| `GET /api/me` | everyone | current user (`null` if not logged in) |
| `POST /api/login` | everyone | log in |
| `POST /api/logout` | everyone | log out |
| `POST /api/register` | everyone | sign up |
| `POST /api/events/{id}/join` | logged in | join an event |
| `GET`, `POST /api/user/events` | user | my events, create an event |
| `DELETE /api/user/events/{id}` | user | delete my event |
| `GET /api/user/participations` | user | events I joined |
| `DELETE /api/user/participations/{id}` | user | leave an event |
| `PUT /api/user/avatar` | user | change avatar |
| `GET /api/admin/events` | admin | all events |
| `DELETE /api/admin/events/{id}` | admin | delete an event |
| `GET /api/admin/users` | admin | all users |
| `DELETE /api/admin/users/{id}` | admin | delete a user |

## Security notes

- Passwords are hashed with `password_hash` (bcrypt); legacy plain-text passwords are upgraded on the first login
- All SQL queries use prepared statements
- Users can only delete their own events and participations; admin routes check the role on the server
- JSON-only requests and `SameSite=Lax` cookies protect against CSRF

## Demo accounts

Only for local development (see `database/club.sql`):

| Login | Password | Role |
|---|---|---|
| `admin` | `admin` | admin |
| `user` | `user` | user |

## Roadmap

- [ ] Edit events
- [ ] Store dates in a `DATE` column and sort events chronologically
- [ ] Event place and categories
- [ ] Password reset by email
- [ ] Tests (PHPUnit for the API, Vitest for React)

## License

[MIT](LICENSE)
