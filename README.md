# CCTV RMI & Financial Dashboard

Sistem Manajemen CCTV Terpadu, Pemantauan Real-Time, serta Integrasi Inventaris & Invoice Keuangan berbasis **Laravel 11**, **Livewire 3**, **go2rtc**, dan desain modern **Hope UI**.

---

## 🚀 Fitur Utama & Pembaruan Desain

### 📺 1. Monitoring Wall Publik (`/`)
Halaman landing utama (`http://127.0.0.1:8000/`) sekarang langsung memuat **Monitoring Wall Publik** bertema gelap (Control Room Mode):
*   **Grid Pemantauan Fleksibel**: Pilihan kolom dinamis (1, 2, 3, atau 4 kolom) yang dapat diganti secara instan oleh viewer.
*   **Filter Berbasis Area**: Filter lokasi area CCTV secara real-time dengan tab lokasi interaktif.
*   **Privasi Data**: Alamat IP kamera disembunyikan sepenuhnya dari pandangan publik dan trigger pemanggilan stream.
*   **Tanpa Tautan Administrasi**: Tidak menampilkan tombol login maupun akses dashboard apa pun demi estetika dan kerahasiaan panel admin.

### 🔐 2. Portal Login Administratif Rahasia (`/secure-gate-login`)
Untuk masuk ke sistem admin, kelola kamera, inventaris, dan cetak invoice keuangan, gunakan pintu gerbang rahasia:
*   **URL Akses**: `http://127.0.0.1:8000/secure-gate-login`
*   **Akun Default**:
    *   **Email**: `superadmin@cctv.com`
    *   **Password**: `password` (atau password default Laravel)

### 🎨 3. Sistem Desain Hope UI Modern
*   **Tipografi**: Menggunakan font premium `Lexend Deca`.
*   **Warna Aksen**: Biru Indigo (`#4361ee`) dengan latar belakang abu-abu terang bersih (`#f0f2f5`).
*   **Sidebar Minimalis**: Navigasi ramping 90px dengan ikon-sentris yang hemat ruang pemantauan.

---

## 🛠️ Persyaratan Sistem
*   **PHP**: 8.1 ke atas (Direkomendasikan PHP 8.3)
*   **Database**: MySQL 8.0+
*   **Cache / Queue**: Redis 6.0+
*   **Node.js**: 18.0+ & npm
*   **Media Server**: [go2rtc](https://github.com/AlexxIT/go2rtc) untuk WebRTC stream

---

## 📦 Panduan Instalasi Cepat

1.  **Clone Repository** ke folder lokal (misal: Laragon `www`).
2.  **Instalasi Dependensi PHP**:
    ```bash
    composer install
    ```
3.  **Instalasi Dependensi Node.js**:
    ```bash
    npm install
    ```
4.  **Konfigurasi Environment**:
    Salin file `.env.example` ke `.env`, lalu buat database kosong dan sesuaikan pengaturan koneksi:
    ```bash
    copy .env.example .env
    php artisan key:generate
    ```
5.  **Migrasi & Seed Data Demo**:
    ```bash
    php artisan migrate:fresh --seed
    ```
6.  **Build Assets (Vite)**:
    ```bash
    npm run build
    ```
7.  **Jalankan Server Lokal**:
    ```bash
    php artisan serve
    ```

---

## 📽️ Konfigurasi go2rtc Media Server
Aplikasi ini menggunakan `go2rtc` sebagai media server performa tinggi. Aliran video WebRTC dimuat secara langsung di browser klien tanpa membebani PHP.

1.  Unduh binary go2rtc sesuai sistem operasi Anda di [go2rtc Releases](https://github.com/AlexxIT/go2rtc/releases).
2.  Gunakan contoh konfigurasi `go2rtc.yaml` yang tersedia di direktori root proyek.
3.  Jalankan service:
    *   **Windows**: Jalankan `.\go2rtc.exe` di PowerShell.
    *   **Linux/macOS**: `./go2rtc`
4.  Pastikan port `1984` (API) dan `8555` (WebRTC) tidak terhalang firewall.

---

## 📱 Build APK Android (Capacitor)
Aplikasi web ini siap dibungkus menjadi aplikasi mobile Android:

1.  **Instal Capacitor**:
    ```bash
    npm install @capacitor/core @capacitor/cli
    npx cap init
    npx cap add android
    ```
2.  **Konfigurasi**: Sesuaikan `server.url` di file `capacitor.config.ts` agar mengarah ke alamat server produksi atau IP komputer dev lokal Anda (misal `http://10.0.2.2:8000`).
3.  **Sinkronisasi & Buka Android Studio**:
    ```bash
    npm run build
    npx cap sync
    npx cap open android
    ```

---

## ⚙️ Variabel Lingkungan (.env)
Berikut variabel khusus CCTV yang dapat Anda konfigurasi di `.env`:

| Variabel | Deskripsi | Default |
|---|---|---|
| `CCTV_GO2RTC_HOST` | Host address server go2rtc | `localhost` |
| `CCTV_GO2RTC_PORT` | Port REST API server go2rtc | `1984` |
| `CCTV_HEALTH_CHECK_INTERVAL` | Interval cron job pengecekan status kamera (menit) | `5` |
| `CCTV_DEFAULT_TAX_RATE` | Tarif PPN bawaan untuk invoice/quotation (%) | `12` |
| `CCTV_INVOICE_PREFIX` | Awalan nomor invoice tagihan | `INV` |
| `CCTV_QUOTATION_PREFIX` | Awalan nomor penawaran harga | `QUO` |
