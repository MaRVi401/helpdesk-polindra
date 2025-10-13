---
<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
    <img src="http://vanseodesign.com/blog/wp-content/uploads/2015/09/sass-logo.png" width="150" alt="SCSS Logo">
    <img src="https://desarrolloweb.com/storage/tag_images/actual/4nqulUliAHwz5h1JaZOlpJkO8I3qc9lpxakpHzma.png" width="200" alt="VITE Logo">
  </a>
</p>

# 🚀 Helpdesk Polindra

Helpdesk berbasis **Laravel** dan **Vite**, menggunakan **SCSS** untuk styling.  
Proyek ini dirancang untuk sistem pengelolaan layanan bantuan internal kampus Polindra.

---

## 📦 Persyaratan Sistem

Pastikan sudah menginstal:
- PHP >= 8.2  
- Composer  
- Node.js >= 18  
- NPM  
- MySQL / MariaDB  
- Git

---
## ⚙️ Langkah Instalasi


1. **Clone repository**

   ```bash
   git clone https://github.com/USERNAME/helpdesk-polindra.git
   cd helpdesk-polindra
   ```

2. **Install dependency Laravel**

   ```bash
   composer install
   ```

3. **Install dependency frontend**

   ```bash
   npm install
   ```

4. **Salin file environment**

   ```bash
   cp .env.example .env
   ```

5. **Generate application key**

   ```bash
   php artisan key:generate
   ```

6. **Konfigurasi file `.env`**
   Atur koneksi database sesuai dengan konfigurasi lokal:

   ```
   DB_DATABASE=helpdesk
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. **Migrasi database**

   ```bash
   php artisan migrate
   ```

---

## 💅 Build SCSS (Frontend)

Gunakan **Vite** untuk mengompilasi SCSS menjadi CSS:

* **Mode development** (otomatis reload)

  ```bash
  npm run dev
  ```

* **Mode production** (build final)

  ```bash
  npm run build
  ```

File hasil build akan berada di:

```
public/build/
```

---

## 🚀 Menjalankan Aplikasi

Jalankan server Laravel:

```bash
php artisan serve
```

Akses aplikasi di browser:

```
http://127.0.0.1:8000
```

---

## 🧠 Catatan

* Jika terjadi error pada Vite, hapus folder `node_modules` lalu jalankan ulang:

  ```bash
  npm install
  ```
* Untuk membersihkan cache Laravel:

  ```bash
  php artisan optimize:clear
  ```

---
