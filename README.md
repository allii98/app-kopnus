# MX100 Job Portal – Laravel RESTful API

Aplikasi API backend untuk job portal **MX100**, menghubungkan perusahaan dan freelancer. Employer bisa memposting pekerjaan dan melihat CV, sementara freelancer bisa melamar ke pekerjaan dengan mengirimkan satu CV.

---

## 🚀 Fitur

### 🔐 Autentikasi
- Register dengan role (`freelancer` / `employer`)
- Login & logout menggunakan Laravel Sanctum

### 💼 Employer
- Membuat job posting (draft)
- Mempublish job
- Melihat daftar job miliknya
- Melihat daftar CV dari pelamar

### 👨‍💻 Freelancer
- Melihat daftar job yang dipublish
- Melamar 1x ke setiap job dengan **unggah file CV (PDF/DOC)**

---

## ⚙️ Instalasi

1. **Clone repositori**
   ```bash
   git clone https://github.com/allii98/app-kopnus.git
   cd app-kopnus
   ```

2. **Install dependency**
   ```bash
   composer install
   ```

3. **Copy dan konfigurasi `.env`**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Setup database**
   - Pastikan database MySQL sudah dibuat.
   - Ubah `.env` sesuai database:
     ```
     DB_DATABASE=mx100
     DB_USERNAME=root
     DB_PASSWORD=
     ```

5. **Migrate dan Seed**
   ```bash
   php artisan migrate --seed
   ```

6. **Buat symbolic link untuk akses file**
   ```bash
   php artisan storage:link
   ```

7. **Jalankan server**
   ```bash
   php artisan serve
   ```

---

## 🧪 Postman Collection

Import file Postman untuk menguji API:

- File: `KOPNUS.postman_collection.json`
- URL dasar: `http://localhost:8000/api`
- Tambahkan token di tab **Authorization** → **Bearer Token** untuk endpoint yang butuh login.

### ✅ Upload CV (Freelancer Apply Job)

- **Endpoint**: `POST /api/jobs/{id}/apply`
- **Method**: POST
- **Authorization**: Bearer Token (login sebagai freelancer)
- **Headers**:
  - Content-Type: multipart/form-data
- **Body**:
  - `cv_file`: (file) – unggah file CV dengan format PDF, DOC, DOCX

---

## 📚 API Endpoint

### 🔐 AUTH

| Method | Endpoint      | Deskripsi         |
|--------|---------------|-------------------|
| POST   | /register     | Register user     |
| POST   | /login        | Login user        |
| POST   | /logout       | Logout user       |

---

### 💼 JOBS

| Method | Endpoint              | Role      | Deskripsi                             |
|--------|-----------------------|-----------|----------------------------------------|
| POST   | /jobs                 | employer  | Buat job baru (draft)                 |
| PATCH  | /jobs/{id}/publish    | employer  | Publish job                           |
| GET    | /jobs/my              | employer  | Lihat job milik sendiri               |
| GET    | /jobs                 | all       | Lihat semua job yang dipublish        |

---

### 📎 APPLICATIONS (CV)

| Method | Endpoint                  | Role        | Deskripsi                             |
|--------|---------------------------|-------------|----------------------------------------|
| POST   | /jobs/{id}/apply          | freelancer  | Apply job, unggah file CV             |
| GET    | /jobs/{id}/applications  | employer    | Lihat pelamar job                     |

---

## 🛠️ Seeder Data Contoh

Seeder membuat:
- 1 employer (`hrd@majujaya.com`, password: `password`)
- 1 freelancer (`budi@gmail.com`, password: `password`)

---

## 📂 Struktur Folder Penting

```
app/
  Models/
    User.php
    Job.php
    Application.php
  Http/
    Controllers/
      AuthController.php
      JobController.php
      ApplicationController.php
routes/
  api.php
database/
  migrations/
  seeders/
  factories/
```

---

## 🔒 Role & Validasi

- Role `employer` hanya bisa akses endpoint employer
- Role `freelancer` hanya bisa apply job
- CV hanya bisa dikirim **1x per job**
- File CV dibatasi max 2MB, format: PDF/DOC/DOCX

---

## 📌 Catatan Tambahan

- CV disimpan di `storage/app/public/cv/`
- Akses URL publik: `http://localhost:8000/storage/cv/namafile.pdf`

---

## ✅ Selesai

Silakan sesuaikan `APP_URL` di `.env` jika kamu deploy ke server publik.

---
