# Video Script — Checking Docker Performance (Attendly)

Midterm project: **Build, Deploy, and Compare a CRUD App on a VM and in Docker**
Course: MIT001.26 — Advanced Platform and Networking Technologies
Stack: Laravel 12 + Livewire 4 + MySQL 8 + Tailwind + Vite (app = "Attendly")
References: `INSTRUCTIONS.MD` (Part 2.1), `CHECKLIST.MD` (Steps 5, 6, 8), `DOCKER_COMMANDS.md` (Sections 8, 9)

This is the **performance check** part of the demo video. The goal is simple:
run a few terminal commands that show how fast Docker starts and how little
RAM/CPU it uses — real numbers we can compare against the VM later.

Estimated on-screen time: **~3 minutes.**

---

## Before you record

- Terminal open at the project root (`project_mit/`).
- Docker Desktop running (whale icon says "running").
- Browser tab open at `http://localhost:8000/login`.
- Bump up the font size so the audience can read the output.
- Run `clear` so the screen is clean.

---

## Scene 1 — Quick intro (≈ 20 sec)

**Narration:**

> "Now I'll check how the Docker deployment performs — how fast it starts up
> and how much RAM and CPU it uses. These are the real numbers we'll compare
> against the VM in Part 2."

---

## Scene 2 — Time how fast it starts (≈ 60 sec)

**On screen:**

```bash
docker compose down
time docker compose up --build -d
```

**Narration (before pressing enter):**

> "First I'll tear it down, then time a full rebuild and start with `time`.
> The `real` number at the end is how many seconds the whole thing took."

**Wait for it to finish**, then read the `real` number out loud.

**Narration:**

> "About ____ seconds from cold to both containers up. I'll note that down."

**Then confirm the app is actually ready:**

```bash
docker ps
```

**Narration:**

> "`docker ps` shows both containers are up and the database is healthy — so
> the app is ready to use."

---

## Scene 3 — Check RAM and CPU usage (≈ 60 sec)

**On screen:**

```bash
docker stats --no-stream
```

**Narration:**

> "`docker stats --no-stream` gives a one-shot snapshot of each container's
> CPU and memory. Two rows: the app and the database."

**Read the numbers out loud and note them:**
- App container: ____ MiB RAM, ____% CPU
- DB container: ____ MiB RAM, ____% CPU

**Narration:**

> "The app is using about ____ MiB and MySQL about ____ MiB. CPU is just a few
> percent at idle. I'll add these to the comparison table."

---

## Scene 4 — Fill in the table on screen (≈ 30 sec)

**On screen (show this table — fill in the Docker column with your real numbers):**

| Measurement              | VM | Docker |
|--------------------------|----|--------|
| Startup time             |    | ____ s |
| RAM usage                |    | ____ MiB |
| CPU usage                |    | ____ % |

**Narration:**

> "Here's the table for Part 2.1. We'll fill in the VM column with numbers
> measured the same way, then compare. The idea is that Docker shares the host
> kernel so it starts faster and uses less RAM than a full VM running its own
> guest OS."

---

## Scene 5 — Wrap-up (≈ 15 sec)

**Narration:**

> "That's the Docker performance check — startup time, RAM, and CPU, all from
> the terminal. Real numbers, not estimates."

---

## Cut list (keep these in the edit)

- [ ] `time docker compose up --build -d` with the `real` time visible
- [ ] `docker ps` showing both containers healthy
- [ ] `docker stats --no-stream` showing RAM and CPU for both containers
- [ ] The filled-in comparison table

## Things to avoid

- Don't measure against a cached build — use `--build` so it's a real cold start.
- Don't read `docker stats` during the startup spike — use `--no-stream` after
  the app has settled.
- Don't forget to say the numbers out loud so the grader can hear them.
