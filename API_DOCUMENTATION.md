# Fundlink API Documentation

This API is designed for mobile application integration to manage financial transactions and user data.

## Authentication

All API requests require authentication using Laravel Sanctum tokens.

### Login

**POST** `/api/login`

Request Body:
```json
{
  "email": "user@example.com",
  "password": "password",
  "device_name": "Mobile App"
}
```

Response:
```json
{
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "User Name",
    "email": "user@example.com",
    "unit": {...}
  }
}
```

### Logout

**POST** `/api/logout`

Headers: `Authorization: Bearer {token}`

## Endpoints

### Dashboard

**GET** `/api/dashboard`

Returns balance, total income, total expenses, and unit info.

### Transactions

**GET** `/api/transactions`

Returns paginated list of transactions.

**POST** `/api/transactions`

Create a new transaction.

Request Body:
```json
{
  "type": "pemasukan|pengeluaran",
  "amount": 1000.00,
  "category": "Category",
  "description": "Description",
  "transaction_date": "2023-01-01"
}
```

### User Profile

**GET** `/api/user`

Returns current user information.

### Notifications

**GET** `/api/notifications`

Returns paginated list of user notifications.

## Rate Limiting

API requests are limited to 60 per minute.

## CORS

CORS is enabled for all origins to support mobile applications.
