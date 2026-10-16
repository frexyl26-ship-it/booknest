# BookNest - Library Management System

PHP + MySQL + Tailwind CSS + vanilla JavaScript.

## Features
- Register / login / logout with password_hash (bcrypt) and sessions
- Role-based access: **admin** and **member**
- Members: search catalog (live search), borrow books (max 3, 7-day loans), return books
- Admin: full CRUD for books and categories, view/return all borrowings, manage users, dashboard stats
- Server-side and client-side validation, prepared statements (PDO), XSS escaping, CSRF tokens
- Responsive layout

## Setup (XAMPP)
1. Copy this folder to `C:\xampp\htdocs\booknest`
2. In phpMyAdmin, import `database/schema.sql` (it creates `booknest_db`)
3. Open `http://localhost/booknest/setup_admin.php` once, then delete that file
4. Visit `http://localhost/booknest/`

## Test credentials
- Admin: `admin@booknest.com` / `Admin@123`
- Member: register a new account

## Deploying (InfinityFree)
Edit `config/database.php`: set `BASE_URL` to `''` and use your hosting DB credentials.
