# Fundlink API Documentation

**Version**: 2.0  
**Base URL Production**: `https://bahamud.my.id/api`  
**Base URL Development**: `http://127.0.0.1:8000/api`  
**Auth**: Laravel Sanctum (Bearer Token)

---

## Konvensi Response

Semua endpoint mengembalikan format JSON yang konsisten:

```json
// Sukses
{
  "success": true,
  "message": "Pesan sukses",
  "data": { ... }
}

// Error
{
  "success": false,
  "message": "Pesan error",
  "errors": { ... }   // hanya ada jika validasi gagal (422)
}
```

### Pagination Format
Endpoint yang mengembalikan list menggunakan format:
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "data": [ ... ],
    "pagination": {
      "current_page": 1,
      "last_page": 5,
      "per_page": 15,
      "total": 72,
      "has_more": true
    }
  }
}
```

---

## HTTP Status Codes

| Code | Keterangan |
|------|-----------|
| 200  | Sukses |
| 201  | Data berhasil dibuat |
| 401  | Token tidak valid / belum login |
| 403  | Tidak punya izin |
| 404  | Data tidak ditemukan |
| 422  | Validasi gagal |
| 429  | Rate limit (60 req/menit) |
| 500  | Server error |

---

## Rate Limiting

- **60 request per menit** per user (authenticated)
- Header response: `X-RateLimit-Limit`, `X-RateLimit-Remaining`
- Jika melebihi: HTTP 429

---

## 1. Authentication

### POST `/login`
Login dan dapatkan token.

**Request:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "device_name": "iPhone 15 Pro"   // opsional, untuk manajemen token
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "token": "1|abc123xyz...",
    "user": {
      "id": 1,
      "name": "Ahmad Bahaudin",
      "email": "ahmad@example.com",
      "role": "user",
      "unit_id": 2,
      "unit": { "id": 2, "name": "Unit Bogor" },
      "profile_photo_url": "https://bahamud.my.id/storage/profile-photos/abc.jpg",
      "email_verified_at": "2025-05-01T10:00:00.000000Z",
      "created_at": "2025-04-01T08:00:00.000000Z"
    }
  }
}
```

**Response 401:**
```json
{
  "success": false,
  "message": "Email atau password salah."
}
```

---

### POST `/register`
Daftar akun baru.

**Request:**
```json
{
  "name": "Nama Lengkap",
  "email": "email@example.com",
  "password": "password123",
  "device_name": "Flutter App"
}
```

**Response 201:** sama seperti login.

---

### POST `/logout` 🔒
Hapus token saat ini.

**Response 200:**
```json
{ "success": true, "message": "Logout berhasil.", "data": null }
```

---

### POST `/logout-all` 🔒
Hapus semua token (logout dari semua device).

---

## 2. User

### GET `/user` 🔒
Ambil data user yang sedang login.

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 1,
    "name": "Ahmad Bahaudin",
    "email": "ahmad@example.com",
    "role": "user",
    "unit_id": 2,
    "unit": { "id": 2, "name": "Unit Bogor" },
    "profile_photo_url": null,
    "email_verified_at": "2025-05-01T10:00:00.000000Z",
    "created_at": "2025-04-01T08:00:00.000000Z"
  }
}
```

---

### POST `/user/profile` 🔒
Update profil. Gunakan `multipart/form-data` jika upload foto.

**Fields:**
| Field | Type | Required |
|-------|------|----------|
| name  | string | ✅ |
| email | string | ✅ |
| photo | file (jpg/png/webp, max 2MB) | ❌ |

---

### POST `/user/password` 🔒
Ganti password.

**Request:**
```json
{
  "current_password": "password_lama",
  "password": "password_baru",
  "password_confirmation": "password_baru"
}
```

---

## 3. Dashboard

### GET `/dashboard` 🔒
Ringkasan keuangan. Non-admin hanya melihat data unit sendiri.

**Query Parameters (opsional):**
| Param | Value | Keterangan |
|-------|-------|-----------|
| period | `weekly` \| `monthly` \| `yearly` \| `custom` | Filter periode |
| year | `2025` | Tahun (untuk monthly/yearly) |
| month | `5` | Bulan 1-12 (untuk monthly) |
| date_from | `2025-01-01` | Tanggal mulai (untuk custom) |
| date_to | `2025-05-31` | Tanggal akhir (untuk custom) |

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "saldo": 1500000.00,
    "total_pemasukan": 2000000.00,
    "total_pengeluaran": 500000.00,
    "recent_transactions": [
      {
        "id": 10,
        "type": "pemasukan",
        "amount": 500000.00,
        "category": "Donasi",
        "description": "Donasi bulan Mei",
        "transaction_date": "2025-05-15",
        "attachment_url": null,
        "unit": { "id": 2, "name": "Unit Bogor" },
        "recorded_by": { "id": 1, "name": "Ahmad" },
        "created_at": "2025-05-15T09:00:00.000000Z"
      }
    ],
    "unit": { "id": 2, "name": "Unit Bogor" }
  }
}
```

---

## 4. Transactions

### GET `/transactions` 🔒
Daftar transaksi dengan filter dan pagination.

**Query Parameters:**
| Param | Value | Keterangan |
|-------|-------|-----------|
| page | `1` | Halaman |
| per_page | `15` (max 50) | Item per halaman |
| type | `pemasukan` \| `pengeluaran` | Filter tipe |
| category | `Donasi` | Filter kategori |
| search | `donasi` | Cari di kategori/keterangan |
| unit_id | `2` | Filter unit (admin only) |
| period | `weekly` \| `monthly` \| `yearly` \| `custom` | Filter periode |
| year | `2025` | Tahun |
| month | `5` | Bulan |
| date_from | `2025-01-01` | Tanggal mulai |
| date_to | `2025-05-31` | Tanggal akhir |

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "data": [
      {
        "id": 10,
        "type": "pemasukan",
        "amount": 500000.00,
        "category": "Donasi",
        "description": "Donasi bulan Mei",
        "transaction_date": "2025-05-15",
        "attachment_url": "https://bahamud.my.id/storage/attachments/abc.jpg",
        "unit": { "id": 2, "name": "Unit Bogor" },
        "recorded_by": { "id": 1, "name": "Ahmad" },
        "created_at": "2025-05-15T09:00:00.000000Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 4,
      "per_page": 15,
      "total": 58,
      "has_more": true
    }
  }
}
```

---

### GET `/transactions/{id}` 🔒
Detail satu transaksi.

---

### POST `/transactions` 🔒
Buat transaksi baru. Gunakan `multipart/form-data` jika ada lampiran.

**Fields:**
| Field | Type | Required |
|-------|------|----------|
| type | `pemasukan` \| `pengeluaran` | ✅ |
| amount | number (min: 1) | ✅ |
| category | string (max 100) | ✅ |
| description | string (max 500) | ❌ |
| transaction_date | `YYYY-MM-DD` | ✅ |
| unit_id | integer | ✅ (admin) |
| attachment | file (jpg/png/webp, max 2MB) | ❌ |

**Response 201:**
```json
{
  "success": true,
  "message": "Transaksi berhasil disimpan.",
  "data": { ... }
}
```

---

### POST `/transactions/{id}` 🔒 Admin
Update transaksi. Gunakan `multipart/form-data` jika ganti lampiran.  
> Menggunakan POST (bukan PUT) karena mendukung file upload.

**Fields:** sama seperti create.

---

### DELETE `/transactions/{id}` 🔒 Admin
Hapus transaksi beserta lampirannya.

---

## 5. Notifications

### GET `/notifications` 🔒
Daftar notifikasi user yang login.

**Query Parameters:** `page`, `per_page`

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "data": [
      {
        "id": 5,
        "title": "Transaksi Baru",
        "message": "Transaksi pemasukan sebesar Rp 500.000 ditambahkan oleh Ahmad.",
        "type": "transaction",
        "is_read": false,
        "created_at": "2025-05-15T09:00:00.000000Z"
      }
    ],
    "pagination": { ... },
    "unread_count": 3
  }
}
```

---

### POST `/notifications/{id}/read` 🔒
Tandai satu notifikasi sudah dibaca.

---

### POST `/notifications/read-all` 🔒
Tandai semua notifikasi sudah dibaca.

---

## 6. Units

### GET `/units` 🔒
Daftar semua unit.

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": [
    {
      "id": 1,
      "name": "Unit Bogor",
      "address": "Bogor",
      "google_maps_url": "https://maps.google.com/...",
      "users_count": 5,
      "created_at": "2025-01-01T00:00:00.000000Z"
    }
  ]
}
```

---

### GET `/units/{id}` 🔒
Detail unit beserta ringkasan keuangan.

---

### POST `/units` 🔒 Admin
Tambah unit baru.

**Request (JSON):**
```json
{
  "name": "Unit Jakarta",
  "address": "Jakarta",
  "google_maps_url": "https://maps.google.com/...",
  "initial_balance": 1000000
}
```

---

### PUT `/units/{id}` 🔒 Admin
Update unit.

---

### DELETE `/units/{id}` 🔒 Admin
Hapus unit.

---

## 7. Users (Admin Only)

### GET `/users` 🔒 Admin
Daftar pengguna.

**Query Parameters:** `page`, `per_page`, `search`, `role`

---

### POST `/users` 🔒 Admin
Tambah pengguna baru.

**Request:**
```json
{
  "name": "Nama User",
  "email": "user@example.com",
  "password": "password123",
  "role": "user",
  "unit_id": 2
}
```

---

### PUT `/users/{id}` 🔒 Admin
Update pengguna. `password` opsional (kosongkan jika tidak ingin ganti).

---

### DELETE `/users/{id}` 🔒 Admin
Hapus pengguna. Tidak bisa hapus diri sendiri.

---

## 8. Kategori

### GET `/categories`
Daftar kategori transaksi (tidak perlu login).

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "pemasukan": [
      "Dana BOS", "Donasi", "Infaq", "Zakat",
      "Iuran Siswa", "Bantuan Pemerintah", "Hibah",
      "Pendapatan Usaha", "Lainnya"
    ],
    "pengeluaran": [
      "Gaji Pegawai", "Listrik & Air", "Internet", "Pemeliharaan",
      "Alat Tulis Kantor", "Konsumsi", "Kegiatan Siswa",
      "Transportasi", "Kebersihan", "Perlengkapan", "Lainnya"
    ]
  }
}
```

---

## Testing dengan cURL

```bash
# Login
curl -X POST https://bahamud.my.id/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Dashboard
curl -X GET https://bahamud.my.id/api/dashboard \
  -H "Authorization: Bearer TOKEN_DISINI"

# Transaksi bulan ini
curl -X GET "https://bahamud.my.id/api/transactions?period=monthly&year=2025&month=5" \
  -H "Authorization: Bearer TOKEN_DISINI"

# Buat transaksi dengan lampiran
curl -X POST https://bahamud.my.id/api/transactions \
  -H "Authorization: Bearer TOKEN_DISINI" \
  -F "type=pemasukan" \
  -F "amount=500000" \
  -F "category=Donasi" \
  -F "transaction_date=2025-05-15" \
  -F "attachment=@/path/to/foto.jpg"
```

---

> 🔒 = Membutuhkan `Authorization: Bearer {token}` di header  
> Admin = Hanya bisa diakses oleh user dengan `role: admin`
