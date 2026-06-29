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

---

## 📱 REST API untuk Integrasi Mobile (Flutter)

Sistem ini menyediakan REST API produksi untuk dikonsumsi oleh aplikasi mobile (Flutter) dengan rate-limiting, otentikasi aman via Laravel Sanctum, caching performa tinggi berbasis Redis, dan proxy go2rtc untuk privasi IP kamera.

### 🔑 1. Otentikasi Sanctum & Keamanan Media Stream
*   **Login**: Kirim email dan password ke `POST /api/auth/login` untuk mendapatkan Bearer Token.
*   **Header Authorization**: Kirim header `Authorization: Bearer <token>` pada setiap request.
*   **Otentikasi via Query String**: Untuk pemutar video bawaan (native video players) di mobile yang sulit mengirimkan header, Anda dapat menggunakan query parameter `?token=<token>` pada endpoint video stream.

---

### 📡 2. Daftar Endpoint REST API

Semua endpoint dilindungi oleh `auth:sanctum` kecuali endpoint Login.

#### A. Otentikasi
*   `POST /api/auth/login` — Login admin & dapatkan Bearer Token.
*   `POST /api/auth/logout` — Hapus token saat ini.
*   `GET /api/auth/me` — Profil user saat ini.

#### B. Kamera CCTV & Streaming Proxy
*   `GET /api/kamera` — Daftar kamera (bisa difilter `area_id` atau `location_id` dan `status`).
*   `GET /api/kamera/{id}` — Detail kamera.
*   `GET /api/kamera/status-summary` — Agregasi jumlah kamera online, offline, total per area.
*   `PATCH /api/kamera/{id}/status` — Update status kamera manual (`online`, `offline`, `maintenance`).
*   `GET /api/kamera/{id}/stream-info` — Dapatkan URL stream WebRTC & HTML proxy aman.
*   `GET /api/kamera/{id}/stream` — Proxy player HTML go2rtc (menyembunyikan port/IP asli).
*   `POST /api/kamera/{id}/webrtc` — Proxy handshake WebRTC SDP.

#### C. Klien & Lokasi (Area)
*   `GET /api/klien` — Daftar klien (pencarian & paginasi).
*   `GET /api/klien/{id}` — Detail klien.
*   `GET /api/klien/{id}/lokasi` — Lokasi yang dimiliki klien tersebut.
*   `GET /api/area` — Daftar semua area dengan jumlah kamera per area.
*   `GET /api/area/{id}/kamera` — Daftar kamera di area tertentu.

#### D. Monitoring Kesehatan Kamera (Redis Cached)
*   `GET /api/monitoring/live` — Status kamera real-time (Cached di Redis, TTL 30 detik). *Rate Limit: 120 req/menit.*
*   `GET /api/monitoring/history/{kamera_id}` — Riwayat status up/down kamera (paginasi).
*   `GET /api/monitoring/alert` — Kamera yang baru saja offline (dalam 1 jam terakhir).

#### E. Inventaris (Stok Peralatan)
*   `GET /api/inventaris` — Daftar barang (pencarian, paginasi, filter kategori).
*   `GET /api/inventaris/{id}` — Detail barang.
*   `GET /api/inventaris/stok-menipis` — Barang dengan stok di bawah minimum.

#### F. Keuangan (Invoice & Penawaran)
*   `GET /api/invoice` — Daftar invoice (filter status, bulan, tahun).
*   `GET /api/invoice/{id}` — Detail invoice & rincian barang.
*   `GET /api/invoice/statistik` — Statistik tagihan, pembayaran, outstanding bulan ini.
*   `GET /api/quotation` — Daftar penawaran harga.
*   `GET /api/quotation/{id}` — Detail penawaran harga & rincian barang.

#### G. Laporan & Dashboard
*   `GET /api/dashboard` — Ringkasan statistik (total kamera, online, alerts, nilai aset, revenue bulan ini).
*   `GET /api/laporan/kamera-uptime` — Laporan persentase uptime dan status kamera.
*   `GET /api/laporan/keuangan` — Laporan keuangan bulanan (billing, paid, outstanding).
*   `GET /api/laporan/inventaris` — Laporan aset inventaris (total SKU, nilai aset, nilai retail, laba kotor).

---

### 📊 3. Format Response Standard

Semua response dari API memiliki format JSON yang konsisten.

#### Response Sukses (HTTP 200/201)
```json
{
  "status": true,
  "message": "Daftar klien berhasil diambil.",
  "data": [
    {
      "id": 1,
      "name": "Budi Santoso",
      "company": "PT. Sinar Mas Utama"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

#### Response Error (HTTP 401/422/404)
```json
{
  "status": false,
  "message": "The email field is required. (and 1 more error)",
  "errors": {
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password field is required."
    ]
  }
}
```
