# 📱 Aplikasi Absensi Siswa dengan Fingerprint

Sistem manajemen kehadiran siswa terintegrasi dengan mesin fingerprint ZKTeco. Dibangun dengan **CodeIgniter 4**, **Tailwind CSS**, dan **Material Icons 3**.

## ✨ Highlights

- 🎨 **Modern UI/UX** dengan Tailwind CSS dan animasi smooth
- 📱 **Responsive Design** - Mobile-friendly interface
- 🔒 **Role-based Access** - Admin, Guru Piket, Siswa, Orang Tua
- ⚡ **Real-time Monitoring** - Live scan updates
- 🎯 **Material Icons 3** - Beautiful full-color icons
- 📊 **Comprehensive Dashboard** - Statistics dan laporan lengkap

## 🚀 Quick Start

```bash
# Install dependencies
composer install
npm install

# Setup database
cp env .env
# Edit .env dengan konfigurasi database Anda
php spark migrate
php spark db:seed

# Build CSS
npm run build

# Run server
php spark serve
```

Akses aplikasi di `http://localhost:8080`

## 🔐 Default Login

**Admin:**
- Username: `admin`
- Password: `admin123`

**Guru Piket:**
- Username: `guru.piket`
- Password: `guru123`

**Siswa:**
- Username: `12345` (NIS)
- Password: `siswa123`

## 📚 Dokumentasi Lengkap

Lihat [DOCUMENTATION.md](DOCUMENTATION.md) untuk dokumentasi lengkap.

## 🛠️ Tech Stack

- **Backend:** CodeIgniter 4
- **Frontend:** Tailwind CSS 3.4
- **Icons:** Material Symbols (Material Icons 3)
- **Database:** MySQL
- **Fingerprint:** ZKTeco ADMS Protocol

## 📝 Fitur Utama

### Admin Panel
- Manajemen mesin fingerprint
- Mapping ID mesin ke siswa
- Pengaturan jam shift
- Manajemen user dan kelas

### Guru Piket Panel
- Monitoring real-time scan siswa
- Rekap harian kehadiran
- Input ketidakhadiran (Sakit, Izin, Lupa Scan)
- Filter dan laporan

### Siswa/Orang Tua Panel
- Riwayat kehadiran lengkap
- Statistik bulanan
- Notifikasi kehadiran
- Profil siswa

## 🎨 Tailwind CSS Development

Untuk mengembangkan UI dengan Tailwind CSS:

```bash
# Watch mode - auto rebuild saat ada perubahan
npm run dev

# Production build - minified
npm run build
```

## 📄 License

MIT License

---

**Made with ❤️ using CodeIgniter 4 & Tailwind CSS**
