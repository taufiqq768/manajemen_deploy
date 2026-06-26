# IT Work Hub — Dokumentasi Aplikasi

**Divisi IT · PTPN**
**Versi:** 1.0.0 (Fase Awal)
**Tanggal Dokumen:** Juni 2026

---

## Daftar Isi

1. [Gambaran Umum](#1-gambaran-umum)
2. [Struktur Menu & Navigasi](#2-struktur-menu--navigasi)
3. [Modul App Dev](#3-modul-app-dev)
   - 3.1 [Longlist Project](#31-longlist-project)
   - 3.2 [Tambah Project](#32-tambah-project)
   - 3.3 [Detail Project](#33-detail-project)
   - 3.4 [Detail Aktivitas](#34-detail-aktivitas)
   - 3.5 [List Fitur Awal](#35-list-fitur-awal)
   - 3.6 [Change Request (CR)](#36-change-request-cr)
   - 3.7 [Bugs / Error / Note](#37-bugs--error--note)
4. [Modul Dashboard](#4-modul-dashboard)
5. [Repository Dokumen](#5-repository-dokumen)
6. [Referensi Nilai & Enum](#6-referensi-nilai--enum)
7. [Alur Navigasi (User Flow)](#7-alur-navigasi-user-flow)
8. [Desain & Komponen UI](#8-desain--komponen-ui)
9. [Rencana Pengembangan](#9-rencana-pengembangan)

---

## 1. Gambaran Umum

**IT Work Hub** adalah aplikasi web internal milik Divisi IT PTPN yang berfungsi sebagai pusat manajemen dan pemantauan seluruh project IT. Aplikasi ini dirancang untuk mempermudah tracking status project, pengelolaan fitur, change request, serta pencatatan bug/error secara terpusat dan terstruktur.

### Tujuan Aplikasi
- Menyediakan satu sumber informasi (single source of truth) untuk seluruh project IT
- Memudahkan pemantauan status dan progres project secara real-time
- Mendokumentasikan aktivitas pengembangan: fitur, CR, dan bug
- Menyimpan dokumen pendukung tiap project secara terorganisir

### Teknologi yang Direncanakan
- **Backend:** Laravel (PHP)
- **Frontend:** Blade + Tailwind CSS
- **Database:** MySQL / BigQuery
- **Icon:** Tabler Icons
- **Font:** Inter (Google Fonts)

---

## 2. Struktur Menu & Navigasi

Aplikasi menggunakan layout **sidebar + main content**. Sidebar bersifat tetap dan berisi navigasi utama.

```
IT Work Hub
├── App Dev            ← Fase 1 (aktif)
├── Dashboard          ← Fase 1 (aktif, placeholder)
└── Repository Doc     ← Fitur pendukung
```

### Sidebar
| Item Menu       | Icon                  | Status    |
|-----------------|-----------------------|-----------|
| App Dev         | `ti-code`             | Aktif     |
| Dashboard       | `ti-chart-bar`        | Aktif     |
| Repository Doc  | `ti-file-text`        | Aktif     |
| Pengaturan      | `ti-settings`         | Fase 2    |

---

## 3. Modul App Dev

Modul utama untuk mengelola project pengembangan aplikasi IT.

---

### 3.1 Longlist Project

Halaman utama App Dev yang menampilkan seluruh daftar project dalam bentuk tabel.

#### Summary Card (Stat Cards)

Ditampilkan di atas tabel, berisi ringkasan jumlah project berdasarkan status:

| Card           | Keterangan                          |
|----------------|-------------------------------------|
| Total Project  | Jumlah seluruh project              |
| Not Started    | Project belum dimulai               |
| Live           | Project sudah go-live               |
| Live with CR   | Live namun sedang ada CR aktif      |
| Live with Bug  | Live namun terdapat bug aktif       |
| Hold           | Project ditangguhkan sementara      |
| Retired        | Project sudah tidak aktif/dihentikan|

#### Kolom Tabel Longlist

| Kolom             | Tipe      | Keterangan                              |
|-------------------|-----------|-----------------------------------------|
| #                 | Integer   | Nomor urut                              |
| Nama Project      | String    | Nama project IT                         |
| Uraian Singkat    | String    | Deskripsi singkat project               |
| Priority          | Enum      | High / Medium / Low                     |
| Squad / Tim       | String    | Nama tim yang mengerjakan               |
| Status            | Enum      | Status project (lihat §6)               |
| % (Progress)      | Integer   | Persentase penyelesaian (0–100)         |
| BPO               | String    | Business Process Owner / divisi         |
| Aksi              | Button    | Tombol "Detail →" untuk masuk ke detail |

---

### 3.2 Tambah Project

Form untuk menambahkan project baru ke dalam sistem.

**Akses:** Tombol "Tambah Project" di halaman Longlist

#### Field Form

**Informasi Umum**

| Field             | Tipe        | Keterangan                              |
|-------------------|-------------|-----------------------------------------|
| Nama Project      | Text        | Wajib diisi                             |
| Uraian Singkat    | Textarea    | Deskripsi ringkas tujuan project        |
| Priority          | Dropdown    | High / Medium / Low                     |
| Squad / Tim       | Text        | Nama tim pelaksana                      |
| Status            | Dropdown    | Status awal project (lihat §6)          |
| Persentase (%)    | Number      | 0–100, progress awal project            |
| BPO               | Text        | Business Process Owner / nama divisi    |
| BRD               | Text        | Nomor dokumen BRD                       |

**Pain Point**

| Field             | Tipe     | Keterangan                              |
|-------------------|----------|-----------------------------------------|
| Uraian Pain Point | Textarea | Deskripsi masalah yang melatarbelakangi |
| Impact            | Text     | Dampak dari pain point tersebut         |
| Tanggal           | Date     | Tanggal identifikasi pain point         |
| PIC Pain Point    | Text     | Penanggung jawab                        |
| Flow CR           | Text     | Nomor CR terkait (jika ada)             |

---

### 3.3 Detail Project

Halaman view/edit informasi lengkap sebuah project. Dapat diakses dengan mengklik "Detail →" pada baris tabel Longlist.

#### Konten Halaman

Halaman ini terdiri dari beberapa seksi:

**1. Informasi Project**
Field yang sama dengan form Tambah Project (dapat diedit).

**2. Pain Point**
Detail pain point yang melatarbelakangi project.

**3. Post Implementation Review**

| Field             | Tipe     | Keterangan                              |
|-------------------|----------|-----------------------------------------|
| Uraian Review     | Textarea | Catatan hasil review pasca implementasi |
| Tanggal Review    | Date     | Tanggal pelaksanaan review              |
| Link Dokumentasi  | Text/URL | Tautan dokumen hasil review             |

**4. Dokumen Pendukung**

Tabel daftar dokumen yang terlampir pada project:

| Kolom         | Keterangan                     |
|---------------|--------------------------------|
| Nama Dokumen  | Nama file/dokumen              |
| Uraian        | Keterangan singkat dokumen     |
| Tanggal       | Tanggal upload                 |
| Link          | Tautan/URL dokumen             |

Terdapat tombol **"Tambah Dokumen"** untuk melampirkan dokumen baru.

**Tombol Aksi**
- **Simpan Perubahan** — menyimpan seluruh perubahan data project
- **Detail Aktivitas** — navigasi ke halaman Detail Aktivitas (3 tab)

---

### 3.4 Detail Aktivitas

Halaman yang menampilkan rincian aktivitas pengembangan project, diakses dari tombol **"Detail Aktivitas"** di halaman Detail Project.

Halaman ini terdiri dari **3 tab**:

| Tab                | Keterangan                                      |
|--------------------|-------------------------------------------------|
| List Fitur Awal    | Daftar fitur yang dikerjakan sejak awal project |
| Change Request     | Daftar permintaan perubahan/tambahan fitur      |
| Bugs / Error / Note| Daftar bug, error, dan catatan teknis           |

---

### 3.5 List Fitur Awal

Tab pertama di halaman Detail Aktivitas. Berisi daftar fitur yang direncanakan dan dikerjakan sejak awal project.

#### Kolom Tabel

| Kolom                | Tipe   | Keterangan                              |
|----------------------|--------|-----------------------------------------|
| #                    | Integer| Nomor urut                              |
| Nama Fitur           | String | Nama fitur yang dikerjakan              |
| Tanggal              | Date   | Tanggal mulai pengerjaan fitur          |
| Flow CR              | String | Nomor CR terkait (jika ada)             |
| Deadline             | Date   | Target penyelesaian awal                |
| Deadline Penyesuaian | Date   | Deadline yang disesuaikan (jika direvisi)|
| PIC                  | String | Penanggung jawab fitur                  |
| Status               | Enum   | Status pengerjaan (lihat §6 — Status Aktivitas)|

#### Form Tambah Fitur

Diakses melalui tombol **"Tambah Fitur"**. Field:

| Field                | Tipe     | Keterangan              |
|----------------------|----------|-------------------------|
| Nama Fitur           | Text     | Wajib diisi             |
| Tanggal              | Date     |                         |
| Flow CR              | Text     | Nomor CR (opsional)     |
| Deadline             | Date     |                         |
| Deadline Penyesuaian | Date     | Opsional                |
| PIC                  | Text     | Penanggung jawab        |
| Status               | Dropdown | Status aktivitas        |
| Keterangan           | Textarea | Catatan tambahan        |

---

### 3.6 Change Request (CR)

Tab kedua di halaman Detail Aktivitas. Berisi daftar permintaan perubahan atau tambahan fitur dari stakeholder setelah project berjalan.

#### Kolom Tabel

| Kolom                | Tipe   | Keterangan                              |
|----------------------|--------|-----------------------------------------|
| #                    | Integer| Nomor urut                              |
| Fitur CR             | String | Nama fitur/perubahan yang diminta       |
| Tanggal              | Date   | Tanggal CR masuk                        |
| Deadline             | Date   | Target penyelesaian CR                  |
| Deadline Penyesuaian | Date   | Deadline revisi (jika ada)              |
| Keterangan           | String | Catatan/alasan CR                       |
| PIC                  | String | Penanggung jawab                        |
| Status               | Enum   | Status pengerjaan CR (lihat §6)         |

---

### 3.7 Bugs / Error / Note

Tab ketiga di halaman Detail Aktivitas. Berisi pencatatan bug, error teknis, maupun catatan (note) penting terkait project.

#### Kolom Tabel

| Kolom                | Tipe   | Keterangan                                      |
|----------------------|--------|-------------------------------------------------|
| #                    | Integer| Nomor urut                                      |
| Deskripsi            | String | Deskripsi bug/error/catatan                     |
| Jenis                | Enum   | Bug / Note                                      |
| Tanggal              | Date   | Tanggal ditemukan/dicatat                       |
| Deadline             | Date   | Target penyelesaian                             |
| Deadline Penyesuaian | Date   | Deadline revisi (jika ada)                      |
| PIC                  | String | Penanggung jawab                                |
| Status               | Enum   | Status penanganan (lihat §6)                    |

---

## 4. Modul Dashboard

**Status: Fase Berikutnya (Placeholder)**

Modul Dashboard direncanakan untuk menampilkan visualisasi data dan statistik project secara keseluruhan, seperti:
- Grafik distribusi status project
- Trend progress project per periode
- Ringkasan CR dan bug aktif
- Rekapitulasi per squad/tim

---

## 5. Repository Dokumen

Halaman untuk menyimpan dan mengelola dokumen-dokumen umum Divisi IT yang tidak terikat pada satu project tertentu.

#### Kolom Tabel

| Kolom         | Keterangan                                   |
|---------------|----------------------------------------------|
| Nama Dokumen  | Nama file/dokumen                            |
| Jenis         | Jenis dokumen (BRD, SRS, MOM, dll.)          |
| Versi         | Versi dokumen (v1.0, v2.1, dst.)             |
| Tanggal       | Tanggal upload/publish                       |
| Aksi          | Tombol unduh dokumen                         |

---

## 6. Referensi Nilai & Enum

### Status Project (Longlist)

Digunakan pada kolom Status di tabel Longlist dan form Detail Project.

| Nilai         | Warna Badge | Keterangan                                      |
|---------------|-------------|-------------------------------------------------|
| Not Started   | Abu-abu     | Project belum dimulai                           |
| Live          | Hijau       | Project sudah go-live dan berjalan normal       |
| Live with CR  | Ungu        | Live namun sedang terdapat Change Request aktif |
| Live with Bug | Amber/Kuning| Live namun terdapat bug yang belum terselesaikan|
| Hold          | Abu-abu     | Project ditangguhkan sementara waktu            |
| Retired       | Merah       | Project sudah dihentikan / tidak aktif          |

### Status Aktivitas (Fitur, CR, Bugs)

Digunakan pada kolom Status di semua sub-tabel Detail Aktivitas.

| Nilai          | Warna Badge | Keterangan                               |
|----------------|-------------|------------------------------------------|
| Not Started    | Abu-abu     | Belum dikerjakan                         |
| Ureq Analysis  | Biru muda   | Sedang dalam analisis kebutuhan          |
| Programming    | Biru        | Sedang dalam tahap coding                |
| Tech Testing   | Biru tua    | Sedang dalam pengujian teknis            |
| UAT            | Amber       | Sedang dalam User Acceptance Testing     |
| ST             | Teal        | Sedang dalam System Testing              |
| Done           | Hijau       | Selesai                                  |

### Priority Project

| Nilai   | Warna Badge | Keterangan                 |
|---------|-------------|----------------------------|
| High    | Merah       | Prioritas tinggi           |
| Medium  | Amber       | Prioritas menengah         |
| Low     | Abu-abu     | Prioritas rendah           |

### Jenis Bugs/Error/Note

| Nilai | Warna Badge | Keterangan                      |
|-------|-------------|---------------------------------|
| Bug   | Merah       | Bug atau error teknis           |
| Note  | Abu-abu     | Catatan teknis / informasi      |

---

## 7. Alur Navigasi (User Flow)

```
[Sidebar: App Dev]
       │
       ▼
[Longlist Project]  ──── [Tambah Project] ──► [Form Tambah Project]
       │
       │ klik "Detail →"
       ▼
[Detail Project]
├── Form: Nama, Uraian, Priority, Squad, Status, %, BPO, BRD
├── Seksi: Pain Point
├── Seksi: Post Implementation Review
├── Seksi: Dokumen Pendukung
└── Tombol: [Detail Aktivitas]
                │
                ▼
        [Detail Aktivitas]
        ├── Tab: List Fitur Awal
        │     ├── Tabel Fitur
        │     └── [Tambah Fitur] ──► [Form Tambah Fitur]
        ├── Tab: Change Request
        │     ├── Tabel CR
        │     └── [Tambah CR] ──► [Form Tambah CR]
        └── Tab: Bugs / Error / Note
              ├── Tabel Bugs
              └── [Tambah Bug] ──► [Form Tambah Bug]
```

---

## 8. Desain & Komponen UI

### Palet Warna Utama

| Variabel           | Nilai Hex  | Kegunaan                         |
|--------------------|------------|----------------------------------|
| `--color-bg`       | `#F5F5F3`  | Background halaman               |
| `--color-surface`  | `#FFFFFF`  | Background card / sidebar        |
| `--color-surface-2`| `#F1EFE8`  | Background tabel header / hover  |
| `--green`          | `#639922`  | Warna aksen utama (tombol, aktif)|
| `--green-dark`     | `#3B6D11`  | Hover tombol / teks aktif        |
| `--green-light`    | `#EAF3DE`  | Background nav item aktif        |

### Komponen Utama

| Komponen       | Kelas CSS          | Keterangan                              |
|----------------|--------------------|-----------------------------------------|
| Tombol Utama   | `.btn.btn-primary` | Latar hijau, aksi utama                 |
| Tombol Default | `.btn`             | Latar putih, aksi sekunder              |
| Tombol Kecil   | `.btn.btn-sm`      | Ukuran lebih kecil, di dalam tabel      |
| Tombol Kembali | `.back-btn`        | Navigasi ke halaman sebelumnya          |
| Badge Status   | `.badge.badge-*`   | Indikator status berwarna               |
| Card           | `.card`            | Container dengan border dan radius      |
| Tabel          | `table`            | Tabel data standar                      |
| Form Grid      | `.form-grid`       | 2 kolom, responsif dengan `.full`       |
| Tabs           | `.tabs > .tab`     | Tab navigasi sub-halaman                |

### Dependensi Eksternal

| Library           | Versi    | Sumber                          | Kegunaan        |
|-------------------|----------|---------------------------------|-----------------|
| Inter Font        | Latest   | Google Fonts                    | Tipografi utama |
| Tabler Icons      | 3.19.0   | cdn.jsdelivr.net                | Icon UI         |

---

## 9. Rencana Pengembangan

### Fase 1 (Saat Ini)
- [x] Layout sidebar + navigasi utama
- [x] Modul App Dev — Longlist Project
- [x] Modul App Dev — Tambah Project
- [x] Modul App Dev — Detail Project
- [x] Modul App Dev — Detail Aktivitas (3 tab)
- [x] Repository Dokumen (basic)
- [x] Dashboard placeholder

### Fase 2 (Berikutnya)
- [ ] Integrasi database (Laravel + MySQL)
- [ ] Autentikasi user (login, role)
- [ ] Dashboard dengan grafik & statistik
- [ ] Modul Non-App (project non-pengembangan)
- [ ] Notifikasi deadline
- [ ] Filter & pencarian di tabel
- [ ] Export data ke Excel / PDF
- [ ] Audit log perubahan data

### Fase 3 (Jangka Panjang)
- [ ] Integrasi dengan sistem PTPN lain
- [ ] Mobile-responsive view
- [ ] API untuk integrasi eksternal
- [ ] Dashboard eksekutif (multi-level)

---

*Dokumen ini dibuat berdasarkan mockup UI IT Work Hub versi 1.0.0*
*Divisi IT · PTPN · Juni 2026*
