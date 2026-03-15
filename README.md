# HR Management API

A RESTful API built with Laravel for managing employees, positions, salary changes, and managerial hierarchies.

---

## Requirements

- PHP 8.2+
- Composer
- MySQL
- Laravel 11

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/your-username/hr-management-api.git
cd hr-management-api
```

### 2. Install dependencies

```bash
composer install
```

### 3. Copy environment file

```bash
cp .env.example .env
```

### 4. Generate application key

```bash
php artisan key:generate
```

### 5. Configure your database

Open `.env` and update these values:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_hr_management
DB_USERNAME=root
DB_PASSWORD=
```

Create the database in phpMyAdmin or via terminal:

```sql
CREATE DATABASE db_hr_management;
```

### 6. Run migrations

```bash
php artisan migrate
```

### 7. Configure mail (for email notifications)

For local development, emails are logged to `storage/logs/laravel.log`:

```env
MAIL_MAILER=log
```

For production, update with your real SMTP credentials:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=hr@company.com
MAIL_FROM_NAME="HR Management"
```

### 8. Configure broadcasting (for real-time salary notifications)

For local development:

```env
BROADCAST_DRIVER=log
```

For production with Pusher:

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

### 9. Seed fake data (optional)

```bash
php artisan employees:insert 20
```

### 10. Start the server

```bash
php artisan serve
```

The API will be available at `http://127.0.0.1:8000`

---

## Running Tests

```bash
php artisan test
```

---

## API Endpoints

All protected endpoints require:
```
Authorization: Bearer YOUR_TOKEN
```

### Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/register` | Register a new user |
| POST | `/api/v1/login` | Login and get token |
| POST | `/api/v1/logout` | Logout (revoke token) |

### Employees

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/employees` | List all employees (paginated) |
| POST | `/api/v1/employees` | Create a new employee |
| GET | `/api/v1/employees/{id}` | Get a single employee |
| PUT | `/api/v1/employees/{id}` | Update an employee |
| DELETE | `/api/v1/employees/{id}` | Soft delete an employee |
| GET | `/api/v1/employees/search?name=&salary=` | Search employees |
| GET | `/api/v1/employees/{id}/hierarchy` | Get manager hierarchy (names) |
| GET | `/api/v1/employees/{id}/hierarchy-with-salary` | Get manager hierarchy (names + salaries) |
| GET | `/api/v1/employees/{id}/logs` | Get employee activity logs |
| GET | `/api/v1/employees/export/csv` | Export all employees to CSV |
| POST | `/api/v1/employees/import/csv` | Import employees from CSV |
| GET | `/api/v1/employees/no-salary-change/{months}` | Employees without salary change in X months |

### Positions

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/positions` | List all positions |
| POST | `/api/v1/positions` | Create a position |
| GET | `/api/v1/positions/{id}` | Get a single position |
| PUT | `/api/v1/positions/{id}` | Update a position |
| DELETE | `/api/v1/positions/{id}` | Delete a position |

---

## Artisan Commands

| Command | Description |
|---------|-------------|
| `php artisan logs:delete-old` | Delete employee logs older than 1 month |
| `php artisan logs:remove-files` | Remove all log files from storage/logs |
| `php artisan employees:insert {count}` | Insert fake employees with progress bar |
| `php artisan db:export-sql` | Export entire database to a SQL file |
| `php artisan employees:export-json` | Export all employees to a JSON file |

---

## Key Features

- **Token-based authentication** using Laravel Sanctum
- **Single founder rule** — only one founder allowed per company
- **Manager hierarchy** — every employee (except founder) must have a manager
- **Email notifications** — manager notified when a new employee is added to their team
- **Salary change notifications** — employee and all managers up to founder are notified by email and real-time broadcast
- **CSV import/export** — bulk import and export employee data
- **Database logging** — every operation on employee data is saved to the `employee_logs` table
- **File logging** — storage operations are saved to `storage/logs/employee.log`
- **Rate limiting** — maximum 10 requests per minute per user
- **Soft deletes** — deleted employees are not permanently removed

---

## Project Structure

```
app/
├── Console/Commands/       # Artisan commands
├── Events/                 # SalaryChanged event
├── Http/
│   ├── Controllers/Api/V1/ # API controllers
│   └── Requests/           # Form request validation
├── Listeners/              # Event listeners
├── Mail/                   # Mailables
├── Models/                 # Eloquent models
├── Providers/              # AppServiceProvider
└── Services/               # Business logic (optional)

database/
├── migrations/             # Database migrations

resources/views/emails/     # Email blade templates

routes/
└── api.php                 # API routes

storage/logs/
├── laravel.log             # Application logs
└── employee.log            # Employee storage logs

tests/Feature/              # Feature tests
```

---

## Database Structure

```
users
employees         (self-referencing: manager_id → employees.id)
positions
employee_logs
salary_histories
```

---

## Rate Limiting

All API endpoints are limited to **10 requests per minute** per authenticated user.
Exceeding the limit returns:

```json
{
    "message": "Too many requests. Please slow down."
}
```

---

## License

MIT