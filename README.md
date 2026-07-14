# CRM System

A lightweight Customer Relationship Management (CRM) application for managing contacts, companies, leads, deals, activities, and notes — with a sales pipeline, dashboard, and role-based access control.

Built with **Laravel 8**, **Inertia.js**, **Vue 3**, and **Bootstrap 5**.

---

## Features

- **Contacts & Companies** — full CRUD with relationships (a company has many contacts)
- **Leads** — track leads through statuses (new, contacted, qualified, lost, converted) and convert a lead into a contact
- **Deals & Pipeline** — a drag-and-drop kanban board to move deals across stages (new → qualified → proposal → won/lost)
- **Activities / Tasks** — calls, meetings, emails, and tasks with due dates and completion tracking
- **Notes** — attach notes to contacts, companies, and deals
- **Dashboard** — key metrics, a "deals by stage" chart, and recent activity
- **Search & filters** — debounced search and filtering on every list page
- **Roles & permissions** — Admin, Manager, and Sales Rep roles; reps only see their own records, admins/managers see everything
- **User management** — admin-only screen to manage users

---

## Tech Stack

| Layer      | Technology                      |
|------------|---------------------------------|
| Backend    | Laravel 8, PHP 7.4              |
| Frontend   | Inertia.js, Vue 3, Bootstrap 5  |
| Build tool | Laravel Mix (webpack)           |
| Database   | MySQL / MariaDB                 |
| Auth       | Laravel Breeze + Sanctum        |

---

## Requirements

- PHP 7.4+
- Composer
- Node.js 20+ & npm
- MySQL / MariaDB

---

## Installation

1. **Clone the repository**
   ```bash
   git clone <your-repo-url>
   cd CRM---System
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Set up your environment file**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure the database** in `.env`
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3307
   DB_DATABASE=laravel
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Run migrations and seed demo data**
   ```bash
   php artisan migrate --seed
   ```

7. **Build front-end assets**
   ```bash
   npm run dev
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

   The app will be available at **http://127.0.0.1:8000**

---

## Default Login

After seeding, you can log in with:

| Role       | Email            | Password    |
|------------|------------------|-------------|
| Admin      | admin@crm.test   | password123 |
| Sales Rep  | rep@crm.test     | password123 |

---

## Development

- Rebuild assets after editing Vue/JS/SCSS files:
  ```bash
  npm run dev      # one-off build
  npm run watch    # rebuild automatically on change
  ```
- PHP changes do not require a rebuild.

---

## Project Structure

```
app/
├── Http/Controllers/   # Thin controllers (validation + authorization)
├── Models/             # Eloquent models
└── Repositories/       # Query & persistence logic

resources/
├── js/
│   ├── Pages/          # Vue page components (Contacts, Companies, Leads, Deals...)
│   ├── Components/      # Shared Vue components (Pagination, NotesPanel...)
│   └── Layouts/        # App layouts
└── sass/               # Bootstrap styles

routes/
└── web.php             # Application routes
```

---

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
