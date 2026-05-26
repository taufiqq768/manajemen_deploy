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
4. **File Upload Secure**: Sistem mengizinkan dan memvalidasi tipe *file* spesifik (PDF, DOC, DOCX, JPG, PNG) untuk keperluan dokumen administratif rilis.
5. **Dark Mode & Responsive UI**: Dibangun dengan *Tailwind CSS*, mendukung pergantian tema Terang/Gelap dan mulus dibuka di perangkat seluler (*mobile-friendly*).
6. **Sistem Terisolasi untuk Admin**: Keamanan terjaga karena profil *user* dan *password* dikunci dan dikendalikan sepenuhnya oleh Administrator.

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
*Dokumentasi ini dibuat untuk mempermudah serah terima sistem, pemeliharaan (maintenance), dan pengenalan bagi pengguna baru.*
