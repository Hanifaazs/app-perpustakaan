## Persiapan Demo

Pastikan dependensi sudah terpasang dan database berisi data awal. Untuk mengisinya kembali, jalankan:

### 1. Isi database
```bash
php artisan migrate:fresh --seed
```
Perintah ini menjalankan migrasi dan seeder: `UserSeeder`, `CategorySeeder`, `BookSeeder`, `MemberSeeder`, dan `LoanSeeder`. Database akan berisi 20 buku, 5 kategori, 15 anggota, dan 10 transaksi peminjaman.

### 2. Jalankan server
Jalankan dua server di terminal terpisah. Server PHP bawaan (`php artisan serve`) hanya memproses satu permintaan dalam satu waktu.

- **Terminal 1 (aplikasi web):**
  ```bash
  php artisan serve --port=8000
  ```
  Buka aplikasi di `http://127.0.0.1:8000`.

- **Terminal 2 (API internal):**
  ```bash
  php artisan serve --port=8011
  ```
  Server ini digunakan oleh `DashboardController` dan `LoanController@report`.

### Akun demo
| Role | Email | Password | Nama Lengkap |
|:---|:---|:---|:---|
| **Admin** | `admin@pens.ac.id` | `password` | Bambang Sutrisno |
| **Petugas 1** | `petugas1@pens.ac.id` | `password` | Siti Rahmawati |
| **Petugas 2** | `petugas2@pens.ac.id` | `password` | Ahmad Fauzi |

---

## Langkah Demo

### Langkah 1: Autentikasi dan Login
1. Buka browser dan arahkan ke `http://127.0.0.1:8000`.
2. Pengguna yang belum login akan diarahkan ke halaman `/login`.
3. Masukkan kredensial yang salah untuk melihat pesan validasi "Email atau password salah".
4. Masukkan kredensial admin:
   - **Email:** `admin@pens.ac.id`
   - **Password:** `password`
5. Klik **Login**.
6. Login berhasil. Pengguna diarahkan ke Dashboard `/`, pesan sukses ditampilkan, dan nama admin muncul di navbar.

---

### Langkah 2: Tampilkan Dashboard (Konsumsi `GET /api/stats`)
1. Di halaman utama `/` (Dashboard), tunjukkan 3 kartu statistik ringkasan perpustakaan:
   - **Total Buku:** 20
   - **Total Anggota:** 15
   - **Peminjaman Aktif:** (sesuai transaksi berstatus *dipinjam*)
2. Statistik diambil `DashboardController` dari endpoint `GET /api/stats` menggunakan Laravel HTTP Client, bukan dari query di Blade.
3. Tunjukkan tombol **Lihat Laporan Peminjaman** yang mengarah ke `/loans/report`.

---

### Langkah 3: Kelola Kategori Buku (Khusus Admin)
1. Klik menu **Kategori** di navbar (`/categories`).
2. Tunjukkan daftar kategori yang sudah ada (`Novel`, `Komik`, `Sains`, `Teknologi`, `Sejarah`).
3. Klik tombol **Tambah Kategori**:
   - **Nama Kategori:** `Ensiklopedia`
   - **Deskripsi:** `Referensi ilmu pengetahuan bergambar.`
4. Klik **Simpan**.
5. Kategori baru muncul di tabel dan pesan sukses ditampilkan.
6. *(Opsional)* Tunjukkan bahwa menu Kategori hanya tersedia untuk pengguna dengan role `admin` dan dilindungi middleware `CheckAdminRole`.

---

### Langkah 4: Tambah dan Kelola Buku
1. Klik menu **Buku** di navbar (`/books`).
2. Perhatikan pagination di bawah tabel sudah rapi menggunakan link teks bersih (`« Sebelumnya`, `1`, `2`, `Berikutnya »`).
3. Klik tombol **Tambah Buku**:
   - **Judul:** `Ensiklopedia Sains Nusantara`
   - **Penulis:** `Prof. Hendra Wijaya`
   - **Penerbit:** `Balai Pustaka`
   - **Tahun Terbit:** `2023`
   - **ISBN:** `978-602-0012-34`
   - **Stok:** `8`
   - **Kategori:** Pilih `Ensiklopedia`
4. Klik **Simpan**.
5. Buku baru muncul di daftar dengan stok yang dimasukkan.

---

### Langkah 5: Tambah dan Kelola Anggota
1. Klik menu **Anggota** di navbar (`/members`).
2. Tunjukkan daftar mahasiswa dengan format email institusi `@pens.ac.id`.
3. Klik **Tambah Anggota**:
   - **Nama:** `Rizky Pratama`
   - **NIM:** `3122600099`
   - **Email:** `rizky.pratama99@pens.ac.id`
   - **Nomor Telepon:** `081298765432`
   - **Alamat:** `Jl. Sukolilo No. 45, Surabaya`
   - **Status:** `aktif`
4. Klik **Simpan**.
5. Anggota baru muncul di daftar.

---

### Langkah 6: Transaksi Peminjaman Baru (Multi-Buku)
1. Klik menu **Peminjaman** di navbar (`/loans`).
2. Klik tombol **Catat Peminjaman** (`/loans/create`).
3. Isi form peminjaman:
   - **Anggota:** Pilih `Rizky Pratama`
   - **Tanggal Pinjam:** (Tanggal hari ini, misal `2026-10-30`)
   - **Tanggal Kembali:** (7 hari ke depan)
   - **Pilih Buku:** Pilih 2 buku sekaligus (checkbox / multi-select).
4. Klik **Simpan Peminjaman**.
5. Peminjaman tercatat dengan admin yang sedang login sebagai petugas pencatat.

---

### Langkah 7: Periksa Daftar Peminjaman Aktif
1. Pada tabel Peminjaman `/loans`, tunjukkan transaksi baru yang dibuat.
2. Status transaksi ditampilkan sebagai **Dipinjam**.
3. Klik tombol **Detail** pada transaksi untuk melihat rincian anggota, petugas pencatat, dan daftar buku yang dipinjam.

---

### Langkah 8: Proses Pengembalian Buku
1. Pada daftar transaksi peminjaman aktif, klik tombol **Kembalikan** (atau buka halaman edit transaksi lalu ubah status).
2. Periksa hasil pengembalian:
   - Status peminjaman berubah menjadi **Dikembalikan**.
   - Kolom `tanggal_dikembalikan` otomatis terisi dengan tanggal hari ini.
   - Angka peminjaman aktif di Dashboard berkurang secara otomatis.

---

### Langkah 9: Tampilkan Laporan Peminjaman (Konsumsi `GET /api/loans`)
1. Klik menu **Laporan** di navbar (`/loans/report`).
2. Data tabel diambil `LoanController@report` melalui HTTP request ke endpoint `GET /api/loans`.
3. Periksa data relasi `member`, `petugas`, dan `books` pada respons JSON API.
4. Gunakan navigasi pagination `« Sebelumnya` dan `Selanjutnya »`. Halaman mengikuti metadata `meta.current_page` dan `meta.last_page` dari API.

---

### Langkah 10: Demonstrasi Pengujian REST API di Postman
Buka aplikasi Postman atau jalankan perintah `curl` untuk menguji endpoint API secara langsung:

1. **`GET /api/stats`**
   - Header: `Accept: application/json`
   - URL: `http://127.0.0.1:8000/api/stats`
   - Status: `200 OK`
   - Tunjukkan JSON respons berisi `total_buku`, `total_anggota`, `peminjaman_aktif`.

2. **`GET /api/books?judul=Laskar` (Pencarian/Filter)**
   - Header: `Accept: application/json`
   - URL: `http://127.0.0.1:8000/api/books?judul=Laskar`
   - Status: `200 OK`
   - Tunjukkan hasil filter pencarian parsial judul buku.

3. **`POST /api/books` dengan body kosong (Validasi Error)**
   - Header: `Accept: application/json`, `Content-Type: application/json`
   - Body: `{}`
   - Status: `422 Unprocessable Content`
   - Tunjukkan pesan validasi untuk setiap field yang wajib diisi.

4. **`POST /api/loans` (Transaksi Peminjaman via API)**
   - Body JSON:
     ```json
     {
       "member_id": 1,
       "user_id": 1,
       "tanggal_pinjam": "2026-10-30",
       "tanggal_kembali": "2026-11-06",
       "book_ids": [1, 2]
     }
     ```
   - Status: `201 Created`
   - Tunjukkan bahwa data tersimpan dengan status default `"dipinjam"`.

5. **`PUT /api/loans/{id}/kembalikan` (Pengembalian via API)**
   - URL: `http://127.0.0.1:8000/api/loans/1/kembalikan`
   - Status: `200 OK`
   - Tunjukkan field `status: "dikembalikan"` dan `tanggal_dikembalikan` terisi tanggal hari ini.

---

### Penanganan API yang Tidak Tersedia
1. Matikan instance server di **Terminal 2** (port `8011`) dengan menekan `Ctrl+C`.
2. Kembali ke browser dan lakukan refresh pada halaman **Dashboard** (`/`) dan **Laporan** (`/loans/report`).
3. Periksa hasilnya:
   - Halaman tidak menampilkan error 500.
   - Dashboard menampilkan angka statistik `0`.
   - Laporan menampilkan tabel kosong ("Belum ada data peminjaman").
4. Controller menangani `\Illuminate\Http\Client\ConnectionException` jika API internal tidak dapat diakses.
