MIT001.26 — Advanced Platform and Networking Technologies
Midterm Project Checklist
Build, Deploy, and Compare a CRUD App on a VM and in Docker
Deadline: Saturday, September 12, 2026

How to Use This Checklist
Work through the steps in order — each step builds on the one before it. Tick each box as your group finishes it.
Our stack: Laravel 12 (PHP 8.2+) + Livewire 4 + MySQL + Tailwind CSS + Vite. The app is an attendance system ("Attendly") that manages Students (full CRUD) and Attendance records (kiosk time-in / time-out + logs). This checklist replaces the earlier Express/MongoDB plan — we are continuing with this Laravel codebase instead.

STEP 1: Set Up the GitHub Repository
[x]  Create / use the GitHub repository (project_mit).
[x]  Confirm .gitignore excludes vendor/, node_modules/, .env, and storage/*.key (already present).
[x]  Confirm .env.example exists with variable names only (DB_DATABASE, DB_USERNAME, DB_PASSWORD, etc.) — no real values committed.
[ ]  Replace the default Laravel README.md with a project-specific one describing the app and how to run it (see README.md at repo root).
[ ]  Commit and push the current working setup to GitHub.

STEP 2: Build the CRUD Application (Laravel + Livewire + MySQL)
2.1 Project foundation
[x]  Laravel 12 project initialized (composer.json requires php ^8.2, laravel/framework ^12, livewire/livewire 4.0).
[x]  Database configuration lives in .env (DB_CONNECTION=mysql, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD) — no hardcoded credentials in source.
[x]  APP_KEY generated via php artisan key:generate.
2.2 The "Student" resource (full CRUD)
[x]  Migration 2026_08_30_000001_create_students_table — fields: student_id (unique), first_name, last_name, email (nullable/unique), course, year_level, status.
[x]  Eloquent model App\Models\Student (fillable + hasMany attendances + full_name accessor).
[x]  Livewire component App\Livewire\Students — Create (create/save), Read (paginated list + search), Update (edit/save), Delete (delete with confirm).
[x]  Blade view resources/views/livewire/students.blade.php — table, search, add/edit modal, delete buttons.
[x]  Route GET /students (auth-protected) in routes/web.php.
2.3 The "Attendance" resource (kiosk + logs)
[x]  Migration 2026_08_30_000002_create_attendances_table — fields: student_id (FK, cascade delete), attendance_date, time_in, time_out (nullable), status; unique(student_id, attendance_date).
[x]  Eloquent model App\Models\Attendance (casts for date/datetime + belongsTo student).
[x]  Livewire component App\Livewire\Kiosk — time-in / time-out by student ID (auto-detects first tap vs second tap; marks late after 08:15).
[x]  Livewire component App\Livewire\AttendanceLogs — paginated, searchable, filterable by date.
[x]  Routes GET /kiosk (public) and GET /attendance-logs (auth-protected).
2.4 Auth + dashboard
[x]  AuthController (show/login/logout) with session-based auth.
[x]  Login view + /login route; / redirects to /login.
[x]  Livewire Dashboard — total active students, present today, checked out, 7-day bar chart, recent activity.
[x]  AdminUserSeeder seeds admin@attendly.test / password.
2.5 Frontend assets
[x]  Tailwind CSS 4 via @tailwindcss/vite + Vite 6 (vite.config.js, package.json).
[x]  Layouts: layouts.app (admin) and layouts.kiosk (kiosk).
2.6 Test it locally
[x]  Run the full cycle once on your own machine: Create student → Read list → Edit student → Delete student.
[x]  Perform a kiosk time-in and time-out; confirm the record appears in Attendance Logs.
[x]  Confirm the data actually appears in MySQL (e.g. SELECT * FROM students; SELECT * FROM attendances;) — not just in the browser.
[ ]  Commit the working application to GitHub.

STEP 3: Deploy on the Ubuntu VM (no Docker here)
3.1 Create the VM
[ ]  Create an Ubuntu 24.04 VM (VirtualBox, VMware, or a free-tier cloud instance).
[ ]  Give it enough RAM/CPU to run Ubuntu, PHP, and MySQL comfortably (≥ 2 GB RAM recommended).
[ ]  Decide the network mode: Bridged Adapter (VM gets its own LAN IP — easiest to reach) or NAT with port forwarding (use only if bridging isn't possible).
3.2 Install everything by hand
[ ]  Update Ubuntu packages (sudo apt update && sudo apt upgrade).
[ ]  Install Git (sudo apt install git).
[ ]  Install PHP 8.2+ with extensions: sudo apt install php8.2 php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd (verify with php -v).
[ ]  Install Composer (follow getcomposer.org instructions; verify with composer -V).
[ ]  Install Node.js + npm (e.g. via NodeSource or nvm; verify with node -v, npm -v).
[ ]  Install MySQL server (sudo apt install mysql-server); run sudo mysql_secure_installation; verify it's running (systemctl status mysql).
[ ]  Create the database: sudo mysql -e "CREATE DATABASE attendance_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;".
[ ]  Create a DB user (do not use root in production): sudo mysql -e "CREATE USER 'attendly'@'localhost' IDENTIFIED BY '<strong-password>'; GRANT ALL ON attendance_system.* TO 'attendly'@'localhost'; FLUSH PRIVILEGES;".
[ ]  Open only the ports you actually need (e.g. ufw allow 8000/tcp for the app) — leave 3306 closed to the outside unless required.
3.3 Run the app on the VM
[ ]  Clone your GitHub repo into the VM (git clone <repo-url>).
[ ]  Create the .env file directly on the VM (cp .env.example .env) and fill in DB_DATABASE=attendance_system, DB_USERNAME, DB_PASSWORD — do not commit it.
[ ]  Run composer install --no-dev --optimize-autoloader.
[ ]  Run npm install && npm run build (compile assets for production).
[ ]  Run php artisan key:generate.
[ ]  Run php artisan migrate --force (and php artisan db:seed --force if you want sample data).
[ ]  Start the app: php artisan serve --host=0.0.0.0 --port=8000 (or set up Nginx + PHP-FPM for a more production-like setup).
[ ]  Open the app from a browser using the VM's IP:8000 and confirm it loads.
[ ]  Perform a full Create → Read → Update → Delete cycle on Students and confirm the data lands in MySQL.
[ ]  Write down the VM's IP address and port — you'll need this for submission.
Note: The assignment specifically wants everything installed by hand here — no Docker in this step.

STEP 4: Containerize the App with Docker
4.1 Write the Dockerfile
[ ]  Choose a PHP base image with extensions (e.g. php:8.2-fpm or php:8.2-cli; install pdo_mysql, mbstring, xml, zip, gd).
[ ]  Set the working directory inside the image (WORKDIR /var/www/html).
[ ]  Install Composer inside the image (COPY composer.json composer.lock, run composer install --no-dev --optimize-autoloader).
[ ]  Copy the rest of the application source code in.
[ ]  Run npm install && npm run build at build time (multi-stage build recommended: use a node image to compile assets, then copy public/build into the final image).
[ ]  Expose the port your app listens on inside the container (EXPOSE 8000).
[ ]  Set the command that starts the app (php artisan serve --host=0.0.0.0 --port=8000) — or use PHP-FPM + a separate Nginx container.
[ ]  Ensure storage/ and bootstrap/cache/ are writable by the web user (chmod/chown).
4.2 Write docker-compose.yml
[ ]  Add a service for your Laravel app (build: ., ports: "8000:8000").
[ ]  Add a service for MySQL (image: mysql:8.0, environment for MYSQL_DATABASE, MYSQL_USER, MYSQL_PASSWORD, MYSQL_ROOT_PASSWORD — all from .env).
[ ]  Let the app connect to MySQL using the service name as DB_HOST (e.g. DB_HOST=db) — not localhost.
[ ]  Pass database configuration through environment variables in docker-compose.yml, not hardcoded values.
[ ]  Add a named Docker volume for MySQL's data (volumes: dbdata:/var/lib/mysql) so it survives container restarts.
[ ]  Add a healthcheck for MySQL so the app waits until the DB is ready before migrating.
[ ]  Map your app's container port to a host port so it's reachable in a browser (ports: "8000:8000").
[ ]  Do not expose MySQL's port to the public host unless you genuinely need to (omit "3306:3306" or bind to 127.0.0.1 only).
[ ]  Add an entrypoint/command that runs php artisan migrate --force && php artisan db:seed --force before starting php artisan serve.
4.3 Test it
[ ]  Run docker compose up --build and confirm both services start.
[ ]  Run docker ps and confirm both the app and MySQL containers are running.
[ ]  Perform a full Create → Read → Update → Delete cycle on Students through the Docker deployment.
[ ]  Perform a kiosk time-in / time-out and confirm it appears in Attendance Logs.

STEP 5: Prove the Docker Deployment Works From a Clean Build
[ ]  Stop the current deployment (docker compose down).
[ ]  Rebuild with no cache: docker compose build --no-cache.
[ ]  Bring it back up: docker compose up.
[ ]  Run docker ps and confirm both containers are healthy.
[ ]  Open the app in a browser and perform Create → Read → Update → Delete again.
[ ]  Confirm the data is really being read from and written to MySQL (docker compose exec db mysql -u attendly -p attendance_system -e "SELECT * FROM students;").
Note: This step directly satisfies the rubric's requirement to show the Docker deployment "working from a clean build" — don't skip it, and capture it in your demo video (Step 8).

STEP 6: Collect Your Resource & Startup Measurements
[ ]  Measure VM RAM/CPU usage while the app is running (free -h, top, or htop).
[ ]  Measure Docker RAM/CPU usage while the app is running (docker stats).
[ ]  Time how long each deployment takes to go from "start" to "usable in the browser" — define this the same way for both (e.g. VM: after boot, run php artisan serve, time until it responds; Docker: from docker compose up to the app responding with HTTP 200).
[ ]  Fill in the table below with your group's real numbers — do not estimate or invent them.

Measurement              VM       Docker
Startup time (start → app usable)
RAM usage
CPU usage
Notes / observations

STEP 7: Write the Part 2 Answers (300–500 words each)
2.1 — Speed and Resource Use
[ ]  State your actual VM and Docker RAM/CPU numbers and startup times from Step 6.
[ ]  Explain the difference in terms of virtualization overhead (full guest OS on the VM) vs. containers sharing the host OS kernel.
2.2 — Security
[ ]  Cover VM-specific risks: SSH exposure, unpatched OS packages, firewall/ufw port configuration, exposed MySQL port, running php artisan serve as root.
[ ]  Cover container-specific risks: vulnerable base images (pin tags, scan with docker scout/trivy), running the container as root, exposed ports, secrets in environment variables / docker-compose.yml, volume permissions, container escape.
[ ]  Mention what your group actually configured (e.g. which ports you closed with ufw, whether you ran the container as a non-root user, whether MySQL's port was left unexposed).
2.3 — Updating and Moving the App
[ ]  Walk through fixing a bug and redeploying on the VM (git pull, composer install, npm run build, php artisan migrate, restart php artisan serve / PHP-FPM).
[ ]  Walk through fixing a bug and redeploying with Docker (git pull, docker compose build, docker compose up -d).
[ ]  State which one was less work in your actual experience, and why.
2.4 — Keeping Your Data Safe
[ ]  Explain where MySQL's data physically lives in the VM setup (/var/lib/mysql/attendance_system).
[ ]  Explain how the Docker named volume keeps data alive even if the container is rebuilt or removed (the volume is independent of the container's writable layer).
[ ]  Explain what would happen if the named volume itself were deleted (data loss — the volume is the data).
[ ]  Describe a backup plan using mysqldump, and a restore plan, for each environment:
    - VM:  mysqldump -u attendly -p attendance_system > backup.sql  /  mysql -u attendly -p attendance_system < backup.sql
    - Docker:  docker compose exec db mysqldump -u attendly -p attendance_system > backup.sql  /  docker compose exec -T db mysql -u attendly -p attendance_system < backup.sql
[ ]  Save all four answers as a single Word doc or PDF.

STEP 8: Record the Demo Video
[ ]  Briefly introduce the project (Attendly attendance system — Laravel + Livewire + MySQL).
[ ]  Show the VM deployment running — display its IP/port.
[ ]  Perform Create → Read → Update → Delete on Students on the VM deployment.
[ ]  Show a kiosk time-in / time-out on the VM.
[ ]  Show docker compose up --build running from a clean build.
[ ]  Run docker ps and show both containers healthy.
[ ]  Perform Create → Read → Update → Delete on Students on the Docker deployment.
[ ]  Perform a kiosk time-in / time-out on Docker.
[ ]  Briefly show that both deployments are backed by a real, working MySQL database (run a SELECT in each), not just the browser UI.
[ ]  Keep it short and focused — a few minutes proving both deployments genuinely work is enough.
[ ]  Upload it and generate a shareable link.

STEP 9: Final Submission Checklist
[ ]  GitHub repo link (app code, Dockerfile, docker-compose.yml, README.md, .env.example — no .env, no vendor/, no node_modules/).
[ ]  Working VM address (IP/URL) and port.
[ ]  Working Docker deployment address (IP/URL) and port.
[ ]  Part 2 write-up as a Word doc or PDF.
[ ]  Demo video link.
[ ]  Everyone in the group has tested the demo and can explain how the whole system works.
[ ]  Submit everything before Saturday, September 12, 2026.
