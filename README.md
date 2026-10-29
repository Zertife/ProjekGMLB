# Sistem Inventaris Sekolah

Aplikasi inventaris sekolah berbasis Laravel untuk mengelola kategori dan barang, peminjaman, pengembalian, akun pengguna, serta catatan aktivitas. Terdapat dua peran: **Admin** dan **User**.

## Kebutuhan

- PHP 8.2 atau lebih baru dan ekstensi PHP yang dibutuhkan Laravel
- Composer
- MySQL
- Node.js dan npm

## Instalasi

1. Clone repository, lalu masuk ke folder proyek.

   ```bash
   git clone <URL-REPOSITORY>
   cd Sistem_Inventaris
   ```

2. Buat file konfigurasi lokal dan pasang dependensi PHP.

   ```powershell
   Copy-Item .env.example .env
   composer install
   ```

3. Buat database MySQL, misalnya `sistem_inventaris`, lalu isi pengaturan berikut di `.env` sesuai konfigurasi lokal:

   ```dotenv
   APP_NAME="Sistem Inventaris Sekolah"
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sistem_inventaris
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. Buat application key, jalankan migration, dan isi data contoh.

   Jalankan:
   php artisan key:generate
   php artisan migrate --seed

5. Pasang dan build aset frontend.

   Jalankan:
   npm install
   npm run build
   

6. Jalankan aplikasi.

   Jalankan:
   php artisan serve

Buka `http://127.0.0.1:8000` di browser. Untuk pengembangan frontend dengan hot reload, jalankan `npm run dev` di terminal terpisah.

## Akun Seeder

`php artisan migrate --seed` membuat akun dan data contoh berikut:

- **Admin** — `admin@email.com` / `admin123`
- **User 1** — `user1@email.com` / `user123`
- **User 2** — `user2@email.com` / `user123!`

