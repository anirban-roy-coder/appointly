# AppointEase – Appointment Booking System

A full-stack appointment booking application built with **PHP (LAMP stack)** + **Vue.js 3**.

---

## Tech Stack

| Layer    | Technology                             |
|----------|----------------------------------------|
| Backend  | PHP 8.1+, MySQL, Apache / PHP built-in |
| Frontend | Vue.js 3, Vite 5, Tailwind CSS v3      |
| Auth     | Custom JWT (HMAC-SHA256, zero deps)    |
| HTTP     | Axios                                  |
| State    | Pinia                                  |

---

## Features

- Register & Login (JWT-based authentication)
- View user profile with appointment statistics
- Book appointments (select date + time slot)
- View all appointments filterable by status
- Edit pending appointments
- Cancel appointments (with confirmation)
- Duplicate time-slot prevention (HTTP 409)
- Full validation on both frontend and backend
- Responsive design (mobile + desktop)

---

## Quick Start

### Prerequisites
- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.4+
- Node.js 18+ and npm

---

### Step 1 — Database Setup

```bash
mysql -u root -p < database/schema.sql
```

This creates the `appointment_db` database and all required tables.

---

### Step 2 — Configure Environment

```bash
cp .env.example .env
```

Edit `.env` with your actual database credentials:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=appointment_db
DB_USER=root
DB_PASS=your_password_here
JWT_SECRET=some-long-random-string-here
ALLOWED_ORIGINS=http://localhost:5173
```

---

### Step 3 — Start the Backend

```bash
cd backend
php -S localhost:8000 index.php
```

Backend will be available at: `http://localhost:8000`

---

### Step 4 — Start the Frontend

```bash
cd frontend
npm install
npm run dev
```

Frontend will be available at: `http://localhost:5173`

The Vite dev server proxies all `/api` requests to `http://localhost:8000` automatically.

---

### Step 5 — Open the App

Visit **http://localhost:5173** in your browser, register a new account, and start booking appointments.

---

## API Endpoints

All responses follow:
```json
{ "success": true, "data": {}, "message": "string" }
```

| Method | Endpoint                    | Auth? | Description              |
|--------|-----------------------------|-------|--------------------------|
| POST   | /api/auth/register          | No    | Register new user        |
| POST   | /api/auth/login             | No    | Login, returns JWT token |
| GET    | /api/user/profile           | Yes   | Get authenticated user   |
| GET    | /api/appointments           | Yes   | List user's appointments |
| POST   | /api/appointments           | Yes   | Book new appointment     |
| PUT    | /api/appointments/{id}      | Yes   | Update appointment       |
| DELETE | /api/appointments/{id}      | Yes   | Cancel appointment       |

---

## Environment Variables

| Variable          | Description                         | Default              |
|-------------------|-------------------------------------|----------------------|
| `DB_HOST`         | MySQL host                          | `localhost`          |
| `DB_PORT`         | MySQL port                          | `3306`               |
| `DB_NAME`         | Database name                       | `appointment_db`     |
| `DB_USER`         | Database user                       | `root`               |
| `DB_PASS`         | Database password                   | _(empty)_            |
| `JWT_SECRET`      | Secret key for JWT signing          | _(must set this)_    |
| `ALLOWED_ORIGINS` | Comma-separated CORS allowed origins | `http://localhost:5173` |

---

## Project Structure

```
appointment-booking-system/
├── backend/
│   ├── app/
│   │   ├── controllers/   AuthController, UserController, AppointmentController
│   │   ├── models/        UserModel, AppointmentModel
│   │   ├── middlewares/   AuthMiddleware (JWT guard)
│   │   └── utils/         JWT, Response, Validator
│   ├── config/            database.php, cors.php
│   ├── routes/            api.php (router)
│   ├── public/            .htaccess
│   ├── .htaccess
│   └── index.php          Entry point
├── frontend/
│   └── src/
│       ├── components/    NavBar, AppointmentCard
│       ├── pages/         Login, Register, Dashboard, BookAppointment, Profile
│       ├── services/      api.js, authService.js, appointmentService.js
│       ├── store/         auth.js (Pinia)
│       ├── router/        index.js
│       └── utils/         helpers.js
├── database/
│   └── schema.sql
├── .env.example
└── README.md
```

---

## Deployment

- **Frontend** → Vercel / Netlify (`npm run build` → deploy `dist/`)
- **Backend** → Any PHP host (Render, Railway, shared hosting)
- **Database** → MySQL on your host or PlanetScale

For production, set `VITE_API_BASE_URL=https://your-backend-url.com/api` in frontend `.env`.

---

## Developer

**Anirban Roy**  
GitHub: [github.com/anirban-roy-coder](https://github.com/anirban-roy-coder)  

