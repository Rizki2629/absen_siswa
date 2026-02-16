# 🎯 Panduan Login Siswa & Halaman 7 Kebiasaan

## 🔐 Cara Login Siswa

### Login dengan NISN:

```
URL      : http://localhost:8080
Username : [NISN Siswa]
Password : siswa123
```

### Contoh Login:

- **Siswa**: Budi Santoso
- **NISN**: 12345
- **Password**: siswa123

Siswa bisa login menggunakan:

1. ✅ **NISN** (Nomor Induk Siswa Nasional)
2. ✅ **Username** (jika ada)
3. ✅ **Email** (jika ada)

---

## 🖼️ Gambar Card 7 Kebiasaan

### Lokasi Gambar:

```
public/images/habits/
```

### Mapping Gambar ke Kebiasaan:

| No  | Kebiasaan                    | Gambar                 | Deskripsi                           |
| --- | ---------------------------- | ---------------------- | ----------------------------------- |
| 1   | **Proaktif**                 | `bagun pagi.webp`      | Berinisiatif dan bertanggung jawab  |
| 2   | **Merujuk Tujuan**           | `gemar belajar.webp`   | Memiliki visi dan tujuan hidup      |
| 3   | **Dahulukan Yang Utama**     | `tidur cepat.webp`     | Prioritaskan yang penting           |
| 4   | **Berpikir Menang-Menang**   | `bermasyarakat.webp`   | Solusi menguntungkan semua pihak    |
| 5   | **Mengerti Lalu Dimengerti** | `rajib beribadah.webp` | Dengarkan dulu sebelum bicara       |
| 6   | **Wujudkan Sinergi**         | `berolahraga.webp`     | Bekerja sama untuk hasil lebih baik |
| 7   | **Mengasah Gergaji**         | `makan bergizi.webp`   | Terus belajar dan berkembang        |

### Cara Kerja Gambar:

1. Gambar otomatis dimuat dari `public/images/habits/[nama-file].webp`
2. Jika gambar tidak ditemukan, akan tampil icon fallback
3. Ukuran gambar: 80x80px (w-20 h-20 Tailwind)
4. Style: Rounded corners dengan shadow

---

## 🎨 Cara Mengganti Gambar

### 1. Siapkan Gambar Baru:

- **Format**: WebP, PNG, atau JPG
- **Ukuran**: Minimal 400x400px (akan di-resize otomatis)
- **Nama**: Sesuai dengan nama di tabel mapping di atas

### 2. Upload ke Folder:

```bash
# Copy gambar ke folder habits
cp gambar-baru.webp public/images/habits/
```

### 3. Update Kode (Opsional):

Jika ingin mengubah mapping gambar, edit file:

```
app/Views/student/habits.php
```

Cari bagian `habitDefinitions` dan update properti `image`:

```javascript
{
    key: 'proaktif',
    title: 'Proaktif',
    image: 'nama-gambar-baru.webp',  // ← Ubah ini
    // ...
}
```

---

## 📱 Fitur Halaman 7 Kebiasaan

### 1. **Progress Real-time**

- Progress bar dinamis
- Hitungan kebiasaan selesai (X/7)
- Persentase completion

### 2. **Statistik**

- **XP**: 20 poin per kebiasaan (max 140 XP/hari)
- **Streak**: Hari beruntun dengan 7/7 kebiasaan
- **Perfect Days**: Hari sempurna bulan ini
- **Total Days**: Total hari tercatat

### 3. **Interaksi**

- Klik card untuk toggle status
- Auto-save ke database
- Visual feedback (border hijau saat complete)
- Animation hover

### 4. **Design**

- Glass morphism effect
- Smooth transitions
- Mobile responsive
- Dark mode ready (optional)

---

## 🔧 Troubleshooting

### Gambar Tidak Muncul?

1. Cek apakah file ada di `public/images/habits/`
2. Cek nama file (case-sensitive)
3. Pastikan format didukung (webp, jpg, png)
4. Clear browser cache (Ctrl+F5)

### Login Gagal?

1. Pastikan menggunakan NISN yang benar
2. Password: `siswa123` (huruf kecil semua)
3. Cek database: tabel `students` untuk NISN
4. Cek koneksi user ke student_id

### Data Tidak Tersimpan?

1. Cek browser console (F12) untuk error
2. Cek tabel `student_habits` di database
3. Pastikan siswa punya student_id yang valid

---

## 📝 Script Helper

### Set Password Semua Siswa:

```bash
php set_student_password.php
```

### Buat User Siswa Baru:

```bash
# Via SQL
INSERT INTO students (nis, name, class_id, created_at, updated_at)
VALUES ('123456', 'Nama Siswa', 1, NOW(), NOW());

# Kemudian buat user
INSERT INTO users (username, password_hash, role, full_name, student_id, created_at, updated_at)
VALUES ('123456', '$password_hash', 'student', 'Nama Siswa', LAST_INSERT_ID(), NOW(), NOW());
```

---

## 🎉 Selamat!

Halaman 7 Kebiasaan sudah siap digunakan dengan:

- ✅ Login menggunakan NISN
- ✅ Password universal: siswa123
- ✅ Gambar custom dari folder habits
- ✅ Sistem statistik dan gamification
- ✅ Design modern dan interaktif

**URL Akses**: `http://localhost:8080/student/habits`
