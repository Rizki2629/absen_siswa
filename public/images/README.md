# Folder Gambar / Images

Folder ini digunakan untuk menyimpan gambar-gambar yang digunakan di website.

## 📁 Cara Meletakkan Gambar

1. **Simpan gambar** di folder ini: `public/images/`
2. **Akses dari view** menggunakan: `<?= base_url('images/nama-file.png') ?>`

## 🖼️ Contoh Penggunaan

### Di HTML/PHP:
```php
<img src="<?= base_url('images/meditation.png') ?>" alt="Meditasi">
```

### Di CSS (inline):
```html
<div style="background-image: url('<?= base_url('images/background.jpg') ?>')">
```

## 📂 Struktur Folder yang Disarankan

```
public/
└── images/
    ├── avatars/          # Foto profil user
    ├── habits/           # Ikon/gambar untuk halaman 7 kebiasaan
    ├── backgrounds/      # Background images
    └── icons/            # Icon files
```

## ✅ Format Gambar yang Disarankan

- **Avatar/Profil**: 200x200px, PNG/JPG
- **Background**: 1920x1080px, JPG (compressed)
- **Icons**: 256x256px, PNG (transparent)
- **Ilustrasi**: SVG (jika memungkinkan untuk ukuran file kecil)

## 📝 Catatan

- Gunakan nama file yang deskriptif (misal: `student-reading.png`)
- Hindari spasi dalam nama file (gunakan `-` atau `_`)
- Kompres gambar sebelum upload untuk performa lebih baik
- Untuk gambar yang sama digunakan berkali-kali, simpan di folder ini agar bisa di-cache browser

## 🎨 Gambar yang Digunakan di Halaman 7 Kebiasaan

File yang diperlukan:
- `meditation.png` atau `meditation.jpg` - Gambar ilustrasi meditasi/inspirasi di bagian quote

Jika gambar tidak tersedia, sistem akan otomatis menampilkan icon placeholder.
