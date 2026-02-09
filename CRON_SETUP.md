# Setup Cron Job untuk Auto-Generate Daily Summary

## Linux / macOS

1. Buka crontab editor:
```bash
crontab -e
```

2. Tambahkan baris berikut (jalankan setiap hari pukul 23:59):
```bash
59 23 * * * cd /path/to/absensi-siswa/backend && php spark attendance:generate-summary >> /tmp/attendance-cron.log 2>&1
```

3. Atau jalankan setiap pagi pukul 00:05 untuk proses data hari sebelumnya:
```bash
5 0 * * * cd /path/to/absensi-siswa/backend && php spark attendance:generate-summary $(date -d "yesterday" +\%Y-\%m-\%d) >> /tmp/attendance-cron.log 2>&1
```

## Windows (Task Scheduler)

1. Buka Task Scheduler
2. Create Basic Task
3. Trigger: Daily at 23:59
4. Action: Start a program
   - Program: `C:\php\php.exe`
   - Arguments: `spark attendance:generate-summary`
   - Start in: `C:\path\to\absensi-siswa\backend`

## Manual Run

Untuk test atau run manual:

```bash
# Generate untuk hari ini
php spark attendance:generate-summary

# Generate untuk tanggal spesifik
php spark attendance:generate-summary 2026-02-07
```

## Fungsi Command

Command ini akan:
1. Ambil semua siswa yang aktif
2. Proses attendance log untuk setiap siswa
3. Filter double scan (ambil scan pertama & terakhir)
4. Hitung keterlambatan berdasarkan shift
5. Update/insert ke `attendance_summaries`
6. Cek exception (sakit/izin) dan override status jika ada

## Monitoring

Cek log hasil eksekusi:
```bash
# Linux/Mac
tail -f /tmp/attendance-cron.log

# Windows
# Lihat di Task Scheduler → History
```

## Alternatif: Service/Daemon

Untuk yang lebih advanced, bisa buat service yang running 24/7:

```bash
# Jalankan setiap 1 jam untuk auto-process
*/60 * * * * cd /path/to/absensi-siswa/backend && php spark attendance:generate-summary
```

Atau buat dengan Supervisor (Linux):

```ini
[program:attendance-processor]
command=php /path/to/backend/spark attendance:generate-summary
directory=/path/to/backend
autostart=true
autorestart=true
user=www-data
stdout_logfile=/var/log/attendance.log
stderr_logfile=/var/log/attendance-error.log
```
