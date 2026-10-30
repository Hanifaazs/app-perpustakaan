# Dokumentasi REST API `app-perpustakaan` (Pertemuan 9)

| No | Method | URL | Body (jika ada) | Status Sukses | Status Gagal | Deskripsi |
|:---|:---|:---|:---|:---:|:---:|:---|
| 1 | `GET` | `/api/books` | - | `200 OK` | - | Mengambil daftar buku, 10 item per halaman. Filter tersedia melalui `?judul=` dan `?category_id=`. |
| 2 | `GET` | `/api/books/{id}` | - | `200 OK` | `404 Not Found` | Mengambil detail 1 buku berdasarkan ID beserta relasi kategori. |
| 3 | `POST` | `/api/books` | JSON (data buku) | `201 Created` | `422 Unprocessable Content` | Menambahkan data buku baru menggunakan validasi `StoreBookRequest`. |
| 4 | `PUT` | `/api/books/{id}` | JSON (data buku) | `200 OK` | `422`, `404` | Memperbarui data buku berdasarkan ID. |
| 5 | `DELETE` | `/api/books/{id}` | - | `200 OK` | `404 Not Found` | Menghapus data buku berdasarkan ID. |
| 6 | `GET` | `/api/members` | - | `200 OK` | - | Mengambil daftar anggota perpustakaan (terpaginasi 10 item/halaman). |
| 7 | `GET` | `/api/members/{id}` | - | `200 OK` | `404 Not Found` | Mengambil detail 1 anggota berdasarkan ID. |
| 8 | `GET` | `/api/loans` | - | `200 OK` | - | Mengambil daftar transaksi peminjaman beserta relasi `member`, `user` (petugas), dan `loanItems.book`. |
| 9 | `POST` | `/api/loans` | JSON (data peminjaman) | `201 Created` | `422 Unprocessable Content` | Mencatat transaksi peminjaman baru untuk 1 atau lebih buku. |
| 10 | `PUT` | `/api/loans/{id}/kembalikan` | - | `200 OK` | `404 Not Found` | Mengubah status peminjaman menjadi `dikembalikan` dan mengisi `tanggal_dikembalikan`. |
| 11 | `GET` | `/api/stats` | - | `200 OK` | - | Mengambil ringkasan statistik (total buku, total anggota, peminjaman aktif). |


### 1. `GET /api/books`
Mengambil daftar buku beserta data kategori. Respons menggunakan API Resource Collection dan pagination. Filter yang tersedia:
- `?judul=laskar`
- `?category_id=1`

#### Request
```http
GET /api/books HTTP/1.1
Host: 127.0.0.1:8000
Accept: application/json
```

#### Response: `200 OK`
```json
{
  "data": [
    {
      "id": 2,
      "judul": "Bahar",
      "penulis": "Tere Liye",
      "penerbit": "Gramedia",
      "tahun_terbit": 2019,
      "isbn": null,
      "stok": 1,
      "sampul": null,
      "category": {
        "id": 1,
        "nama_kategori": "Fiksi"
      },
      "created_at": "2026-09-27T11:59:45.000000Z",
      "updated_at": "2026-09-27T11:59:45.000000Z"
    },
    {
      "id": 3,
      "judul": "Buku Test Relasi",
      "penulis": "Penulis Test",
      "penerbit": "Penerbit Test",
      "tahun_terbit": 2024,
      "isbn": null,
      "stok": 5,
      "sampul": null,
      "category": {
        "id": 2,
        "nama_kategori": "Fiksi Utama"
      },
      "created_at": "2026-09-30T02:23:03.000000Z",
      "updated_at": "2026-09-30T02:23:03.000000Z"
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/books?page=1",
    "last": "http://127.0.0.1:8000/api/books?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "http://127.0.0.1:8000/api/books?page=1",
        "label": "1",
        "active": true
      },
      {
        "url": null,
        "label": "Next &raquo;",
        "active": false
      }
    ],
    "path": "http://127.0.0.1:8000/api/books",
    "per_page": 10,
    "to": 2,
    "total": 2
  }
}
```

---

### 2. `GET /api/books/{id}`
Mengambil detail buku berdasarkan ID.

#### Request
```http
GET /api/books/2 HTTP/1.1
Host: 127.0.0.1:8000
Accept: application/json
```

#### Response Sukses: `200 OK`
```json
{
  "data": {
    "id": 2,
    "judul": "Bahar",
    "penulis": "Tere Liye",
    "penerbit": "Gramedia",
    "tahun_terbit": 2019,
    "isbn": null,
    "stok": 1,
    "sampul": null,
    "category": {
      "id": 1,
      "nama_kategori": "Fiksi"
    },
    "created_at": "2026-09-27T11:59:45.000000Z",
    "updated_at": "2026-09-27T11:59:45.000000Z"
  }
}
```

#### Response Gagal: `404 Not Found` (Misal: `GET /api/books/999`)
```json
{
  "message": "No query results for model [App\\Models\\Book] 999"
}
```

---

### 3. `POST /api/books`
Menambahkan buku baru. Validasi menggunakan `StoreBookRequest`. Jika berhasil, respons menggunakan status `201 Created`.

#### Request
```http
POST /api/books HTTP/1.1
Host: 127.0.0.1:8000
Content-Type: application/json
Accept: application/json

{
  "judul": "Clean Code: A Handbook of Agile Software Craftsmanship",
  "penulis": "Robert C. Martin",
  "penerbit": "Prentice Hall",
  "tahun_terbit": 2008,
  "isbn": "978-0132350884",
  "stok": 5,
  "category_id": 1
}
```

#### Response Sukses: `201 Created`
```json
{
  "data": {
    "id": 4,
    "judul": "Clean Code: A Handbook of Agile Software Craftsmanship",
    "penulis": "Robert C. Martin",
    "penerbit": "Prentice Hall",
    "tahun_terbit": 2008,
    "isbn": "978-0132350884",
    "stok": 5,
    "sampul": null,
    "category": {
      "id": 1,
      "nama_kategori": "Fiksi"
    },
    "created_at": "2026-10-30T01:17:00.000000Z",
    "updated_at": "2026-10-30T01:17:00.000000Z"
  }
}
```

#### Response Gagal Validasi: `422 Unprocessable Content` (Body kosong `{}`)
```json
{
  "message": "Judul buku wajib diisi. (and 5 more errors)",
  "errors": {
    "judul": [
      "Judul buku wajib diisi."
    ],
    "penulis": [
      "Nama penulis wajib diisi."
    ],
    "penerbit": [
      "Nama penerbit wajib diisi."
    ],
    "tahun_terbit": [
      "Tahun terbit wajib diisi."
    ],
    "stok": [
      "Stok wajib diisi."
    ],
    "category_id": [
      "Kategori wajib dipilih."
    ]
  }
}
```

---

### 4. `PUT /api/books/{id}`
Memperbarui data buku berdasarkan ID.

#### Request
```http
PUT /api/books/4 HTTP/1.1
Host: 127.0.0.1:8000
Content-Type: application/json
Accept: application/json

{
  "judul": "Clean Code (Updated Edition)",
  "penulis": "Robert C. Martin",
  "penerbit": "Prentice Hall",
  "tahun_terbit": 2008,
  "isbn": "978-0132350884",
  "stok": 12,
  "category_id": 1
}
```

#### Response Sukses: `200 OK`
```json
{
  "data": {
    "id": 4,
    "judul": "Clean Code (Updated Edition)",
    "penulis": "Robert C. Martin",
    "penerbit": "Prentice Hall",
    "tahun_terbit": 2008,
    "isbn": "978-0132350884",
    "stok": 12,
    "sampul": null,
    "category": {
      "id": 1,
      "nama_kategori": "Fiksi"
    },
    "created_at": "2026-10-30T01:17:00.000000Z",
    "updated_at": "2026-10-30T01:18:00.000000Z"
  }
}
```

---

### 5. `DELETE /api/books/{id}`
Menghapus buku berdasarkan ID.

#### Request
```http
DELETE /api/books/4 HTTP/1.1
Host: 127.0.0.1:8000
Accept: application/json
```

#### Response Sukses: `200 OK`
```json
{
  "message": "Buku berhasil dihapus."
}
```

#### Response Gagal: `404 Not Found`
```json
{
  "message": "No query results for model [App\\Models\\Book] 4"
}
```

---

### 6. `GET /api/members`
Mengambil daftar anggota perpustakaan.

#### Request
```http
GET /api/members HTTP/1.1
Host: 127.0.0.1:8000
Accept: application/json
```

#### Response Sukses: `200 OK`
```json
{
  "data": [
    {
      "id": 1,
      "nama": "Budi Relasi",
      "nim": "12345678",
      "email": "budi@example.com",
      "nomor_telepon": "08123456789",
      "alamat": "Jl. Test No. 1",
      "status": "aktif",
      "created_at": "2026-09-30T02:23:03.000000Z",
      "updated_at": "2026-09-30T02:23:03.000000Z"
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/members?page=1",
    "last": "http://127.0.0.1:8000/api/members?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "http://127.0.0.1:8000/api/members?page=1",
        "label": "1",
        "active": true
      },
      {
        "url": null,
        "label": "Next &raquo;",
        "active": false
      }
    ],
    "path": "http://127.0.0.1:8000/api/members",
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

---

### 7. `GET /api/members/{id}`
Mengambil detail anggota berdasarkan ID.

#### Request
```http
GET /api/members/1 HTTP/1.1
Host: 127.0.0.1:8000
Accept: application/json
```

#### Response Sukses: `200 OK`
```json
{
  "data": {
    "id": 1,
    "nama": "Budi Relasi",
    "nim": "12345678",
    "email": "budi@example.com",
    "nomor_telepon": "08123456789",
    "alamat": "Jl. Test No. 1",
    "status": "aktif",
    "created_at": "2026-09-30T02:23:03.000000Z",
    "updated_at": "2026-09-30T02:23:03.000000Z"
  }
}
```

#### Response Gagal: `404 Not Found` (Misal: `GET /api/members/999`)
```json
{
  "message": "No query results for model [App\\Models\\Member] 999"
}
```

---

### 8. `GET /api/loans`
Mengambil daftar peminjaman beserta relasi `member`, `user` (`petugas`), dan `loanItems.book` (`books`).

#### Request
```http
GET /api/loans HTTP/1.1
Host: 127.0.0.1:8000
Accept: application/json
```

#### Response Sukses: `200 OK`
```json
{
  "data": [
    {
      "id": 1,
      "member": {
        "id": 1,
        "nama": "Budi Relasi"
      },
      "petugas": {
        "id": 1,
        "name": "Admin"
      },
      "tanggal_pinjam": "2026-10-30",
      "tanggal_kembali": "2026-11-06",
      "tanggal_dikembalikan": null,
      "status": "dipinjam",
      "books": [
        {
          "id": 2,
          "judul": "Bahar"
        }
      ]
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/loans?page=1",
    "last": "http://127.0.0.1:8000/api/loans?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "http://127.0.0.1:8000/api/loans?page=1",
        "label": "1",
        "active": true
      },
      {
        "url": null,
        "label": "Next &raquo;",
        "active": false
      }
    ],
    "path": "http://127.0.0.1:8000/api/loans",
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

---

### 9. `POST /api/loans`
Mencatat peminjaman baru beserta daftar buku (`book_ids[]`). Sertakan `user_id` pada request body.

#### Request
```http
POST /api/loans HTTP/1.1
Host: 127.0.0.1:8000
Content-Type: application/json
Accept: application/json

{
  "member_id": 1,
  "user_id": 1,
  "tanggal_pinjam": "2026-10-30",
  "tanggal_kembali": "2026-11-06",
  "book_ids": [2, 3]
}
```

#### Response Sukses: `201 Created`
```json
{
  "data": {
    "id": 2,
    "member": {
      "id": 1,
      "nama": "Budi Relasi"
    },
    "petugas": {
      "id": 1,
      "name": "Admin"
    },
    "tanggal_pinjam": "2026-10-30",
    "tanggal_kembali": "2026-11-06",
    "tanggal_dikembalikan": null,
    "status": "dipinjam",
    "books": [
      {
        "id": 2,
        "judul": "Bahar"
      },
      {
        "id": 3,
        "judul": "Buku Test Relasi"
      }
    ]
  }
}
```

#### Response Gagal Validasi: `422 Unprocessable Content`
```json
{
  "message": "Anggota wajib dipilih. (and 4 more errors)",
  "errors": {
    "member_id": [
      "Anggota wajib dipilih."
    ],
    "user_id": [
      "Petugas (user_id) wajib diisi."
    ],
    "tanggal_pinjam": [
      "Tanggal pinjam wajib diisi."
    ],
    "tanggal_kembali": [
      "Tanggal kembali wajib diisi."
    ],
    "book_ids": [
      "Pilih minimal satu buku."
    ]
  }
}
```

---

### 10. `PUT /api/loans/{id}/kembalikan`
Menandai peminjaman sebagai dikembalikan. Status diubah menjadi `dikembalikan` dan `tanggal_dikembalikan` diisi dengan tanggal hari ini.

#### Request
```http
PUT /api/loans/2/kembalikan HTTP/1.1
Host: 127.0.0.1:8000
Accept: application/json
```

#### Response Sukses: `200 OK`
```json
{
  "data": {
    "id": 2,
    "member": {
      "id": 1,
      "nama": "Budi Relasi"
    },
    "petugas": {
      "id": 1,
      "name": "Admin"
    },
    "tanggal_pinjam": "2026-10-30",
    "tanggal_kembali": "2026-11-06",
    "tanggal_dikembalikan": "2026-10-30",
    "status": "dikembalikan",
    "books": [
      {
        "id": 2,
        "judul": "Bahar"
      },
      {
        "id": 3,
        "judul": "Buku Test Relasi"
      }
    ]
  }
}
```

#### Response Gagal: `404 Not Found`
```json
{
  "message": "No query results for model [App\\Models\\Loan] 999"
}
```

---

### 11. `GET /api/stats`
Mengambil jumlah buku, anggota, dan peminjaman aktif (`status = 'dipinjam'`).

#### Request
```http
GET /api/stats HTTP/1.1
Host: 127.0.0.1:8000
Accept: application/json
```

#### Response Sukses: `200 OK`
```json
{
  "total_buku": 2,
  "total_anggota": 1,
  "peminjaman_aktif": 0
}
```
