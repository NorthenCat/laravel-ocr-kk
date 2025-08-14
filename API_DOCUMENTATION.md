# Laravel OCR KK API Documentation

This API provides endpoints for managing Desa, RW, KK, and Anggota data for the Laravel OCR KK application.

## Base URL
```
http://your-domain.com/api
```

## Authentication

This API uses Laravel Sanctum for authentication. You need to obtain an API token to access protected endpoints.

### Login
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "User Name",
            "email": "user@example.com"
        },
        "token": "your-api-token",
        "token_type": "Bearer"
    }
}
```

### Register
```http
POST /api/auth/register
Content-Type: application/json

{
    "name": "User Name",
    "email": "user@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

### Get User Info
```http
GET /api/auth/user
Authorization: Bearer your-api-token
```

### Logout
```http
POST /api/auth/logout
Authorization: Bearer your-api-token
```

## API Endpoints

All protected endpoints require the `Authorization: Bearer your-api-token` header.

### Dashboard
- `GET /api/dashboard` - Get dashboard statistics

### Desa Management
- `GET /api/desa` - List all desas
- `POST /api/desa` - Create new desa
- `GET /api/desa/{id}` - Get specific desa
- `PUT /api/desa/{id}` - Update desa
- `DELETE /api/desa/{id}` - Delete desa
- `POST /api/desa/{id}/users` - Add user to desa
- `DELETE /api/desa/{id}/users/{userId}` - Remove user from desa

### RW Management
- `GET /api/desa/{desa}/rw` - List RWs in a desa
- `POST /api/desa/{desa}/rw` - Create new RW
- `GET /api/desa/{desa}/rw/{rw}` - Get specific RW
- `PUT /api/desa/{desa}/rw/{rw}` - Update RW
- `DELETE /api/desa/{desa}/rw/{rw}` - Delete RW
- `GET /api/desa/{desa}/rw/{rw}/export-excel` - Export RW data to Excel
- `GET /api/desa/{desa}/rw/{rw}/export-excel-no-filename` - Export RW data to Excel (no filename)

### KK Management
- `GET /api/desa/{desa}/rw/{rw}/kk` - List KKs in an RW
- `POST /api/desa/{desa}/rw/{rw}/kk` - Create new KK
- `GET /api/desa/{desa}/rw/{rw}/kk/{kk}` - Get specific KK
- `PUT /api/desa/{desa}/rw/{rw}/kk/{kk}` - Update KK
- `DELETE /api/desa/{desa}/rw/{rw}/kk/{kk}` - Delete KK
- `GET /api/desa/{desa}/rw/{rw}/kk/upload` - Get upload information
- `POST /api/desa/{desa}/rw/{rw}/kk/upload` - Upload KK file

### Anggota Management
- `GET /api/desa/{desa}/rw/{rw}/kk/{kk}/anggota` - List anggota in a KK
- `POST /api/desa/{desa}/rw/{rw}/kk/{kk}/anggota` - Create new anggota
- `GET /api/desa/{desa}/rw/{rw}/kk/{kk}/anggota/{anggota}` - Get specific anggota
- `PUT /api/desa/{desa}/rw/{rw}/kk/{kk}/anggota/{anggota}` - Update anggota
- `DELETE /api/desa/{desa}/rw/{rw}/kk/{kk}/anggota/{anggota}` - Delete anggota

### Standalone Anggota
- `GET /api/desa/{desa}/rw/{rw}/standalone/{anggota}` - Get standalone anggota
- `PUT /api/desa/{desa}/rw/{rw}/standalone/{anggota}` - Update standalone anggota
- `DELETE /api/desa/{desa}/rw/{rw}/standalone/{anggota}` - Delete standalone anggota

### Failed Files
- `GET /api/desa/{desa}/rw/{rw}/failed-files/{file}` - Get failed file details
- `PATCH /api/desa/{desa}/rw/{rw}/failed-files/{file}/mark-processed` - Mark file as processed
- `DELETE /api/desa/{desa}/rw/{rw}/failed-files/{file}` - Delete failed file

### Settings
- `GET /api/settings` - Get application settings
- `POST /api/settings` - Update application settings

## Request/Response Format

### Standard Success Response
```json
{
    "success": true,
    "message": "Operation successful",
    "data": {
        // Response data
    }
}
```

### Standard Error Response
```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        // Validation errors (if applicable)
    }
}
```

### Validation Error Example
```json
{
    "success": false,
    "message": "Validation error",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

## Example Requests

### Create Desa
```http
POST /api/desa
Authorization: Bearer your-api-token
Content-Type: application/json

{
    "nama_desa": "Desa Sukamaju",
    "google_drive": "https://drive.google.com/folder/example"
}
```

### Create RW
```http
POST /api/desa/1/rw
Authorization: Bearer your-api-token
Content-Type: application/json

{
    "nama_rw": "RW 01",
    "google_drive": "https://drive.google.com/folder/example-rw"
}
```

### Create KK
```http
POST /api/desa/1/rw/1/kk
Authorization: Bearer your-api-token
Content-Type: application/json

{
    "no_kk": "1234567890123456",
    "nama_kepala_keluarga": "John Doe"
}
```

### Create Anggota
```http
POST /api/desa/1/rw/1/kk/1/anggota
Authorization: Bearer your-api-token
Content-Type: application/json

{
    "nama_lengkap": "Jane Doe",
    "nik": "3201234567890123",
    "jenis_kelamin": "PEREMPUAN",
    "tempat_lahir": "Jakarta",
    "tanggal_lahir": "1990-01-01",
    "agama": "Islam",
    "pendidikan": "S1",
    "jenis_pekerjaan": "Karyawan Swasta",
    "status_perkawinan": "Kawin",
    "status_hubungan_dalam_keluarga": "Istri",
    "kewarganegaraan": "WNI"
}
```

## HTTP Status Codes

- `200` - OK (successful GET, PUT, DELETE)
- `201` - Created (successful POST)
- `400` - Bad Request
- `401` - Unauthorized (invalid or missing token)
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found
- `409` - Conflict (duplicate data)
- `422` - Unprocessable Entity (validation error)
- `500` - Internal Server Error

## Testing the API

You can test the API using tools like:
- Postman
- Insomnia
- curl
- Your desktop application

### Example curl request:
```bash
curl -X POST http://your-domain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'
```

## Notes

- All timestamps are in ISO 8601 format
- File uploads support JSON and ZIP formats with max size of 50MB
- The API uses nested resource routing to maintain data relationships
- Role-based access control is implemented using Spatie Laravel Permission package
- CORS is handled by Laravel Sanctum for frontend requests
