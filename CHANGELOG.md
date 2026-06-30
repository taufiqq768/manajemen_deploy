# Changelog - Deploy Manager

## [Unreleased]

### Added
- **Multi-Select PIC (Programmer)**:
  - Relasi pivot `application_user` (many-to-many) antara tabel `applications` dan `users`.
  - Tampilan modal Edit menggunakan Tom Select untuk pemilih PIC multipel secara dinamis.
  - Integrasi PIC di halaman pengajuan Deploy Request secara otomatis.
- **Integrasi Versi Aplikasi via API**:
  - Kolom baru `version_api_get`, `version_api_write`, dan `version_api_key` pada tabel `applications` untuk menyimpan konfigurasi API versi.
  - Penanda read-only di form edit data aplikasi yang ditarik secara otomatis dari API HCIS (nama dan alamat/URL live terkunci).
  - Fitur **Refresh Versi AJAX**: Tombol sync di samping header tabel utama untuk memicu pembaruan versi seluruh aplikasi melalui background fetch menggunakan AJAX spinner.
  - Tombol **"Tes Get Versi"** di pojok kiri bawah modal konfigurasi API untuk menguji koneksi & parser JSON key secara real-time sebelum disimpan.
  - Fitur deteksi ketidakcocokan key field JSON / respon kosong saat uji coba maupun refresh berkala untuk mencegah string JSON mentah disimpan di database.
  - Validasi unsaved changes: Menampilkan peringatan konfirmasi jika pengguna mencoba menutup modal setelah melakukan perubahan input tanpa menyimpannya terlebih dahulu.
  - Penyimpanan konfigurasi modal API Versi secara AJAX tanpa full page reload.
- **Sistem Versi Otomatis (Semantic Bumping)**:
  - Form input `version` (Versi / Release) kini di-set sebagai *read-only* baik pada pembuatan maupun revisi request.
  - Checklist pilihan ganda untuk **Jenis Request**: *Perubahan Besar*, *Perubahan Kecil*, dan *Bug Fixing* lengkap dengan tooltip penjelasan interaktif.
  - Perhitungan nomor rilis baru format `x.y.z` secara otomatis (real-time via JS) berbasis pilihan jenis request (Perubahan Besar menambah `x`, Perubahan Kecil menambah `y`, Bug Fixing menambah `z`).
  - Penyelarasan format badge status kategori request di halaman list dan detail request deploy.
- **Integrasi API Write (Update Versi Otomatis)**:
  - Kolom konfigurasi parameter payload `version_api_write_key` (default: `version`) dan `version_api_write_notes_key` (default: `release_notes`) di modal konfigurasi API Versi.
  - Trigger otomatis pengiriman post request ke API Write aplikasi terpilih membawa payload versi & release notes baru begitu Project Manager menekan tombol **Approve**.
  - Pengecekan status filter jenis request menggunakan `whereJsonContains` untuk mendukung format kolom `jenis` berupa array.
- **Keamanan Skema Migrasi**:
  - Seluruh file migrasi database diperkuat dengan pengecekan pra-eksekusi (`Schema::hasTable` & `Schema::hasColumn`) untuk mencegah crash saat dijalankan ulang.
