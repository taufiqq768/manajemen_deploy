<!DOCTYPE html>
<html>
<head>
    <title>WAHA Connection Down Alert</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <div style="max-w-md mx-auto p-4 border border-red-200 rounded-lg bg-red-50">
        <h2 style="color: #dc2626;">🚨 Peringatan: Koneksi WAHA Terputus</h2>
        
        <p>Halo Administrator,</p>
        
        <p>Sistem monitoring kami mendeteksi bahwa koneksi ke layanan WhatsApp API (WAHA) saat ini sedang mengalami gangguan atau terputus.</p>
        
        <div style="background-color: #fff; padding: 15px; border-left: 4px solid #ef4444; margin: 20px 0;">
            <p style="margin: 0 0 10px 0;"><strong>URL WAHA:</strong> {{ $wahaUrl }}</p>
            <p style="margin: 0 0 10px 0;"><strong>Status Terdeteksi:</strong> {{ $errorStatus }}</p>
            <p style="margin: 0;"><strong>Pesan Error:</strong> {{ $errorMessage ?? 'Tidak ada detail pesan error.' }}</p>
        </div>

        <p>Harap segera memeriksa server WAHA Anda atau log sistem untuk menindaklanjuti masalah ini, karena notifikasi WhatsApp sistem kemungkinan besar akan tertunda atau gagal dikirim.</p>
        
        <p style="margin-top: 30px;">
            <a href="{{ url('/waha-connection') }}" style="display: inline-block; padding: 10px 20px; background-color: #4f46e5; color: #fff; text-decoration: none; border-radius: 5px;">Cek Dashboard Monitoring</a>
        </p>

        <hr style="border: 0; border-top: 1px solid #ddd; margin-top: 30px; margin-bottom: 20px;">
        <p style="font-size: 12px; color: #777;">Email ini dikirim secara otomatis oleh Sistem Manajemen Deploy. Mohon untuk tidak membalas email ini.</p>
    </div>
</body>
</html>
