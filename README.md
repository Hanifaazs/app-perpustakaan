# Sistem Perpustakaan Kampus

Aplikasi manajemen perpustakaan berbasis **Laravel 12** untuk mengelola buku, kategori, anggota, dan transaksi peminjaman. Aplikasi ini juga menyediakan REST API dan menggunakan API internal untuk beberapa halaman.

---

## Fitur

1. **Autentikasi & Role-Based Access Control (RBAC)**
   - Login berbasis session dengan proteksi middleware `auth`.
   - **Admin** dapat mengelola seluruh data, termasuk kategori. **Petugas** mengelola buku, anggota, dan peminjaman.
   - Setelah login, pengguna diarahkan ke Dashboard.
   - Pengguna dapat mengubah profil dan password.

2. **Manajemen Katalog & Keanggotaan (CRUD)**
   - Pengelolaan buku dengan pagination serta filter judul dan kategori.
   - Pengelolaan kategori oleh Admin.
   - Pengelolaan anggota dengan alamat email mahasiswa PENS (`@pens.ac.id`).

3. **Sirkulasi Peminjaman Buku**
   - Satu transaksi dapat berisi beberapa buku (`loan_items`).
   - Petugas yang sedang login dicatat sebagai penanggung jawab transaksi.
   - Tanggal pengembalian dicatat saat buku dikembalikan.

4. **REST API (11 endpoint)**
   - Endpoint dengan prefix `/api` untuk resource `books`, `members`, `loans`, dan `stats`.
   - **Laravel API Resource** (`BookResource`, `MemberResource`, `LoanResource`) digunakan untuk membentuk respons JSON.
   - Relasi dimuat dengan eager loading untuk menghindari N+1 query.
   - Lihat [`API.md`](API.md) untuk dokumentasi endpoint.

5. **API Internal**
   - **Dashboard:** Mengambil ringkasan statistik dari `GET /api/stats` menggunakan Laravel HTTP Client (`Http::get()`).
   - **Laporan Peminjaman:** Mengambil riwayat transaksi dari `GET /api/loans` dan menggunakan metadata API untuk navigasi pagination.
   - Jika API tidak dapat diakses, aplikasi menangani `ConnectionException`.

6. **Data Awal & Pagination**
   - Seeder dan Factory menggunakan locale Indonesia (`APP_FAKER_LOCALE=id_ID`).
   - Data buku disesuaikan dengan kategori dan penerbit Indonesia.
   - Template pagination kustom tanpa dependensi Tailwind CSS.

---

## Instalasi dan Menjalankan Aplikasi

### 1. Kloning repositori
```bash
git clone <url-repository-anda>
cd app-perpustakaan
```

### 2. Pasang dependensi PHP
```bash
composer install
```

### 3. Konfigurasi lingkungan (`.env`)
Salin file konfigurasi:
```bash
cp .env.example .env
php artisan key:generate
```

Pastikan variabel berikut telah terkonfigurasi di file `.env`:
```env
# Koneksi database (SQLite)
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Locale Faker untuk data awal
APP_FAKER_LOCALE=id_ID

# URL server untuk panggilan API internal
INTERNAL_API_URL=http://127.0.0.1:8011
```

Jika file `database/database.sqlite` belum ada, buat dengan perintah `touch database/database.sqlite`.

### 4. Migrasi dan pengisian data awal
Jalankan migrasi dan isi database:
```bash
php artisan migrate:fresh --seed
```
Perintah ini membuat tabel dan menambahkan:
- 3 akun pengguna (1 Admin, 2 Petugas)
- 5 kategori buku
- 20 buku dengan judul dan penerbit Indonesia
- 15 data anggota mahasiswa PENS
- 10 transaksi peminjaman

### 5. Menjalankan server lokal
Jalankan dua server di terminal berbeda. Server PHP built-in (`php artisan serve`) memproses satu request dalam satu waktu, sementara Dashboard dan Laporan mengirim permintaan ke API internal.

- **Terminal 1 (aplikasi web):**
  ```bash
  php artisan serve --port=8000
  ```
  Buka aplikasi di **`http://127.0.0.1:8000`**.

- **Terminal 2 (API internal):**
  ```bash
  php artisan serve --port=8011
  ```
  Server ini melayani permintaan internal dari `DashboardController` dan `LoanController@report`.

---

## Kredensial Login Default

| Role | Email | Password | Nama Lengkap |
|:---|:---|:---|:---|
| **Administrator** | `admin@pens.ac.id` | `password` | Bambang Sutrisno |
| **Petugas 1** | `petugas1@pens.ac.id` | `password` | Siti Rahmawati |
| **Petugas 2** | `petugas2@pens.ac.id` | `password` | Ahmad Fauzi |

---

## Menjalankan Pengujian

Jalankan pengujian dengan perintah:
```bash
php artisan test
```
Pengujian mencakup autentikasi, otorisasi RBAC, 11 endpoint REST API, penanganan kegagalan koneksi API internal, dan pagination kustom.

---

## Dokumentasi

- **[`API.md`](API.md)**: Metode, endpoint, payload request, status code, dan contoh respons JSON.
- **[`DEMO.md`](DEMO.md)**: Langkah-langkah demo aplikasi.
