# Aplikasi Absensi Siswa dengan Fingerprint

Aplikasi manajemen kehadiran siswa terintegrasi dengan mesin fingerprint menggunakan CodeIgniter 4 dengan Tailwind CSS dan Material Icons 3.

## 📋 Fitur Utama

### 1. Panel Admin
- **Manajemen Mesin Fingerprint**
  - Tambah, edit, hapus mesin
  - Test koneksi mesin
  - Monitor status online/offline
  - Konfigurasi IP, Port, Comm Key

- **Mapping ID Mesin ke Siswa**
  - Hubungkan User ID di mesin dengan data siswa
  - Contoh: ID Mesin 105 = Budi Santoso

- **Pengaturan Jam Shift**
  - Tentukan batas waktu scan masuk & pulang
  - Toleransi keterlambatan
  - Multiple shift support

### 2. Panel Guru Piket
- **Monitoring Real-time**
  - Tampilan live siswa yang baru scan
  - Auto refresh setiap 5 detik
  - Notifikasi visual & suara (opsional)

- **Rekap Harian**
  - Statistik kehadiran per hari
  - Daftar siswa yang belum scan
  - Filter berdasarkan tanggal

- **Input Ketidakhadiran**
  - Input manual untuk Sakit (S)
  - Input manual untuk Izin (I)
  - Input untuk siswa yang Lupa Scan
  - Upload bukti surat keterangan

### 3. Panel Siswa & Orang Tua
- **Riwayat Kehadiran**
  - Lihat log scan masuk & pulang
  - Statistik kehadiran
  - Status: Hadir, Terlambat, Sakit, Izin, Alpha

- **Notifikasi**
  - Notifikasi otomatis saat siswa scan
  - Pesan ke orang tua via aplikasi
  - History notifikasi

## 🔧 Teknologi

- **Backend & Frontend:** CodeIgniter 4
- **CSS Framework:** Tailwind CSS 3.4
- **Icons:** Material Symbols (Google Material Icons 3)
- **Database:** MySQL
- **Fingerprint Device:** ZKTeco / ADMS Protocol

## 🚀 Instalasi

### Backend Setup

### Setup Aplikasi

1. Clone repository:
```bash
git clone [repository-url]
cd absensi-siswa
```

2. Install PHP dependencies:
```bash
composer install
```

3. Copy file environment:
```bash
cp env .env
```

4. Konfigurasi database di file `.env`:
```env
database.default.hostname = localhost
database.default.database = absensi_siswa
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
```

5. Buat database:
```bash
php spark db:create absensi_siswa
```

6. Jalankan migrations:
```bash
php spark migrate
```

7. (Opsional) Jalankan seeder untuk data dummy:
```bash
php spark db:seed
```

### Setup Tailwind CSS

1. Install Node.js dependencies:
```bash
npm install
```

2. Build Tailwind CSS:
```bash
npm run build
```

3. Untuk development dengan auto-rebuild saat ada perubahan CSS:
```bash
npm run dev
```

### Jalankan Aplikasi

1. Start PHP development server:
```bash
php spark serve
```

2. Aplikasi akan berjalan di `http://localhost:8080`

3. Akses halaman login dan masuk dengan kredensial default (lihat bagian Autentikasi)

## 🎨 UI/UX Features

### Tailwind CSS Components
Aplikasi dilengkapi dengan komponen UI yang sudah dikustomisasi:
- **Cards** - Card dengan shadow dan hover effects
- **Buttons** - Multiple button variants (primary, success, warning, danger)
- **Forms** - Input fields dengan focus states yang smooth
- **Badges** - Status badges dengan berbagai warna
- **Tables** - Responsive tables dengan hover effects
- **Alerts** - Styled alert boxes dengan auto-dismiss
- **Modals** - Modal dialogs dengan backdrop

### Animasi
Animasi halus yang telah diimplementasikan:
- `fade-in` - Muncul dengan fade effect
- `slide-in` - Slide dari kiri
- `slide-up` - Slide dari bawah
- `scale-in` - Scale up effect untuk modals
- `pulse-soft` - Pulse animation untuk notifikasi
- Hover transitions pada semua elemen interaktif

### Material Symbols
Menggunakan Material Symbols (Material Icons 3) dengan:
- Filled variant untuk visual yang lebih solid
- Smooth font variations
- Consistent sizing dan spacing
- Full color support

## 🔗 URL Routes

### Web Routes (UI)
- `/` - Halaman login
- `/admin/dashboard` - Dashboard admin
- `/guru-piket/dashboard` - Dashboard guru piket
- `/student/dashboard` - Dashboard siswa/orang tua

### API Routes (Legacy - Optional)

## 📡 Konfigurasi Mesin Fingerprint

### Setting di Mesin (ZKTeco)

1. Masuk ke menu **Communication** di mesin
2. Pilih **ADMS**
3. Konfigurasi:
   - Server IP: IP server Anda (contoh: 192.168.1.100)
   - Port: 8080
   - Push Protocol: ADMS
   - Push URL: `/iclock/cdata`

4. Enable Real-time Push
5. Set interval: 1 menit (untuk push otomatis)

### Endpoint yang Digunakan Mesin

Mesin akan memanggil endpoint berikut:

- `POST /iclock/cdata` - Upload data absensi
- `GET /iclock/getrequest` - Cek command dari server
- `POST /iclock/registry` - Registrasi mesin
- `POST /iclock/devicecmd` - Konfirmasi command

## 🔐 Autentikasi

Default user credentials (setelah run seeder):

**Admin:**
- Username: `admin`
- Password: `admin123`

**Guru Piket:**
- Username: `guru.piket`
- Password: `guru123`

**Siswa:**
- Username: `12345` (NIS siswa)
- Password: `siswa123`

**Orang Tua:**
- Username: `orangtua.12345`
- Password: `ortu123`

## 📊 Logika Sistem

### Double Scan Filtering

Sistem otomatis memfilter scan ganda:
- **Scan Pertama** = Scan Masuk
- **Scan Terakhir** = Scan Pulang
- Scan di antara kedua akan diabaikan

### Penentuan Status Kehadiran

1. **Hadir Tepat Waktu**: Scan sebelum batas waktu + toleransi
2. **Terlambat**: Scan setelah batas waktu + toleransi
3. **Sakit**: Input manual oleh guru piket dengan bukti
4. **Izin**: Input manual oleh guru piket dengan surat izin
5. **Alpha**: Tidak ada scan dan tidak ada keterangan
6. **Lupa Scan**: Input manual oleh guru piket (siswa hadir tapi lupa scan)

### Flow Proses Absensi

```
Siswa Scan Jari
    ↓
Mesin Kirim Data ke Server (Real-time)
    ↓
Server Terima Data (/iclock/cdata)
    ↓
Mapping PIN ke Student ID
    ↓
Insert ke attendance_logs
    ↓
Proses Attendance Summary (Filter Double Scan)
    ↓
Cek Shift & Hitung Keterlambatan
    ↓
Update attendance_summaries
    ↓
Kirim Notifikasi ke Orang Tua
```

## 🗂️ Struktur Database

### Tabel Utama

1. **devices** - Data mesin fingerprint
2. **device_user_maps** - Mapping PIN mesin ke siswa
3. **attendance_logs** - Log scan mentah dari mesin
4. **attendance_summaries** - Rekap harian (1 siswa = 1 row per hari)
5. **attendance_exceptions** - Sakit, Izin, Lupa Scan
6. **shifts** - Jam shift
7. **students** - Data siswa
8. **classes** - Data kelas
9. **users** - User login
10. **notifications** - Notifikasi

## 🛠️ Endpoints

### Web Pages (Primary Interface)

#### Admin
- `GET /admin/dashboard` - Dashboard utama admin
- `GET /admin/devices` - Halaman manajemen mesin
- `GET /admin/device-mapping` - Halaman mapping ID mesin
- `GET /admin/shifts` - Halaman pengaturan shift
- `GET /admin/students` - Halaman data siswa
- `GET /admin/classes` - Halaman data kelas
- `GET /admin/users` - Halaman manajemen user

#### Guru Piket
- `GET /guru-piket/dashboard` - Dashboard guru piket
- `GET /guru-piket/monitoring` - Monitoring real-time
- `GET /guru-piket/daily-recap` - Rekap harian
- `GET /guru-piket/exceptions` - Input ketidakhadiran

#### Siswa/Orang Tua
- `GET /student/dashboard` - Dashboard siswa
- `GET /student/attendance` - Riwayat kehadiran
- `GET /student/notifications` - Notifikasi
- `GET /student/profile` - Profil siswa

### API Endpoints (Legacy - for AJAX calls)
- `GET /api/admin/devices` - List mesin
- `POST /api/admin/devices` - Tambah mesin
- `PUT /api/admin/devices/{id}` - Update mesin
- `DELETE /api/admin/devices/{id}` - Hapus mesin
- `POST /api/admin/devices/{id}/test-connection` - Test koneksi
- `GET /api/admin/device-user-maps` - List mapping
- `POST /api/admin/device-user-maps` - Buat mapping
- `GET /api/admin/shifts` - List shift
- `POST /api/admin/shifts` - Buat shift

### Guru Piket
- `GET /api/guru-piket/daily-summary` - Rekap harian
- `GET /api/guru-piket/not-checked-in` - Siswa belum scan
- `GET /api/guru-piket/recent-logs` - Log terbaru (real-time)
- `POST /api/guru-piket/exceptions` - Input exception
- `GET /api/guru-piket/exceptions` - List exception

### Siswa/Orang Tua
- `GET /api/student/profile` - Profil siswa
- `GET /api/student/attendance/today` - Kehadiran hari ini
- `GET /api/student/attendance/summary` - Rekap kehadiran
- `GET /api/student/notifications` - List notifikasi
- `PUT /api/student/notifications/{id}/read` - Tandai dibaca

## 🐛 Troubleshooting

### Mesin tidak bisa connect
1. Cek IP address server bisa di ping dari mesin
2. Pastikan port 8080 tidak diblock firewall
3. Cek setting ADMS di mesin
4. Lihat log di `writable/logs/`

### Data tidak masuk
1. Cek apakah PIN sudah di-mapping ke siswa
2. Lihat log error di browser console & server logs
3. Test manual dengan POST ke `/iclock/cdata`

### Notifikasi tidak terkirim
1. Pastikan user orang tua sudah dibuat
2. Cek field `student_id` di tabel users terisi
3. Implementasikan integrasi WA Gateway (opsional)

### CSS tidak berubah setelah edit
1. Jalankan `npm run build` untuk rebuild Tailwind CSS
2. Atau jalankan `npm run dev` untuk auto-rebuild saat development
3. Clear browser cache (Ctrl+Shift+R / Cmd+Shift+R)

### Halaman tidak muncul dengan benar
1. Pastikan Tailwind CSS sudah di-build (`npm run build`)
2. Cek apakah file `public/css/style.css` ada
3. Periksa console browser untuk error loading CSS

## 🎨 Kustomisasi UI

### Menambah Komponen Tailwind
Edit file `assets/css/input.css` untuk menambah komponen custom:

```css
@layer components {
  .my-custom-component {
    @apply bg-blue-500 text-white p-4 rounded-lg;
  }
}
```

Kemudian rebuild dengan `npm run build`

### Mengubah Warna Theme
Edit `tailwind.config.js` untuk mengubah warna theme:

```javascript
theme: {
  extend: {
    colors: {
      primary: {
        // Ubah nilai-nilai ini sesuai kebutuhan
        500: '#3b82f6',
        600: '#2563eb',
        // ...
      }
    }
  }
}
```

### Menambah Animasi
Tambahkan animasi baru di `tailwind.config.js`:

```javascript
animation: {
  'my-animation': 'myKeyframe 1s ease-in-out',
},
keyframes: {
  myKeyframe: {
    '0%': { transform: 'scale(0)' },
    '100%': { transform: 'scale(1)' },
  }
}
```

## 📝 TODO / Roadmap

- [ ] Implementasi AJAX untuk real-time updates tanpa reload
- [ ] Integrasi WhatsApp Gateway untuk notifikasi
- [ ] Export laporan ke Excel/PDF
- [ ] Dashboard grafik statistik interaktif
- [ ] Multi-tenancy (multi sekolah)
- [ ] Mobile app (Progressive Web App)
- [ ] Face recognition support
- [ ] Backup & restore database otomatis
- [ ] Dark mode theme
- [ ] Advanced filtering dan search
- [ ] API documentation dengan Swagger

## 📦 Struktur File Penting

```
absensi-siswa/
├── app/
│   ├── Controllers/
│   │   ├── Auth.php          # Authentication controller
│   │   ├── Admin.php          # Admin dashboard controller
│   │   ├── GuruPiket.php      # Teacher dashboard controller
│   │   ├── Student.php        # Student dashboard controller
│   │   └── IclockController.php # Fingerprint device endpoint
│   ├── Views/
│   │   ├── layouts/
│   │   │   └── main.php       # Main layout template
│   │   ├── auth/
│   │   │   └── login.php      # Login page
│   │   └── dashboard/
│   │       ├── admin.php      # Admin dashboard
│   │       ├── guru_piket.php # Teacher dashboard
│   │       └── student.php    # Student dashboard
│   ├── Filters/
│   │   └── Auth.php           # Authentication filter
│   └── Config/
│       ├── Routes.php         # Route definitions
│       └── Filters.php        # Filter configurations
├── public/
│   └── css/
│       └── style.css          # Compiled Tailwind CSS
├── assets/
│   └── css/
│       └── input.css          # Source Tailwind CSS
├── tailwind.config.js         # Tailwind configuration
├── package.json               # Node.js dependencies
└── composer.json              # PHP dependencies
```

## 📄 Lisensi

MIT License

## 👥 Kontributor

Developed by: [Your Name]

## 📞 Support

Jika ada pertanyaan, silakan buat issue di repository ini.
