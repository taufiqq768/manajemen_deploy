# Deploy Manager – Sistem Manajemen Rilis Aplikasi

**Deploy Manager** adalah aplikasi berbasis web yang dirancang khusus untuk memanajemen pengajuan, peninjauan, dan persetujuan rilis (*deploy*) aplikasi ke *environment production*. Aplikasi ini menjembatani komunikasi antara *Programmer* dan *Project Manager* dalam sebuah alur persetujuan (*approval workflow*) yang terstruktur, rapi, dan terdokumentasi.

---

## 👥 Hak Akses dan Role

Aplikasi ini memiliki 3 level pengguna utama (*role-based access control*):

### 1. Programmer
- **Tugas Utama**: Mengajukan *request deploy* untuk aplikasi yang akan dirilis.
- **Hak Akses**:
  - Mengakses *Dashboard* (melihat statistik *request* pribadi).
  - Membuat *request deploy* baru lengkap dengan *Release Notes* dan dokumen pendukung.
  - Membaca (*View*), mengubah (*Edit*), atau menghapus (*Delete*) *request* **milik sendiri** yang masih berstatus **Pending**.
  - Mengubah *password* pribadi via menu Profil.

### 2. Project Manager (PM)
- **Tugas Utama**: Meninjau (*review*) dan memberikan keputusan (Setuju/Tolak) terhadap pengajuan *deploy*.
- **Hak Akses**:
  - Mengakses *Dashboard* (melihat rekap statistik seluruh *request* di perusahaan).
  - Melihat seluruh *request deploy* dari semua *Programmer*.
  - Menyetujui (*Approve*) atau Menolak (*Reject*) pengajuan beserta pencantuman alasannya.
  - Mengelola daftar **Aplikasi** (Tambah/Edit/Hapus data aplikasi di Master Data).
  - Mengubah *password* pribadi via menu Profil.

### 3. Administrator
- **Tugas Utama**: Mengelola akses pengguna di dalam sistem.
- **Hak Akses**:
  - Tidak memiliki akses ke menu pengajuan *deploy* maupun daftar aplikasi.
  - Saat *login*, otomatis diarahkan ke menu **Manajemen User**.
  - Menambahkan *user* baru (menentukan NIK, Email, dan Role).
  - Mengubah data *user* yang sudah ada.
  - Melakukan **Reset Password** paksa terhadap *user* yang lupa sandinya.

---

## 🔄 Alur Kerja (Workflow) Sistem

Berikut adalah skenario standar atau alur kerja (*SOP*) di dalam aplikasi:

### Tahap 1: Pengajuan (Programmer)
1. *Programmer* selesai membuat fitur/perbaikan pada aplikasi.
2. *Programmer* masuk ke Deploy Manager dan menekan tombol **"Ajukan Deploy"**.
3. *Programmer* memilih Aplikasi, menginput Versi, *Release Notes*, Dampak Rilis, dan mengunggah dokumen persetujuan/lampiran (jika ada).
4. Status *request* akan otomatis menjadi **PENDING**.
5. **Notifikasi Terkirim**: Sistem akan mengirimkan pesan pemberitahuan otomatis melalui WhatsApp dan Email kepada seluruh *Project Manager* bahwa ada *request* baru.

### Tahap 2: Peninjauan (Project Manager)
1. *Project Manager* menerima notifikasi di WA atau Email dan mengklik *link* yang terlampir.
2. *Project Manager* membaca dengan teliti detail *request* (versi, dokumen, catatan rilis).
3. *Project Manager* mengambil keputusan:
   - **Tombol Approve (Setuju)**: Status berubah menjadi `Approved`. *Programmer* terkait akan mendapat notifikasi WhatsApp dan Email bahwa *deploy* boleh dijalankan.
   - **Tombol Reject (Tolak)**: *Project Manager* wajib mengisi alasan penolakan. Status berubah menjadi `Rejected`. *Programmer* terkait akan menerima notifikasi lengkap beserta alasannya.

### Tahap 3: Pelaksanaan (Di Luar Sistem)
- Jika status *Approved*, *Programmer* bisa melanjutkan eksekusi rilis kode (misal: eksekusi *pipeline* CI/CD, atau unggah manual) ke *server production*.
- Riwayat *request* yang sudah disetujui akan tersimpan permanen di aplikasi sebagai log audit/rekam jejak.

---

## 🚀 Fitur - Fitur Unggulan

1. **Dashboard Intuitif**: Menampilkan ringkasan informasi yang berbeda menyesuaikan dengan *role* pengguna (Programmer melihat statistik pribadi, PM melihat statistik global).
2. **Notifikasi Multi-Channel**:
   - **In-App**: Notifikasi lonceng di sudut kanan atas layar.
   - **Email**: Email elegan berformat HTML lengkap dengan status (warna merah/hijau) dan tombol CTA.
   - **WhatsApp (WAHA)**: Pesan teks rapi yang langsung terkirim ke nomor WA *user*.
3. **Sinkronisasi Aplikasi (API)**: PM dapat menekan tombol *Sync dari HCIS* untuk menarik daftar aplikasi secara otomatis dari API pusat, tanpa harus menginput manual.
4. **Manajemen PIC Multi-Select**: Setiap aplikasi dapat memiliki beberapa PIC (Programmer) yang dipilih menggunakan UI Tom Select (multi-select dropdown). PIC ini akan otomatis terhubung ke alur rilis/deploy request.
5. **Integrasi Versi API (GET & WRITE)**:
   - **API GET**: Pengambilan versi aplikasi secara otomatis melalui API GET internal masing-masing aplikasi. Mendukung key JSON dinamis (menggunakan dot-notation seperti `data.no_versi`), fitur AJAX refresh ber-spinner pada tabel utama, serta fitur interaktif **"Tes Get Versi"** di dalam modal untuk validasi real-time sebelum disimpan.
   - **API WRITE**: Pengiriman otomatis nomor versi terbaru dan catatan rilis (*release notes*) ke API Write eksternal aplikasi yang bersangkutan saat *Project Manager* menyetujui (*Approve*) pengajuan deploy. Key parameter payload JSON untuk data versi & catatan rilis dapat dikustomisasi secara fleksibel melalui modal konfigurasi API.
6. **Sistem Kenaikan Versi Otomatis (Semantic Bumping)**: Input nomor versi saat pengajuan atau revisi rilis dikunci (*read-only*) dan dihitung secara otomatis (real-time via JavaScript) berbasis pilihan kategori **Jenis Request**:
   - **Perubahan Besar**: Menaikkan angka pertama / Major version (`x.*.*`).
   - **Perubahan Kecil**: Menaikkan angka kedua / Minor version (`*.x.*`).
   - **Bug Fixing**: Menaikkan angka ketiga / Patch version (`*.*.x`).
   (Jika ketiganya dicentang sekaligus, maka seluruh bagian nomor rilis akan dinaikkan).
7. **Multi-Upload Dokumen Terkait & Pendukung Dinamis**: Sistem mendukung pengunggahan banyak dokumen secara dinamis dalam tata letak vertikal. Programmer dapat memasukkan Nomor Dokumen (opsional) beserta berkas fisik terkait (PDF, DOC, DOCX, JPG, PNG, TXT) secara berulang. Fitur dilengkapi tombol tambah/hapus dinamis, penomoran urut otomatis, serta pembersihan file yang dihapus di server secara otomatis.
8. **Dark Mode & Responsive UI**: Dibangun dengan *Tailwind CSS*, mendukung pergantian tema Terang/Gelap dan mulus dibuka di perangkat seluler (*mobile-friendly*).
9. **Sistem Terisolasi untuk Admin**: Keamanan terjaga karena profil *user* dan *password* dikunci dan dikendalikan sepenuhnya oleh Administrator.

---

## 🛠️ Stack Teknologi

- **Framework**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL / MariaDB
- **Frontend / Styling**: Blade Templating + Tailwind CSS + Alpine.js
- **Assets Bundler**: Vite
- **Integrasi Pihak Ketiga**:
  - **WAHA (WhatsApp HTTP API)**: Untuk *engine* notifikasi bot WhatsApp.
  - **Mailpit / SMTP Server**: Untuk pengiriman notifikasi via Email.

---

## 📝 Panduan Konfigurasi (Untuk IT / Sysadmin)

Jika Anda ingin melakukan pengaturan konfigurasi pada *server* (*file* `.env`), perhatikan variabel berikut:

### Konfigurasi Notifikasi WhatsApp
Pastikan Anda sudah menginstal *engine* WAHA (baik melalui Docker atau layanan lainnya).
```env
WAHA_URL=http://localhost:3000
WAHA_SESSION=default
WAHA_API_KEY=rahasia_api_key_anda (kosongkan jika WAHA tidak diproteksi)
```

### Konfigurasi Email (SMTP)
Atur sesuai dengan *credential* email perusahaan atau SMTP gratis seperti Gmail/Mailgun/Mailpit (lokal).
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=email_perusahaan@gmail.com
MAIL_PASSWORD=app_password_anda
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@namaperusahaan.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Format Pesan Kustom
- Jika ingin merubah kerangka *teks WhatsApp*, ubah pada `app/Services/NotificationService.php`.
- Jika ingin merubah warna dan format *tampilan Email*, ubah pada `resources/views/emails/deploy-notification.blade.php`.

---

## 🚢 Panduan Deploy ke Server Production

Ikuti langkah-langkah di bawah ini secara berurutan untuk men-*deploy* aplikasi ini dari tahap pengembangan (*development*) ke *server production* (VPS / Shared Hosting):

### 1. Persiapan File
- *Upload* seluruh kode aplikasi ke *server* Anda, atau gunakan Git (`git clone https://github.com/taufiqq768/manajemen_deploy.git`).
- Arahkan konfigurasi web *server* (Nginx/Apache) ke direktori `public` di dalam *folder* aplikasi ini.

### 2. Salin dan Atur File Lingkungan (Environment)
```bash
cp .env.example .env
```
- Buka *file* `.env` lalu sesuaikan *credential* Database, kredensial Mail, URL web (`APP_URL`), dan parameter lainnya.
- Ubah `APP_ENV=production` dan `APP_DEBUG=false` demi alasan keamanan.

### 3. Instalasi Dependency Backend (Composer)
Jalankan instalasi modul PHP tanpa mengikutsertakan modul *testing/dev*:
```bash
composer install --optimize-autoloader --no-dev
```

### 4. Build Aset Frontend (Node.js & Tailwind)
Karena aplikasi ini menggunakan Vite & Tailwind CSS, Anda **wajib** mengompilasi CSS dan JS agar desainnya berjalan baik di *production*.
```bash
npm install
npm run build
```
*(Langkah ini akan menghasilkan folder `public/build` yang berisi aset siap pakai).*

### 5. Generate Key & Atur Database
Buat kunci enkripsi aplikasi (jika belum) dan jalankan migrasi database.
```bash
php artisan key:generate
php artisan migrate --force
```
*(Opsional: Jika ini adalah server yang benar-benar baru dan kosong, Anda bisa menjalankan `php artisan db:seed --force` untuk memasukkan akun default).*

### 6. Hubungkan Folder Penyimpanan (Storage)
Sistem memiliki fitur lampiran *upload* dokumen pdf/gambar, jadi Anda **wajib** membuat *symlink*:
```bash
php artisan storage:link
```
*Pastikan konfigurasi `APP_URL` di `.env` sudah sesuai dengan alamat domain aplikasi Anda agar link download berfungsi dengan benar.*

### 7. Atur Izin Akses Folder (Permissions)
Server Linux butuh akses menulis (`write`) ke folder `storage` dan `bootstrap/cache`:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```
*(Ganti `www-data` sesuai dengan *user web server* Anda, misalnya `nginx` atau `apache`).*

### 8. Optimasi Performa Aplikasi
Langkah terakhir yang sangat penting untuk server *production* agar Laravel berjalan super cepat:
```bash
php artisan optimize
php artisan view:cache
```

Jika sewaktu-waktu Anda merubah isi `.env`, jangan lupa untuk me-reset *cache* ini dengan perintah `php artisan optimize:clear` lalu jalankan `php artisan optimize` kembali.

---
*Dokumentasi ini dibuat untuk mempermudah serah terima sistem, pemeliharaan (maintenance), dan pengenalan bagi pengguna baru.*
