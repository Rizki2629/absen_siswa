## Panduan Cepat Setup Aplikasi Absensi Siswa

### 🚀 Quick Start (5 Menit)

#### 1. Setup Backend

```bash
# Masuk folder backend
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Edit .env dan sesuaikan database
# database.default.database = absensi_siswa
# database.default.username = root
# database.default.password = 

# Jalankan migrations
php spark migrate

# Jalankan server
php spark serve
```

Backend ready di: http://localhost:8080

#### 2. Setup Frontend

```bash
# Buka terminal baru, masuk folder frontend
cd frontend

# Install dependencies
npm install

# Jalankan development server
npm run dev
```

Frontend ready di: http://localhost:5173

#### 3. Setup Mesin Fingerprint

Di mesin ZKTeco:
1. Menu → Communication → ADMS
2. Server IP: [IP komputer server]
3. Port: 8080
4. Push URL: `/iclock/cdata`
5. Enable Realtime Push

### 📱 Cara Menggunakan

#### A. Login Sebagai Admin

1. Buka http://localhost:5173
2. Login dengan:
   - Username: `admin`
   - Password: `admin123`

3. **Tambah Mesin:**
   - Klik "Manajemen Mesin"
   - Klik "+ Tambah Mesin"
   - Isi: SN, Nama, IP Address, Port
   - Klik "Test Koneksi" untuk memastikan mesin online

4. **Mapping ID:**
   - Klik "Mapping ID"
   - Klik "+ Tambah Mapping"
   - Pilih Mesin
   - Masukkan PIN/User ID dari mesin
   - Pilih Siswa yang sesuai
   - Simpan

5. **Atur Jam Shift:**
   - Klik "Pengaturan Shift"
   - Klik "+ Tambah Shift"
   - Isi jam scan masuk & pulang
   - Set toleransi keterlambatan
   - Aktifkan shift

#### B. Login Sebagai Guru Piket

1. Login dengan:
   - Username: `guru.piket`
   - Password: `guru123`

2. **Monitor Real-time:**
   - Klik "Monitoring Real-time"
   - Lihat siswa yang baru scan (auto refresh)

3. **Lihat Rekap Harian:**
   - Klik "Rekap Harian"
   - Pilih tanggal
   - Lihat statistik & daftar kehadiran

4. **Input Exception:**
   - Klik "Input Ketidakhadiran"
   - Klik "+ Input Exception"
   - Pilih siswa yang sakit/izin/lupa scan
   - Isi keterangan
   - Upload bukti (untuk sakit/izin)

#### C. Login Sebagai Siswa/Orang Tua

1. Login dengan:
   - Username: [NIS siswa] (contoh: `12345`)
   - Password: `siswa123`

2. **Lihat Kehadiran:**
   - Dashboard menampilkan kehadiran hari ini
   - Lihat statistik bulanan
   - Cek riwayat kehadiran

3. **Cek Notifikasi:**
   - Klik icon notifikasi
   - Lihat pesan scan masuk/pulang

### 🔧 Tips & Trik

#### Testing Tanpa Mesin Fisik

Simulasikan data dari mesin dengan curl:

```bash
curl -X POST http://localhost:8080/iclock/cdata \
  -H "Content-Type: text/plain" \
  -d "105	2026-02-07 07:15:00	0	0
106	2026-02-07 07:20:00	0	0"
```

Format: `PIN<TAB>DATETIME<TAB>STATUS<TAB>WORKCODE`

#### Reset Database

```bash
cd backend
php spark migrate:rollback
php spark migrate
```

#### Tambah Data Dummy

Buat seeder dan jalankan:
```bash
php spark db:seed NamaSeeder
```

### ❗ Common Issues

**1. CORS Error di browser**
- Pastikan backend running
- Cek URL API di `frontend/.env`
- Tambah CORS config di `backend/app/Config/Filters.php`

**2. Database connection failed**
- Cek service MySQL running
- Cek credentials di `backend/.env`
- Pastikan database sudah dibuat

**3. Mesin tidak konek**
- Pastikan IP server bisa di-ping dari mesin
- Cek firewall tidak block port 8080
- Test dengan browser: http://[IP-SERVER]:8080/iclock/registry

**4. Mapping tidak berfungsi**
- Cek PIN di mesin sama dengan PIN di database
- Lihat tabel `device_user_maps`
- Cek `device_id` benar

### 📊 Monitoring Log

Cek log error:
```bash
# Backend logs
tail -f backend/writable/logs/log-2026-02-07.log

# Frontend console
# Buka Browser DevTools → Console
```

### 🎯 Next Steps

1. ✅ Setup database dan migrations
2. ✅ Test login admin
3. ✅ Tambah mesin fingerprint
4. ✅ Buat mapping ID
5. ✅ Atur jam shift
6. ✅ Test scan dari mesin
7. ✅ Cek data masuk di system

### 📞 Need Help?

- Baca `DOCUMENTATION.md` untuk detail lengkap
- Check logs untuk error messages
- Create issue di repository

Happy coding! 🎉
