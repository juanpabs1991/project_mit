# Attendly — Attendance System

A small CRUD web application for managing student attendance, built with **Laravel 12 + Livewire 4 + MySQL + Tailwind CSS + Vite**.

This project was built for the MIT001.26 *Advanced Platform and Networking Technologies* midterm exam: build a CRUD app that connects to a real database, then deploy it twice — once on a plain Ubuntu VM and once with Docker — and compare the two.

---

## What the app does

- **Students (full CRUD)** — add, list, search, edit, and delete student records (student ID, name, email, course, year level, status).
- **Attendance Kiosk** — a public page where a student types their ID to time in; a second tap times them out. Automatically marks entries `late` after 08:15.
- **Attendance Logs** — paginated, searchable, date-filterable audit trail of every time-in / time-out.
- **Dashboard** — admin overview: total active students, present today, checked out, a 7-day attendance bar chart, and recent activity.
- **Auth** — session-based login (admin account seeded by `AdminUserSeeder`).

## Tech stack

| Layer        | Technology                                   |
|--------------|----------------------------------------------|
| Backend      | Laravel 12 (PHP 8.2+)                        |
| Frontend     | Livewire 4, Tailwind CSS 4, Vite 6           |
| Database     | MySQL 8 (database: `attendance_system`)      |
| Auth         | Laravel session guard                        |
| Build tools  | Composer, npm                                |

## Project structure (the important parts)

```
app/
  Http/Controllers/AuthController.php      # login / logout
  Livewire/
    Dashboard.php                          # admin overview + chart data
    Students.php                           # full CRUD for students
    Kiosk.php                              # public time-in / time-out
    AttendanceLogs.php                     # searchable attendance log
  Models/
    Student.php                            # hasMany Attendance
    Attendance.php                         # belongsTo Student
database/
  migrations/                              # users, cache, jobs, students, attendances
  seeders/                                 # AdminUserSeeder + sample students/attendance
routes/web.php                             # all routes (auth-protected group + public kiosk)
resources/views/livewire/                  # Blade views for each Livewire component
config/database.php                        # MySQL + MariaDB connection config
.env.example                               # environment template (no real secrets)
```

## Prerequisites

- **PHP 8.2+** with extensions: `pdo_mysql`, `mbstring`, `xml`, `curl`, `zip`
- **Composer**
- **Node.js 18+** and **npm**
- **MySQL 8** (e.g. via XAMPP, or a standalone install)

## Getting started (local development)

```bash
# 1. Install PHP dependencies
composer install

# 2. Install JS dependencies
npm install

# 3. Create your environment file
cp .env.example .env
php artisan key:generate

# 4. Configure the database in .env
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=attendance_system
#    DB_USERNAME=root        # or your own user
#    DB_PASSWORD=            # your password

# 5. Create the database in MySQL
mysql -u root -e "CREATE DATABASE IF NOT EXISTS attendance_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 6. Run migrations and seed sample data
php artisan migrate
php artisan db:seed

# 7. Start the app (runs the server, queue, logs, and Vite together)
composer dev
```

Then open **http://127.0.0.1:8000**.

### Run only what you need

```bash
php artisan serve          # web server on http://127.0.0.1:8000
npm run dev                # Vite dev server (hot-reloading assets)
```

## Default login

The seeder creates an admin account:

| Field    | Value                |
|----------|----------------------|
| Email    | `admin@attendly.test`|
| Password | `password`           |

## Routes

| Method | Path                | Access  | Description              |
|--------|---------------------|---------|--------------------------|
| GET    | `/`                 | public  | redirects to `/login`    |
| GET    | `/login`            | public  | login form               |
| POST   | `/login`            | public  | authenticate             |
| GET    | `/kiosk`            | public  | attendance time-in/out   |
| GET    | `/dashboard`        | auth    | admin overview           |
| GET    | `/students`         | auth    | student CRUD             |
| GET    | `/attendance-logs`  | auth    | attendance log           |
| POST   | `/logout`           | auth    | log out                  |

## Environment variables

All configuration is read from `.env` (never committed). See `.env.example` for the full list. The database-related ones:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=attendance_system
DB_USERNAME=root
DB_PASSWORD=
```

> **Never commit your real `.env`.** It is listed in `.gitignore`. Only `.env.example` (with placeholder values) is tracked.

## Deploying

This app is meant to be deployed two ways for the midterm exam — see `notes_project/CHECKLIST.MD` for the full step-by-step:

1. **Ubuntu VM** — install PHP, Composer, Node, and MySQL by hand; clone the repo; `composer install && npm run build && php artisan migrate --force && php artisan serve`.
2. **Docker** — build an image from the `Dockerfile` and bring up the app + MySQL together with `docker-compose.yml` (both still to be added).

## Running tests

```bash
php artisan test
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
