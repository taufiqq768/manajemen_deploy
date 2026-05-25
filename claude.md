# Rancangan Aplikasi Deploy Management

## Gambaran Umum

Aplikasi web responsif untuk mengelola proses pengajuan dan persetujuan deploy aplikasi ke environment **Production**, dibangun dengan **Laravel (PHP)** dan **MySQL**.

---

## 1. Peran Pengguna (Role)

| Role | Akses |
|---|---|
| **Programmer (PIC)** | Mengajukan request deploy, melihat status request miliknya |
| **Project Manager** | Melihat semua request, menyetujui atau menolak deploy |

---

## 2. Fitur Aplikasi

### 2.1 Login
- Autentikasi email & password
- Session-based (Laravel Auth)
- Redirect sesuai role setelah login

### 2.2 Input Request Deploy (Programmer)
- Pilih aplikasi yang di-PIC-kan
- Isi versi/release yang akan di-deploy
- Isi release notes / deskripsi perubahan
- Tentukan jadwal deploy (scheduled_at)
- Submit → status otomatis **Pending**
- Notifikasi dikirim ke Project Manager (email + in-app)

### 2.3 Approved/Rejected (Project Manager)
- Lihat detail request
- Approve → status berubah ke **Approved**, notifikasi ke Programmer
- Reject → wajib isi alasan penolakan, notifikasi ke Programmer
- Programmer bisa revisi dan submit ulang

### 2.4 List Request
- **Programmer**: daftar request yang ia buat (filter by status)
- **Project Manager**: semua request masuk (filter by status, aplikasi, tanggal)
- Badge status: Pending (kuning), Approved (hijau), Rejected (merah)
- Klik untuk melihat detail

### 2.5 Notifikasi
- **In-app**: bell icon di navbar, ditandai belum dibaca
- **Email**: menggunakan Laravel Mail (SMTP/Mailtrap untuk dev)

---

## 3. Struktur Database

### Tabel: `users`
```sql
id              BIGINT PK AUTO_INCREMENT
nik             VARCHAR(100)
name            VARCHAR(100)
email           VARCHAR(150) UNIQUE
password        VARCHAR(255)
role            ENUM('programmer','project_manager')
email_verified_at TIMESTAMP NULL
remember_token  VARCHAR(100) NULL
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### Tabel: `applications`
```sql
id              BIGINT PK AUTO_INCREMENT
name            VARCHAR(100)
description     TEXT NULL
repo_url        VARCHAR(255) NULL
pic_user_id     BIGINT FK → users.id
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### Tabel: `deploy_requests`
```sql
id              BIGINT PK AUTO_INCREMENT
application_id  BIGINT FK → applications.id
requester_id    BIGINT FK → users.id   -- programmer
approver_id     BIGINT FK → users.id NULL -- project_manager
version         VARCHAR(50)
release_notes    TEXT
release_impact   TEXT
document_support TEXT
environment     ENUM('production') DEFAULT 'production'
status          ENUM('pending','approved','rejected') DEFAULT 'pending'
scheduled_at    TIMESTAMP NULL
approved_at     TIMESTAMP NULL
rejection_reason TEXT NULL
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### Tabel: `notifications`
```sql
id                 BIGINT PK AUTO_INCREMENT
user_id            BIGINT FK → users.id
deploy_request_id  BIGINT FK → deploy_requests.id
title              VARCHAR(200)
message            TEXT
type               ENUM('in_app','email')
is_read            BOOLEAN DEFAULT FALSE
created_at         TIMESTAMP
updated_at         TIMESTAMP
```

---

## 4. Struktur Folder Laravel

```
deploy-management/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── LoginController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── DeployRequestController.php
│   │   │   ├── NotificationController.php
│   │   │   └── ApplicationController.php
│   │   └── Middleware/
│   │       └── RoleMiddleware.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Application.php
│   │   ├── DeployRequest.php
│   │   └── Notification.php
│   ├── Mail/
│   │   ├── DeployRequestCreated.php   -- notif ke PM
│   │   └── DeployRequestDecided.php   -- notif ke programmer
│   └── Policies/
│       └── DeployRequestPolicy.php
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_create_users_table.php
│   │   ├── 2024_01_02_create_applications_table.php
│   │   ├── 2024_01_03_create_deploy_requests_table.php
│   │   └── 2024_01_04_create_notifications_table.php
│   └── seeders/
│       ├── UserSeeder.php
│       └── ApplicationSeeder.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php          -- layout utama + navbar
│       ├── auth/
│       │   └── login.blade.php
│       ├── dashboard/
│       │   └── index.blade.php
│       ├── deploy-requests/
│       │   ├── index.blade.php        -- list request
│       │   ├── create.blade.php       -- form buat request
│       │   ├── show.blade.php         -- detail request
│       │   └── review.blade.php       -- form approve/reject (PM)
│       └── notifications/
│           └── index.blade.php
└── routes/
    └── web.php
```

---

## 5. Routes (web.php)

```php
// Auth
Route::get('/login', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Deploy Requests
    Route::resource('deploy-requests', DeployRequestController::class);

    // Approve / Reject (khusus Project Manager)
    Route::middleware('role:project_manager')->group(function () {
        Route::post('/deploy-requests/{deployRequest}/approve', [DeployRequestController::class, 'approve'])->name('deploy-requests.approve');
        Route::post('/deploy-requests/{deployRequest}/reject',  [DeployRequestController::class, 'reject'])->name('deploy-requests.reject');
    });

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
});
```

---

## 6. Contoh Kode Kunci

### Model: DeployRequest.php
```php
class DeployRequest extends Model
{
    protected $fillable = [
        'application_id', 'requester_id', 'approver_id',
        'version', 'release_notes', 'environment',
        'status', 'scheduled_at', 'approved_at', 'rejection_reason',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'approved_at'  => 'datetime',
    ];

    public function application() { return $this->belongsTo(Application::class); }
    public function requester()   { return $this->belongsTo(User::class, 'requester_id'); }
    public function approver()    { return $this->belongsTo(User::class, 'approver_id'); }
    public function notifications(){ return $this->hasMany(Notification::class); }

    public function isPending()  { return $this->status === 'pending'; }
    public function isApproved() { return $this->status === 'approved'; }
    public function isRejected() { return $this->status === 'rejected'; }
}
```

### Controller: DeployRequestController.php (approve & reject)
```php
public function approve(DeployRequest $deployRequest)
{
    $this->authorize('decide', $deployRequest);

    $deployRequest->update([
        'status'      => 'approved',
        'approver_id' => auth()->id(),
        'approved_at' => now(),
    ]);

    // Notifikasi in-app
    Notification::create([
        'user_id'           => $deployRequest->requester_id,
        'deploy_request_id' => $deployRequest->id,
        'title'             => 'Deploy Disetujui',
        'message'           => "Request deploy {$deployRequest->application->name} v{$deployRequest->version} telah disetujui.",
        'type'              => 'in_app',
    ]);

    // Email
    Mail::to($deployRequest->requester->email)
        ->send(new DeployRequestDecided($deployRequest));

    return back()->with('success', 'Request deploy telah disetujui.');
}

public function reject(Request $request, DeployRequest $deployRequest)
{
    $this->authorize('decide', $deployRequest);
    $request->validate(['rejection_reason' => 'required|string|max:1000']);

    $deployRequest->update([
        'status'           => 'rejected',
        'approver_id'      => auth()->id(),
        'rejection_reason' => $request->rejection_reason,
    ]);

    Notification::create([
        'user_id'           => $deployRequest->requester_id,
        'deploy_request_id' => $deployRequest->id,
        'title'             => 'Deploy Ditolak',
        'message'           => "Request deploy {$deployRequest->application->name} v{$deployRequest->version} ditolak. Alasan: {$request->rejection_reason}",
        'type'              => 'in_app',
    ]);

    Mail::to($deployRequest->requester->email)
        ->send(new DeployRequestDecided($deployRequest));

    return back()->with('error', 'Request deploy telah ditolak.');
}
```

### Middleware: RoleMiddleware.php
```php
public function handle(Request $request, Closure $next, string $role)
{
    if (auth()->check() && auth()->user()->role === $role) {
        return $next($request);
    }
    abort(403, 'Akses ditolak.');
}
```

---

## 7. Tech Stack & Library

| Kebutuhan | Library/Tool |
|---|---|
| Framework | Laravel 11 |
| Template engine | Blade |
| CSS Framework | Tailwind CSS atau Bootstrap 5 |
| Auth | Laravel Breeze (custom role) |
| Notif Email | Laravel Mail + Mailtrap (dev) |
| Validasi | Laravel Form Request |
| Database | MySQL 8+ |
| Icon | Heroicons / FontAwesome |

---

## 8. Urutan Pengerjaan (Rekomendasi)

1. Setup project Laravel + auth + role middleware
2. Buat migrations & seeders (user dummy: 1 programmer, 1 PM)
3. Buat CRUD Applications (admin/PM bisa tambah aplikasi)
4. Buat fitur Deploy Request (form, list, detail)
5. Buat fitur Approve/Reject + notifikasi in-app
6. Integrasi notifikasi email (Laravel Mail)
7. Polish UI (responsif, badge status, navbar notification bell)
8. Testing & QA
