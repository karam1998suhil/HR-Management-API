# HR Management API — Documentation

## Table of Contents

1. [Overview](#overview)
2. [Authentication](#authentication)
3. [Rate Limiting](#rate-limiting)
4. [Error Handling](#error-handling)
5. [Endpoints](#endpoints)
   - [Auth](#auth-endpoints)
   - [Employees](#employee-endpoints)
   - [Positions](#position-endpoints)
6. [Email Notifications](#email-notifications)
7. [Broadcasting](#broadcasting)
8. [Logging](#logging)
9. [Artisan Commands](#artisan-commands)

---

## Overview

The HR Management API is a RESTful API built with Laravel 11. It manages employees, positions, salary changes, and managerial hierarchies within a company.

- **Base URL:** `http://127.0.0.1:8000/api/v1`
- **Format:** JSON
- **Authentication:** Laravel Sanctum (Bearer Token)
- **Rate Limit:** 10 requests per minute

---

## Authentication

All endpoints except `register` and `login` require a Bearer token.

Include this header in every request:

```
Authorization: Bearer YOUR_TOKEN
```

Tokens are issued on login and register. To invalidate a token, call the logout endpoint.

---

## Rate Limiting

Every authenticated route is limited to **10 requests per minute** per user.

When the limit is exceeded:

```json
HTTP 429 Too Many Requests

{
    "message": "Too many requests. Please slow down."
}
```

---

## Error Handling

| Status Code | Meaning |
|-------------|---------|
| 200 | Success |
| 201 | Created |
| 401 | Unauthenticated — missing or invalid token |
| 404 | Resource not found |
| 422 | Validation failed |
| 429 | Too many requests |
| 500 | Server error |

Validation error response format:

```json
{
    "message": "The name field is required.",
    "errors": {
        "name": ["The name field is required."],
        "email": ["The email has already been taken."]
    }
}
```

---

## Endpoints

---

## Auth Endpoints

---

### POST `/api/v1/register`

Register a new user account.

**Request Body:**

```json
{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Validation Rules:**
- `name` — required, string, max 255
- `email` — required, valid email, unique
- `password` — required, min 6 characters

**Success Response — 201:**

```json
{
    "user": {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com",
        "created_at": "2026-03-15T20:00:00.000000Z"
    },
    "token": "1|abc123..."
}
```

---

### POST `/api/v1/login`

Login with email and password.

**Request Body:**

```json
{
    "email": "test@example.com",
    "password": "password123"
}
```

**Success Response — 200:**

```json
{
    "user": {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com"
    },
    "token": "2|xyz789..."
}
```

**Failed Response — 401:**

```json
{
    "message": "Invalid credentials"
}
```

---

### POST `/api/v1/logout`

Logout and revoke the current token.

**Headers:** `Authorization: Bearer YOUR_TOKEN`

**Success Response — 200:**

```json
{
    "message": "Logged out successfully"
}
```

---

## Employee Endpoints

---

### GET `/api/v1/employees`

Returns a paginated list of all employees with their manager.

**Headers:** `Authorization: Bearer YOUR_TOKEN`

**Success Response — 200:**

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "name": "John Founder",
            "email": "founder@company.com",
            "salary": 10000.00,
            "is_founder": true,
            "position_id": null,
            "manager_id": null,
            "last_salary_changed_at": null,
            "created_at": "2026-03-15T20:00:00.000000Z",
            "manager": null,
            "position": null
        }
    ],
    "per_page": 15,
    "total": 1
}
```

---

### POST `/api/v1/employees`

Create a new employee.

**Headers:** `Authorization: Bearer YOUR_TOKEN`

**Request Body — Creating a Founder:**

```json
{
    "name": "John Founder",
    "email": "founder@company.com",
    "salary": 10000,
    "is_founder": true
}
```

**Request Body — Creating a Regular Employee:**

```json
{
    "name": "Jane Manager",
    "email": "jane@company.com",
    "salary": 7000,
    "manager_id": 1
}
```

**Validation Rules:**
- `name` — required, string, max 255
- `email` — required, valid email, unique
- `salary` — required, numeric, min 0
- `manager_id` — required unless `is_founder` is true, must exist in employees table
- `is_founder` — optional, boolean

**Success Response — 201:**

```json
{
    "id": 2,
    "name": "Jane Manager",
    "email": "jane@company.com",
    "salary": 7000.00,
    "is_founder": false,
    "manager_id": 1,
    "manager": {
        "id": 1,
        "name": "John Founder"
    }
}
```

**Error — Second Founder — 422:**

```json
{
    "message": "A founder already exists."
}
```

> **Note:** When an employee is created, an email notification is sent to their manager.

---

### GET `/api/v1/employees/{id}`

Get a single employee by ID.

**Headers:** `Authorization: Bearer YOUR_TOKEN`

**Success Response — 200:**

```json
{
    "id": 2,
    "name": "Jane Manager",
    "email": "jane@company.com",
    "salary": 7000.00,
    "is_founder": false,
    "manager": {
        "id": 1,
        "name": "John Founder"
    },
    "position": {
        "id": 1,
        "title": "Software Engineer"
    }
}
```

**Not Found — 404:**

```json
{
    "message": "No query results for model [App\\Models\\Employee] 999"
}
```

---

### PUT `/api/v1/employees/{id}`

Update an employee. Only include fields you want to change.

**Headers:** `Authorization: Bearer YOUR_TOKEN`

**Request Body:**

```json
{
    "name": "Jane Updated",
    "salary": 9000,
    "position_id": 1
}
```

**Validation Rules:**
- `name` — optional, string, max 255
- `email` — optional, valid email, unique (ignores current employee)
- `salary` — optional, numeric, min 0
- `manager_id` — optional, must exist in employees table
- `position_id` — optional, must exist in positions table

**Success Response — 200:**

```json
{
    "id": 2,
    "name": "Jane Updated",
    "salary": 9000.00,
    "last_salary_changed_at": "2026-03-15T20:00:00+00:00"
}
```

> **Note:** If salary changes, the employee and all managers up to the founder are notified by email and real-time broadcast.

---

### DELETE `/api/v1/employees/{id}`

Soft delete an employee. The record stays in the database with `deleted_at` set.

**Headers:** `Authorization: Bearer YOUR_TOKEN`

**Success Response — 200:**

```json
{
    "message": "Employee deleted successfully."
}
```

---

### GET `/api/v1/employees/search`

Search employees by name or salary. If no parameters are given, returns all employees.

**Headers:** `Authorization: Bearer YOUR_TOKEN`

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | string | Partial name match |
| `salary` | number | Exact salary match |

**Examples:**

```
GET /api/v1/employees/search?name=jane
GET /api/v1/employees/search?salary=7000
GET /api/v1/employees/search?name=jane&salary=7000
GET /api/v1/employees/search
```

**Success Response — 200:**

```json
{
    "count": 1,
    "employees": [
        {
            "id": 2,
            "name": "Jane Manager",
            "salary": 7000.00
        }
    ]
}
```

---

### GET `/api/v1/employees/{id}/hierarchy`

Returns the full manager chain from the founder down to the given employee.

**Headers:** `Authorization: Bearer YOUR_TOKEN`

**Success Response — 200:**

```json
{
    "hierarchy": [
        "John Founder",
        "Jane Manager",
        "Bob Employee"
    ]
}
```

---

### GET `/api/v1/employees/{id}/hierarchy-with-salary`

Same as hierarchy but includes salary for each person.

**Headers:** `Authorization: Bearer YOUR_TOKEN`

**Success Response — 200:**

```json
{
    "hierarchy": {
        "John Founder": 10000.00,
        "Jane Manager": 7000.00,
        "Bob Employee": 4000.00
    }
}
```

---

### GET `/api/v1/employees/{id}/logs`

Returns all activity logs for a specific employee.

**Headers:** `Authorization: Bearer YOUR_TOKEN`

**Success Response — 200:**

```json
{
    "employee": "Jane Manager",
    "count": 2,
    "logs": [
        {
            "id": 2,
            "employee_id": 2,
            "action": "updated",
            "description": "Employee Jane Manager was updated.",
            "meta": {
                "before": {
                    "salary": "7000.00"
                },
                "after": {
                    "salary": "9000.00"
                }
            },
            "logged_at": "2026-03-15T20:10:00.000000Z"
        },
        {
            "id": 1,
            "employee_id": 2,
            "action": "created",
            "description": "Employee Jane Manager was created.",
            "meta": {
                "name": "Jane Manager",
                "email": "jane@company.com",
                "salary": "7000.00"
            },
            "logged_at": "2026-03-15T20:00:00.000000Z"
        }
    ]
}
```

**Log Actions:**

| Action | When |
|--------|------|
| `created` | Employee was created |
| `updated` | Employee was updated |
| `deleted` | Employee was soft deleted |
| `imported` | Employee was imported from CSV |
| `exported` | Employee data was exported |

---

### GET `/api/v1/employees/export/csv`

Download all employee data as a CSV file.

**Headers:** `Authorization: Bearer YOUR_TOKEN`

**Response:** CSV file download

**CSV Columns:**

```
ID, Name, Email, Salary, Manager, Is Founder, Created At
```

> **Postman tip:** Use **Send and Download** instead of Send to save the file.

---

### POST `/api/v1/employees/import/csv`

Import employees from a CSV file.

**Headers:** `Authorization: Bearer YOUR_TOKEN`

**Body:** `form-data`

| Key | Type | Value |
|-----|------|-------|
| file | File | your CSV file |

**CSV Format:**

```csv
name,email,salary,manager_id,is_founder
"John Founder",founder@company.com,10000,,1
"Jane Manager",jane@company.com,7000,1,0
```

**Success Response — 200:**

```json
{
    "message": "Import completed.",
    "imported": 6,
    "skipped": 1,
    "errors": [
        "Row 1: Founder already exists, skipped."
    ]
}
```

---

### GET `/api/v1/employees/no-salary-change/{months}`

Returns employees who haven't had a salary change in the last X months.

**Headers:** `Authorization: Bearer YOUR_TOKEN`

**URL Parameter:**
- `months` — integer, number of months to check

**Example:**

```
GET /api/v1/employees/no-salary-change/3
GET /api/v1/employees/no-salary-change/6
```

**Success Response — 200:**

```json
{
    "months": 3,
    "cutoff": "2025-12-15",
    "count": 5,
    "employees": [
        {
            "id": 1,
            "name": "John Founder",
            "salary": 10000.00,
            "last_salary_changed_at": null
        }
    ]
}
```

---

## Position Endpoints

---

### GET `/api/v1/positions`

Returns all positions with employee count.

**Success Response — 200:**

```json
[
    {
        "id": 1,
        "title": "Software Engineer",
        "description": "Develops software applications",
        "employees_count": 3,
        "created_at": "2026-03-15T20:00:00.000000Z"
    }
]
```

---

### POST `/api/v1/positions`

Create a new position.

**Request Body:**

```json
{
    "title": "Software Engineer",
    "description": "Develops and maintains software applications"
}
```

**Validation Rules:**
- `title` — required, string, max 255, unique
- `description` — optional, string

**Success Response — 201:**

```json
{
    "id": 1,
    "title": "Software Engineer",
    "description": "Develops and maintains software applications",
    "created_at": "2026-03-15T20:00:00.000000Z"
}
```

---

### GET `/api/v1/positions/{id}`

Get a single position with its employees.

**Success Response — 200:**

```json
{
    "id": 1,
    "title": "Software Engineer",
    "description": "Develops software",
    "employees": [
        {
            "id": 2,
            "name": "Jane Manager",
            "email": "jane@company.com"
        }
    ]
}
```

---

### PUT `/api/v1/positions/{id}`

Update a position.

**Request Body:**

```json
{
    "title": "Senior Software Engineer",
    "description": "Leads software development"
}
```

**Success Response — 200:**

```json
{
    "id": 1,
    "title": "Senior Software Engineer",
    "description": "Leads software development"
}
```

---

### DELETE `/api/v1/positions/{id}`

Delete a position. Employees with this position will have `position_id` set to null.

**Success Response — 200:**

```json
{
    "message": "Position deleted successfully."
}
```

---

## Email Notifications

| Trigger | Recipient | Email Content |
|---------|-----------|---------------|
| Employee created | Manager | New team member details |
| Salary changed | Employee | Old and new salary |
| Salary changed | All managers up to founder | Employee name and salary change |

Emails are queued and sent asynchronously. In development set `MAIL_MAILER=log` to see emails in `storage/logs/laravel.log`.

---

## Broadcasting

When a salary changes, a real-time event is broadcast on:

```
Channel: salary-changes
Event:   salary.changed
```

**Payload:**

```json
{
    "employee_id": 2,
    "employee_name": "Jane Manager",
    "old_salary": 7000,
    "new_salary": 9000,
    "changed_at": "2026-03-15T20:00:00+00:00"
}
```

---

## Logging

### Database Logs

Every operation on employee data is saved to the `employee_logs` table:

| Column | Description |
|--------|-------------|
| `employee_id` | Which employee was affected |
| `action` | created, updated, deleted, imported, exported |
| `description` | Human readable description |
| `meta` | Before/after snapshot as JSON |
| `logged_at` | When it happened |

### File Logs

Storage operations (export, import, delete) are written to:

```
storage/logs/employee.log
```

Example entries:

```
[2026-03-15 20:00:00] local.INFO: EXPORTED [bulk] — CSV file downloaded
[2026-03-15 20:01:00] local.INFO: IMPORTED [bulk] — 7 records imported, 0 skipped
[2026-03-15 20:02:00] local.INFO: DELETED [#2 Jane Manager]
```

---

## Artisan Commands

```bash
# Delete employee logs older than 1 month
php artisan logs:delete-old

# Remove all log files from storage/logs
php artisan logs:remove-files

# Insert fake employees with a progress bar
php artisan employees:insert 50

# Export the full database to a SQL file
php artisan db:export-sql

# Export all employees to a JSON file
php artisan employees:export-json
```
