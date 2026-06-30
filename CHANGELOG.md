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
- **Keamanan Skema Migrasi**:
  - Seluruh file migrasi database diperkuat dengan pengecekan pra-eksekusi (`Schema::hasTable` & `Schema::hasColumn`) untuk mencegah crash saat dijalankan ulang.
