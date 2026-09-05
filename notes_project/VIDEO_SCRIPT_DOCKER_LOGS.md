# Video Script — Verifying Docker Logs (Attendly)

Midterm project: **Build, Deploy, and Compare a CRUD App on a VM and in Docker**
Course: MIT001.26 — Advanced Platform and Networking Technologies
Stack: Laravel 12 + Livewire 4 + MySQL 8 + Tailwind + Vite (app = "Attendly")
References: `INSTRUCTIONS.MD` (Part 1 / Demo Video), `CHECKLIST.MD` (Steps 4, 5, 8), `DOCKER_COMMANDS.md` (Sections 4, 5, 8)

This script covers the **log verification** portion of the demo video: proving the
Docker deployment is genuinely running, healthy, and writing real data — by reading
the container logs, not just trusting the browser UI.

Estimated on-screen time: **~4–6 minutes.**

---

## Recording setup (do this before hitting record)

- Terminal open at the project root: `project_mit/` (the folder with `docker-compose.yml`).
- Docker Desktop is running (whale icon says "running").
- A browser tab open at `http://localhost:8000/login` (logged out for now).
- Font size bumped up so the audience can read the terminal output.
- Clear the terminal first: `clear` (so the screen isn't cluttered).

> Tip: Keep this script on a second monitor so you can read the narration while
> the terminal stays the focus of the recording.

---

## Scene 1 — Intro & what we're about to verify (≈ 30 sec)

**On screen:** Terminal, project root. Browser tab visible in the background.

**Narration (voiceover or on-camera):**

> "In this part of the demo I'm going to prove the Docker deployment of Attendly
> is actually running and writing to a real MySQL database — not just rendering
> pages in the browser. I'll do that by reading the container logs directly.
> The app is Laravel 12 with Livewire, and the database is MySQL 8, both running
> as Docker containers defined in `docker-compose.yml`."

**Action:** None yet — just introduce.

---

## Scene 2 — Bring the stack up from a clean build (≈ 60 sec)

> Maps to **CHECKLIST Step 5** and **DOCKER_COMMANDS Section 8**.

**On screen:** Type and run:

```bash
docker compose down
docker compose build --no-cache
docker compose up -d
```

**Narration:**

> "First I'll tear down anything currently running, then rebuild the image with
> no cache so we're proving the Dockerfile works from a truly clean build, and
> finally bring the stack up in the background."

**Wait for:** the `up` command to print that `attendly_db` is `Healthy` and
`attendly_app` is `Started` (same as the build output we already saw).

**Narration (after it finishes):**

> "Compose reports the database container became healthy and the app container
> started. The healthcheck in `docker-compose.yml` is what held the app back
> until MySQL was ready to accept connections."

---

## Scene 3 — Confirm both containers are running (≈ 30 sec)

> Maps to **CHECKLIST Step 4.3 / Step 5** and **DOCKER_COMMANDS Section 3**.

**On screen:**

```bash
docker ps
```

**Narration:**

> "`docker ps` lists the running containers. We expect two: `attendly_app` —
> the Laravel app — mapped to host port 8000, and `attendly_db` — MySQL — with
> port 3306 exposed only internally, not to the host. Notice the `STATUS`
> column shows `Up` and the db container shows `(healthy)` from the healthcheck."

**Point out (highlight with mouse or just read aloud):**
- `attendly_app` → `Up` → `0.0.0.0:8000->8000/tcp`
- `attendly_db` → `Up (healthy)` → `3306/tcp` (no host binding = not publicly exposed)

---

## Scene 4 — Read the app container logs (≈ 60 sec)

> Maps to **DOCKER_COMMANDS Section 4**.

**On screen:**

```bash
docker logs attendly_app
```

**Narration:**

> "Now the actual verification. `docker logs attendly_app` prints everything the
> app container wrote to stdout/stderr since it started. We're looking for three
> things: the migration output, the seeder output, and the `php artisan serve`
> startup line."

**Point out in the output (scroll if needed):**
- `Migration table created successfully.`
- `Migrating: 2026_08_30_000001_create_students_table`
- `Migrating: 2026_08_30_000002_create_attendances_table`
- `Migrated successfully.`
- `Seeding started` / `AdminUserSeeder` running
- `Server running on http://0.0.0.0:8000` (the artisan serve line)

**Narration:**

> "There's the migration log — both the students and attendances tables were
> created. The seeder ran and created our admin user. And the last line shows
> `php artisan serve` is listening on port 8000 inside the container. So the
> entrypoint did its job: migrate, seed, then start the server."

---

## Scene 5 — Follow the logs live while exercising the app (≈ 90 sec)

> This is the heart of the "logs verification" segment.

**On screen:** Split your view — terminal on one side, browser on the other
(or just switch tabs). In the terminal:

```bash
docker logs -f attendly_app
```

**Narration:**

> "Now I'll follow the logs live with the `-f` flag — like `tail -f`. While the
> logs stream, I'll log in to the app in the browser and perform a full
> Create-Read-Update-Delete cycle on Students. Watch the terminal: every request
> Laravel handles shows up here in real time."

**In the browser:**
1. Go to `http://localhost:8000/login`.
2. Log in as `admin@attendly.test`.
3. Open the **Students** page.
4. **Create** a student (e.g. student ID `2026-0001`, Juan Dela Cruz).
5. **Read** — confirm the new row appears in the list.
6. **Edit** — change the last name, save.
7. **Delete** — remove the student, confirm it's gone from the list.

**Narration while doing this:**

> "Logging in… there's the POST to `/login` in the logs. Now I create a student —
> that's a Livewire request, you can see it streaming by. I read the list, edit
> the record, and delete it. Every one of those actions produced a log line in
> the app container. This proves the Laravel process inside the container is
> actually handling my requests — the browser isn't faking anything."

**Stop following:** press `Ctrl+C` in the terminal.

---

## Scene 6 — Read the MySQL container logs (≈ 45 sec)

**On screen:**

```bash
docker logs attendly_db
```

**Narration:**

> "The app logs prove Laravel is working. Now let's confirm the database side.
> `docker logs attendly_db` shows MySQL's own output — startup, ready for
> connections, and the connections coming in from the app container."

**Point out:**
- `ready for connections.`
- The host/port MySQL is listening on (`3306`)
- Any lines showing connections from the app (e.g. `attendly` user connecting)

**Narration:**

> "MySQL reports it's ready for connections on 3306, and we can see the app
> connecting as the `attendly` user — not root. That matches the credentials we
> passed in through environment variables in `docker-compose.yml`, never
> hardcoded in the source."

---

## Scene 7 — Prove the data actually landed in MySQL (≈ 60 sec)

> Maps to **CHECKLIST Step 5** ("Confirm the data is really being read from and
> written to MySQL") and **DOCKER_COMMANDS Section 5**.

**On screen:**

```bash
# Create a fresh student in the browser first, then run:
docker exec attendly_db mysql -u attendly -psecret attendance_system -e "SELECT * FROM students;"
```
---

## Scene 9 — Wrap-up (≈ 20 sec)

**Narration:**

> "To recap: we brought the stack up from a clean build, confirmed both
> containers are running and the database is healthy, read the app logs to see
> migrations and the server starting, followed the logs live while doing a full
> CRUD cycle, read the MySQL logs to see real connections, and queried MySQL
> directly to prove the data is actually stored. The Docker deployment is
> genuinely working end-to-end."

---

## Cut list / what to definitely keep in the edit

- [ ] `docker compose up -d` finishing with `Healthy` + `Started`
- [ ] `docker ps` showing both containers, db `(healthy)`, port 8000 mapped, 3306 internal
- [ ] `docker logs attendly_app` showing migrations + seeder + `Server running on http://0.0.0.0:8000`
- [ ] `docker logs -f attendly_app` streaming while you do Create → Read → Update → Delete in the browser
- [ ] `docker logs attendly_db` showing MySQL ready + app connections


## Common things to avoid

- Don't show the real DB password on screen if you can avoid it — the script
  uses `secret` as a placeholder; if yours differs, consider blurring or just
  narrate "I'll type the password" without showing it.
- Don't skip the live `-f` log follow — that's the most convincing part.
- Don't rush the CRUD cycle; let each log line land before the next action.
