---
name: project-base-url
description: The app is served from /PTE-MANAGEMENT-SYSTEM/ not the root, so all absolute URL paths must include this prefix.
metadata:
  type: project
---

All absolute URL paths in this project must be prefixed with `/PTE-MANAGEMENT-SYSTEM/`.

For example:
- `/src/Auth/login.php` → `/PTE-MANAGEMENT-SYSTEM/src/Auth/login.php`
- `/src/Dashboard/index.php` → `/PTE-MANAGEMENT-SYSTEM/src/Dashboard/index.php`

**Why:** XAMPP serves the project from `http://localhost/PTE-MANAGEMENT-SYSTEM/`, not from the domain root.

**How to apply:** Whenever writing `header('Location: ...')`, `<a href="...">`, or `<form action="...">` with an absolute path, always prepend `/PTE-MANAGEMENT-SYSTEM`.
