MIT001.26 Advanced Platform and Networking Technologies
MIDTERM EXAMINATION

Build, Deploy, and Compare a CRUD App on a VM and in Docker

Mode Group exam (3-4 members per group)
Deadline Saturday, September 12, 2026
Total 100 points
Before You Start
● Do your own group's work. Copying another group's code or write-up isn't allowed.
● You can run your VM locally (VirtualBox, VMware) or on a free-tier cloud instance.
● What you'll submit: a link to your GitHub repo (code, Dockerfile, docker-compose.yml), your
Part 2 write-up, and your demo video.
Part 1: Build It and Deploy It Twice (60 points)
Build a small working app, then get the exact same app running two different ways: once on a plain
VM, and once through Docker.
Task What to Build Points

1.1 The App

Build a small CRUD app for a simple resource (for example, an
“items” list). It should connect to a real database (PostgreSQL,
MySQL, or MongoDB, etc). Use whatever language/framework
your group is comfortable with (Node.js, Python, Go, Java, etc).
Don’t hard-code your database password in the source code.

20 pts

1.2 VM Deployment

Set up an Ubuntu VM (local, using VirtualBox/VMware, or a free-
tier cloud instance). Install everything by hand (the runtime, the

database, whatever your app needs) and get it running there.
Only open the ports you actually need.

20 pts

1.3 Docker
Deployment

Containerize the same app. Write your own Dockerfile(s) and a
docker-compose.yml so the app and database run together. Get
it working from a clean build.

20 pts

Demo Video
Record a short video (a few minutes is enough) showing both deployments actually working.
Show: (1) the VM running your app, (2) docker compose bringing up your containers, and (3) a full
create-read-update-delete cycle done on both and prove they're both really working, not just
screenshots.

Part 2: Compare What You Built (40 points)
Answer each question below using what you actually observed while building Part 1 not just what a
textbook says. Aim for about 300-500 words per question.
# Topic Question Points
2.1 Speed and
Resource Use

How much RAM and CPU did each deployment use, and how
long did each take to start up? Use your own numbers from Part
1. What do you think caused the difference?

10 pts

2.2 Security

What security risks are specific to running your app in a
container versus in a VM? What would you actually need to
watch out for in each case?

10 pts

2.3 Updating and
Moving the App

If you needed to fix a bug or move this app to a new server, walk
through what that would involve in each environment. Which
one is less work, and why?

10 pts

2.4 Keeping Your Data
Safe

How does each environment handle keeping your database’s
data intact if the container or VM gets rebuilt? How would you
back each one up?

10 pts

What to Submit
● A link to your GitHub repo (your app code, Dockerfile(s), and docker-compose.yml).
● The address (IP or URL) where we can reach both your running VM and your running
container.
● Your Part 2 answers, as a Word doc or PDF.
● Your demo video (a shared link is fine).
Deadline: September 12, 2026. Group defense/demo schedule will be posted separately.