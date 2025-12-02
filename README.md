# Hokben Vehicle Booking System

> [Aplikasi yang dibuat dalam rangka digitalisasi (Paperless), sebelumnya sudah ada sistem peminjaman yang masih menggunakan kertas tetapi langkah ini dinilai kurang efisien, dengan adanya gerakan Paperless diharapkan dapat menggantikan peranan kertas dan menambah nilai efisien serta efektifitas]

Aplikasi Hokben Vehicle Booking System adalah sebuah sistem booking berbasis web yang dirancang untuk Peminjaman mobil untuk internal perusahaan. Proyek ini dibangun sebagai langkah digitalisasi dari Perusahaan PT.Eka Boga Inti.

---

## ✨ Fitur Utama

Berikut adalah beberapa fitur kunci dari aplikasi ini:

* 🔑 **Autentikasi Pengguna:** Proses login, register, dan lupa password yang aman.
* 👤 **Manajemen Peran (Roles):** Sistem dengan beberapa level akses diantaranya karyawa, admin & security.
* 📅 **Sistem Booking:** Pengguna dapat membuat dan melihat pemesanan.
* ✅ **Alur Persetujuan (Approval):** Pemesanan memerlukan persetujuan dari Admin.
* 📊 **Dashboard Admin:** Halaman khusus admin untuk mengelola data master (pengguna, kendaraan).

---

## 🛠️ Teknologi yang Digunakan

Proyek ini dibangun menggunakan tumpukan teknologi modern:

* **Backend:** Laravel 10
* **Frontend:** Blade & Tailwind CSS
* **Database:** MySQL
* **Server:**  Apache

---

## 📦 Instalasi & Konfigurasi

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di lingkungan lokal Anda.

### Prasyarat

Pastikan Anda sudah menginstal perangkat lunak berikut:
* PHP 8.1+
* Composer
* Node.js & NPM
* Database (MySQL)

### Langkah-langkah Instalasi

1.  **Clone repositori:**
    ```bash
    git clone https://github.com/rnggaul/pinjam-mobil.git
    ```

2.  **Masuk ke direktori proyek:**
    ```bash
    cd pimjam-mobil
    ```

3.  **Install dependensi backend:**
    ```bash
    composer install
    ```

4.  **Install dependensi frontend:**
    ```bash
    npm install
    npm run build
    ```

5.  **Salin file `.env`:**
    ```bash
    cp .env.example .env
    ```

6.  **Generate application key (jika pakai Laravel):**
    ```bash
    php artisan key:generate
    ```

7.  **Konfigurasi file `.env`:**
    Buka file `.env` dan sesuaikan pengaturan database Anda:
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=carbooking
    DB_USERNAME=root
    DB_PASSWORD=
    ```

8.  **Jalankan migrasi dan seeder database:**
    ```bash
    php artisan migrate --seed
    ```

9.  **Jalankan server pengembangan:**
    ```bash
    php artisan serve
    ```

Aplikasi sekarang dovrebbe berjalan di `http://localhost:8000`.

---

## 📖 Cara Penggunaan

Setelah instalasi, Anda dapat mengakses aplikasi.

* **Akun Admin:**
    * Email: `superadmin@gmail.com`
    * Password: `SuperAdmin123!`
 
* **Default Password User:**
  * Password: `12345678`


---

## 🤝 Berkontribusi

Kontribusi sangat kami harapkan! Jika Anda ingin berkontribusi, silakan *fork* repositori ini dan buat *pull request* dengan perubahan Anda.

1.  Fork Proyek
2.  Buat Branch Fitur Anda (`git checkout -b fitur/FiturBaru`)
3.  Commit Perubahan Anda (`git commit -m 'Menambahkan FiturBaru'`)
4.  Push ke Branch (`git push origin fitur/FiturBaru`)
5.  Buka Pull Request

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah Lisensi MIT. Lihat file `LICENSE` untuk detailnya.
