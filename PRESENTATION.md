# PTE Management System — Demo Presentation Flow

**Audience:** Lecturers (ICT502 / ITS571 — Database Engineering)
**Goal:** Show a working, role-based tuition centre management system built on PHP + Oracle 23ai, covering the full operational cycle — user management → enrolment → scheduling → attendance → billing — plus the SQL breadth required by the syllabus.

**Suggested demo accounts** (adjust to your seeded data):

| Role | Email | Notes |
|---|---|---|
| Owner | `owner@pte.edu.my` | Full access, only role that manages Users |
| Admin | `ahmad.fauzi@pte.edu.my` | Operational access, no Users/Reports |
| Tutor | `hafiz@pte.edu.my` | Own classes/sessions/attendance only |

---

## 0. Introduction (2 min)

- **What it is:** a web-based tuition centre management system — user roles, class scheduling, student enrolment, attendance tracking, invoicing, and payment recording.
- **Stack:** native PHP (no framework), Oracle 23ai Free via OCI8, Tailwind CSS (CDN), vanilla JS only — no ORM, no query builder, all raw SQL with bind variables.
- **Why this matters for the module:** every screen is backed by hand-written SQL demonstrating the full syllabus — SELECT, INSERT, UPDATE, DELETE, JOIN, GROUP BY, HAVING, subqueries, aggregate functions, ORDER BY. (`sql/operations_reference.sql` has one fully-annotated example of each, pulled directly from the live code.)
- Briefly show the login screen — mention the role model: **Owner → Admin → Tutor**, with `USERS.SUPERVISOR_ID` self-referencing for the reporting hierarchy.

---

## 1. Owner Walkthrough (10–12 min)

Log in as **Owner** — the only role with full system access.

### 1.1 Dashboard
- Role-aware dashboard: combines **Admin Overview** + **Tutor Overview** sections automatically if a user holds both profiles (`ADMIN_PROFILE` + `TUTOR_PROFILE` — a user can be both).
- KPI cards, students-by-grade chart, today's sessions, quick actions.

### 1.2 User Management (Owner-only module)
- `/users` — list of all Owner/Admin/Tutor accounts, with an amber **"Admin + Tutor"** badge when a user holds both profiles.
- **Add User** (`/users/create`):
  - Checkbox-based role assignment (Admin / Tutor — not mutually exclusive).
  - **Supervisor assignment** — dropdown restricted to Owner/Admin users only, backing the `SUPERVISOR_ID` self-referencing FK on `USERS`.
  - Conditional fields (Department for Admin, Qualification/Specialisation for Tutor) shown/hidden via JS depending on the checkboxes.
- **Edit User** (`/users/edit`) — same dual-role model; profile rows are reconciled (insert/update/delete) independently per checkbox state.
- **Profile photo** (`/profile`) — any logged-in user can upload a profile photo with a **live preview** before saving (FileReader-based, no page reload needed to see the change).

### 1.3 Academic Setup
- **Subjects** (`/subjects`) and **Grades** (`/grades`) — simple reference-data CRUD, the foundation every class is built on.

### 1.4 Class Management
- `/classes` — list with filters (subject, grade, status) and a live enrolled-count per class (LEFT JOIN + GROUP BY — point this out as syllabus JOIN/GROUP BY coverage).
- **Add Class** (`/classes/create`) — subject/grade/tutor selection. Tutor dropdown uses the **searchable combobox** (type-to-filter — worth demoing since the class list grows with every hire).
- **Class Detail** (`/classes/show`):
  - Add recurring **Schedule** (day of week, time range, effective dates).
  - **Enrol Students** — bulk enrolment with checkboxes, default-filtered to the class's own grade (with an "all grades" override), so admins aren't scrolling through irrelevant students.

### 1.5 Scheduling
- **Generate Sessions** (`/schedule/generate`) — bulk-generates `CLASS_SESSION` rows from `CLASS_SCHEDULE` templates over a date range, with a **preview step** before committing, and automatic duplicate-skipping.
- **Manual Session** (`/sessions/create`) — for one-off makeup/rescheduled sessions outside the recurring pattern (e.g. replacing a cancelled class).
- Both use the same searchable class dropdown.

### 1.6 Attendance Oversight
- `/attendance/report` — full filterable log (class, status, date range) plus:
  - **Low Attendance flag** — automatically surfaces students attending **below 80%** of their sessions, computed with a `GROUP BY` + `HAVING` query (this is the HAVING syllabus example — show `sql/operations_reference.sql` section 7 alongside it).
  - **Export to CSV** — respects whatever filters are currently applied.

### 1.7 Billing Cycle
- **Generate Invoice** (`/invoices/generate`) — monthly invoice generation per parent (searchable parent dropdown), one invoice per parent per billing month (enforced by a unique constraint).
- **Invoice List** (`/invoices`) — status pills (Unpaid/Partial/Paid/Overdue), filter by month/year/status.
- **Record Payment** (`/payments/record`) — updates invoice status automatically based on amount paid vs total.
- **Receipt generation** — on payment, a PDF receipt is generated (Dompdf) and **emailed to the parent** (via Mailtrap sandbox in dev) with a secure, unguessable **signed-token link** (`/receipts/view?token=...`) — no parent login required. Demo: open the emailed link, show the receipt, download as PDF.
- **Payment History** (`/payments/history`) — full transaction log with method breakdown (Cash/Bank Transfer/Online/Cheque), exportable.

### 1.8 Reporting Across Modules
- Point out the **Export to CSV** button present on Students, Parents, Classes, Invoices, Payments, and Attendance — every export mirrors the current on-screen filters, so what you see is what you get in Excel.

---

## 2. Admin Walkthrough (5–6 min)

Log in as **Admin** — same operational access as Owner, *minus* User Management and Reports (per the Role Access Matrix).

- Dashboard looks the same shape but **no Users nav item** — demonstrate the role guard by trying to hit `/users` directly and getting redirected to the dashboard.
- Quickly re-show Students / Parents / Classes / Schedule / Invoices / Payments to confirm Admin has full operational parity with Owner there — the difference is purely administrative scope (staff accounts, hierarchy, org-wide reports), not day-to-day operations.

---

## 3. Tutor Walkthrough (6–8 min)

Log in as **Tutor** — deliberately the most restricted role, scoped to "my classes only."

### 3.1 Tutor Dashboard
- Different dashboard shape: **Next Up** card, attendance donut chart, upcoming sessions.
- **Monthly calendar widget** — current month grid with session tags color-coded by status (Scheduled/Completed/Cancelled), max 2 shown per day + "+N more" overflow.

### 3.2 Sessions (own classes only)
- `/sessions` — tutor sees only sessions for classes they teach (enforced via `cs.user_id = :tutor_id` in the WHERE clause, not just hidden in the UI).

### 3.3 Attendance
- `/attendance` — **tutor-only landing page**: "Today & Upcoming" + "Recent Sessions", each linking straight into taking attendance for that session. (This exists specifically because tutors with no scheduled sessions used to get redirected in a confusing way — now they land on a proper empty/upcoming view.)
- **Take Attendance** (`/attendance/take`) — mark Present/Absent/Late per student for a session.
- `/attendance/report` — tutor sees only their own classes' attendance, same low-attendance flag applies, scoped to their students only.

### 3.4 What Tutors Cannot Do
- No access to Users, Students CRUD, Parents, Class management, Invoices, Payments, or Reports — demonstrate one blocked route (e.g. `/students/create`) redirecting away, to show the access matrix is enforced server-side, not just hidden buttons.

---

## 4. System Health & Data Integrity (3–4 min)

- **System Test page** (`/system-test`, Owner-only) — a live smoke-test dashboard: DB connectivity, all 17 tables reachable, representative bind-variable queries per module (regression guard), routing table validation, SMTP handshake, PDF generation, filesystem write check, PHP/extension versions. Run it live — everything should show green/pass.
- Mention this was built specifically to catch a real class of bug encountered during development: Oracle reserves certain words (`UID`, `FROM`, `TO`, `START`) as bind-variable names, which silently breaks queries at runtime (not at PHP syntax-check time) — the System Test's "Module Queries" group exercises exactly these patterns so a regression would be caught immediately instead of only surfacing when a tutor happens to log in.

---

## 5. SQL Deep-Dive (5 min, if time allows)

Open `sql/operations_reference.sql` and walk through 2–3 examples lecturers will want to see mapped to real usage, not textbook toy queries:

- **JOIN + GROUP BY** — the Classes list query (3 equijoins + 1 LEFT JOIN, live enrolled-count).
- **HAVING** — the low-attendance flag (`SUM(CASE WHEN status IN ('PRESENT','LATE')...) < 0.8 * COUNT(*)`), explicitly contrasted with WHERE's inability to filter on an aggregate before grouping.
- **Correlated Subquery** — Grades list, per-row student/class counts without a join fan-out.
- Note every example is annotated with the exact source file/line it's pulled from — this isn't a separate toy script, it's a documented trace-back to production code.

---

## 6. Closing / Q&A (2–3 min)

- Recap: full CRUD across 8+ entities, 3-tier RBAC enforced server-side, real scheduling/billing/receipt workflows (not just static tables), and syllabus-compliant SQL throughout — no window functions, no CTEs, no PL/SQL, per course constraints.
- Open floor for questions — likely areas: why native PHP over a framework (control + syllabus transparency), why Oracle-specific syntax choices, how the reserved-keyword bind bug was diagnosed, or a request to see any specific query live in SQL*Plus/Developer against the same schema shown on screen.

---

## Timing Summary

| Section | Time |
|---|---|
| Introduction | 2 min |
| Owner walkthrough | 10–12 min |
| Admin walkthrough | 5–6 min |
| Tutor walkthrough | 6–8 min |
| System health | 3–4 min |
| SQL deep-dive | 5 min |
| Closing/Q&A | 2–3 min |
| **Total** | **~35–40 min** |

Trim the SQL deep-dive first if running short — the live walkthroughs are the stronger demo material; the SQL file stands on its own if lecturers want to review it after the session.
