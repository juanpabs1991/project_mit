# Docker Commands — Attendly (Laravel + MySQL)

A quick reference for running, stopping, rebuilding, and debugging the Docker
deployment of this app. Run every command from the **project root**
(`project_mit/`), the folder that contains `docker-compose.yml`.

---

## 0. Prerequisites

- Docker Desktop installed and running (whale icon in menu bar says "running")
- Verify before you start:

```bash
docker --version          # should print a version number
docker compose version    # should print a version number
docker info               # should connect to the daemon without error
```

If `docker info` fails with "cannot connect to the Docker daemon", open Docker
Desktop and wait until it says "running".

---

## 1. First-time setup (only needed once)

Make sure you have a `.env` file in the project root. If you cloned the repo
fresh, copy the example and fill in the Docker values:

```bash
cp .env.example .env
```

Then edit `.env` and set these (they must match docker-compose.yml):

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=attendance_system
DB_USERNAME=attendly
DB_PASSWORD=secret
APP_URL=http://localhost:8000
```

> `.env` is git-ignored — it never gets committed. Each machine/VM needs its own.

---

## 2. Build and start the app (everyday use)

```bash
docker compose up --build -d
```

What this does:
- `--build`  → builds the Docker image from `Dockerfile` (uses cache if unchanged)
- `-d`       → detached mode (runs in the background, gives your terminal back)

When it's done, open: **http://localhost:8000/login**

Login credentials:
- Email: `admin@attendly.test`
- Password: *(see `database/seeders/AdminUserSeeder.php`)*

---

## 3. Check what's running

```bash
docker ps
```

You should see two containers:

| Container      | Status              | Ports                        |
|----------------|---------------------|------------------------------|
| `attendly_app` | Up                  | 0.0.0.0:8000->8000/tcp       |
| `attendly_db`  | Up (healthy)        | 3306/tcp (internal only)     |

If you don't see them, they're not running — start them with Step 2.

---

## 4. View logs (debugging)

```bash
# App container logs (migrations, server startup, errors)
docker logs attendly_app

# Follow logs live (Ctrl+C to stop watching)
docker logs -f attendly_app

# MySQL container logs
docker logs attendly_db
```

---

## 5. Run commands inside the containers

```bash
# Run artisan inside the app container
docker exec attendly_app php artisan migrate --force
docker exec attendly_app php artisan db:seed --force
docker exec attendly_app php artisan key:generate
docker exec attendly_app php artisan route:list

# Open a shell inside the app container
docker exec -it attendly_app bash

# Run MySQL queries inside the db container
docker exec attendly_db mysql -u attendly -psecret attendance_system -e "SHOW TABLES;"
docker exec attendly_db mysql -u attendly -psecret attendance_system -e "SELECT * FROM students;"
docker exec attendly_db mysql -u attendly -psecret attendance_system -e "SELECT * FROM attendances;"
```

---

## 6. Stop the app (keeps data)

```bash
docker compose down
```

This stops and removes both containers, but the MySQL **named volume**
(`dbdata`) is preserved — your data survives. The app will be unreachable at
http://localhost:8000 until you start it again (Step 2).

Verify it's stopped:

```bash
docker ps          # should show no attendly containers
```

---

## 7. Stop AND delete all data (fresh start)

```bash
docker compose down -v
```

The `-v` flag deletes the named volume `dbdata`. **This wipes your MySQL data
completely.** Use this only when you want a truly clean slate (e.g. re-running
migrations and seeders from scratch).

---

## 8. Rebuild from scratch (no cache) — for Step 5 of the checklist

This proves the Docker deployment works from a clean build:

```bash
# 1. Stop everything
docker compose down

# 2. Rebuild with no cache (slower, but proves the Dockerfile works from scratch)
docker compose build --no-cache

# 3. Start it up
docker compose up -d

# 4. Verify both containers are healthy
docker ps

# 5. Open http://localhost:8000 and do a full CRUD cycle
```

---

## 9. Resource usage (for Step 6 of the checklist)

```bash
# Live RAM/CPU usage of all containers (Ctrl+C to stop)
docker stats

# One-shot snapshot
docker stats --no-stream
```

---

## 10. Backup and restore MySQL data (for Part 2 write-up)

### Backup
```bash
docker exec attendly_db mysqldump -u attendly -psecret attendance_system > backup.sql
```

### Restore
```bash
docker exec -i attendly_db mysql -u attendly -psecret attendance_system < backup.sql
```

---

## 11. Clean up everything (images, volumes, networks)

> WARNING: This removes ALL Docker data on your machine, not just this project.
> Only run this if you want to free up disk space and don't need any containers.

```bash
docker compose down -v          # stop + remove this project's containers + volume
docker system prune -a          # remove all unused images, containers, networks
```

---

## Quick cheat sheet

| What you want to do              | Command                                      |
|----------------------------------|----------------------------------------------|
| Start the app                    | `docker compose up --build -d`               |
| Stop the app (keep data)         | `docker compose down`                        |
| Stop + wipe data                 | `docker compose down -v`                     |
| See running containers           | `docker ps`                                  |
| See app logs                     | `docker logs attendly_app`                   |
| Follow app logs live             | `docker logs -f attendly_app`                |
| Query MySQL                      | `docker exec attendly_db mysql -u attendly -psecret attendance_system -e "SELECT * FROM students;"` |
| Rebuild from scratch             | `docker compose build --no-cache`            |
| See RAM/CPU usage                | `docker stats`                               |
| Backup database                  | `docker exec attendly_db mysqldump -u attendly -psecret attendance_system > backup.sql` |
