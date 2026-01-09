# API Documentation

**Prompt 498: Create API Documentation**

Smart School Management System API v1

## Overview

The Smart School API provides programmatic access to school management features including students, attendance, fees, exams, and more.

**Base URL:** `https://your-domain.com/api/v1`

**API Version:** v1

## Authentication

The API uses Bearer token authentication via Laravel Sanctum.

### Obtaining a Token

```http
POST /api/tokens
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "your-password",
    "device_name": "My App"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Token created successfully",
    "data": {
        "token": "1|abc123...",
        "token_type": "Bearer",
        "abilities": ["read", "write"]
    }
}
```

### Using the Token

Include the token in the Authorization header:

```http
Authorization: Bearer 1|abc123...
```

### Revoking Tokens

```http
DELETE /api/tokens/{token_id}
Authorization: Bearer {token}
```

## Rate Limiting

API requests are rate limited based on user type:

| User Type | Requests/Minute |
|-----------|-----------------|
| Guest | 60 |
| Authenticated | 120 |
| Admin | 300 |

Rate limit headers are included in responses:
- `X-RateLimit-Limit`: Maximum requests allowed
- `X-RateLimit-Remaining`: Remaining requests
- `X-RateLimit-Reset`: Unix timestamp when limit resets

## Response Format

All responses follow a consistent format:

### Success Response
```json
{
    "success": true,
    "message": "Success",
    "data": { ... },
    "api_version": "v1"
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "errors": { ... },
    "api_version": "v1"
}
```

### Paginated Response
```json
{
    "success": true,
    "message": "Success",
    "data": [ ... ],
    "pagination": {
        "current_page": 1,
        "last_page": 10,
        "per_page": 15,
        "total": 150,
        "from": 1,
        "to": 15
    },
    "api_version": "v1"
}
```

## Endpoints

### Health Check

#### Basic Health
```http
GET /api/health
```

**Response:**
```json
{
    "status": "ok",
    "timestamp": "2026-01-09T12:00:00Z",
    "version": "1.0.0"
}
```

#### Detailed Health
```http
GET /api/health/detailed
```

### Dashboard

#### Get Dashboard Statistics
```http
GET /api/v1/dashboard/stats
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "total_students": 500,
        "total_teachers": 50,
        "total_classes": 20,
        "attendance_today": 450
    }
}
```

### Students

#### List Students
```http
GET /api/v1/students
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 15, max: 100)
- `class_id` - Filter by class
- `section_id` - Filter by section
- `search` - Search by name or admission number

#### Get Student
```http
GET /api/v1/students/{id}
Authorization: Bearer {token}
```

#### Create Student
```http
POST /api/v1/students
Authorization: Bearer {token}
Content-Type: application/json

{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "class_id": 1,
    "section_id": 1,
    ...
}
```

#### Update Student
```http
PUT /api/v1/students/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "first_name": "John Updated",
    ...
}
```

#### Delete Student
```http
DELETE /api/v1/students/{id}
Authorization: Bearer {token}
```

### Attendance

#### Get Attendance
```http
GET /api/v1/attendance
Authorization: Bearer {token}
```

**Query Parameters:**
- `date` - Date (YYYY-MM-DD)
- `class_id` - Filter by class
- `section_id` - Filter by section

#### Mark Attendance
```http
POST /api/v1/attendance
Authorization: Bearer {token}
Content-Type: application/json

{
    "date": "2026-01-09",
    "class_id": 1,
    "section_id": 1,
    "attendance": [
        {"student_id": 1, "status": "present"},
        {"student_id": 2, "status": "absent"}
    ]
}
```

### Fees

#### Get Fee Structure
```http
GET /api/v1/fees/structure
Authorization: Bearer {token}
```

#### Get Student Fees
```http
GET /api/v1/fees/student/{student_id}
Authorization: Bearer {token}
```

#### Record Payment
```http
POST /api/v1/fees/payment
Authorization: Bearer {token}
Content-Type: application/json

{
    "student_id": 1,
    "amount": 5000,
    "payment_method": "cash",
    "fee_type_id": 1
}
```

### Exams

#### List Exams
```http
GET /api/v1/exams
Authorization: Bearer {token}
```

#### Get Exam Results
```http
GET /api/v1/exams/{exam_id}/results
Authorization: Bearer {token}
```

#### Submit Marks
```http
POST /api/v1/exams/{exam_id}/marks
Authorization: Bearer {token}
Content-Type: application/json

{
    "marks": [
        {"student_id": 1, "subject_id": 1, "marks": 85},
        {"student_id": 2, "subject_id": 1, "marks": 90}
    ]
}
```

### Reports

#### Generate Report
```http
GET /api/v1/reports/{type}
Authorization: Bearer {token}
```

**Report Types:**
- `attendance` - Attendance report
- `fees` - Fees collection report
- `exam` - Exam results report
- `student` - Student list report

**Query Parameters:**
- `format` - Output format (json, pdf, excel)
- `from_date` - Start date
- `to_date` - End date

### Translations

#### Get Translations
```http
GET /api/v1/translations
Authorization: Bearer {token}
```

**Query Parameters:**
- `locale` - Language code (en, ar, etc.)
- `group` - Translation group

## Error Codes

| Code | Description |
|------|-------------|
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Too Many Requests |
| 500 | Internal Server Error |

## Webhooks

The API supports webhooks for real-time notifications.

### Available Events
- `student.created`
- `student.updated`
- `attendance.marked`
- `fee.paid`
- `exam.completed`

### Webhook Payload
```json
{
    "event": "student.created",
    "timestamp": "2026-01-09T12:00:00Z",
    "data": { ... }
}
```

## SDK Examples

### PHP
```php
$client = new SmartSchoolClient('your-api-token');
$students = $client->students()->list(['class_id' => 1]);
```

### JavaScript
```javascript
const client = new SmartSchoolClient('your-api-token');
const students = await client.students.list({ classId: 1 });
```

### Python
```python
client = SmartSchoolClient('your-api-token')
students = client.students.list(class_id=1)
```

## Support

For API support, contact: api-support@smartschool.com
