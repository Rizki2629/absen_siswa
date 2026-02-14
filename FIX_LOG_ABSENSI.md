# Panduan Fix Log Absensi

## Masalah yang Ditemukan:

1. **Session cookie tidak secure** - Cookie tidak dikirim karena aplikasi menggunakan HTTPS tapi cookie tidak di-set sebagai secure
2. **AJAX request tidak mengirim credentials** - Fetch API tidak menggunakan `credentials: 'include'`

## Yang Sudah Diperbaiki (v47):

✅ Cookie secure = true di Heroku (HTTPS)
✅ Fetch API menggunakan credentials: 'include'
✅ Tambah header X-Requested-With untuk AJAX
✅ Error handling yang lebih baik untuk 401 Unauthorized

## Langkah-langkah untuk User:

### 1. LOGOUT dulu

- Klik menu Keluar atau akses: https://absensi-siswa-688f8bff946c.herokuapp.com/logout

### 2. LOGIN kembali

- Masuk dengan username dan password
- Session cookie baru yang secure akan dibuat

### 3. Buka halaman Log Absensi

- Menu: Log Absensi
- Atau akses: https://absensi-siswa-688f8bff946c.herokuapp.com/admin/attendance-logs

### 4. Data log seharusnya muncul

- 3 log absensi dengan PIN 12345 dan 105
- Nama siswa masih "Tidak Dikenali" karena mapping PIN belum sesuai

## Catatan Penting:

### PIN Mapping Belum Sesuai:

- Log di database: PIN **12345** (5 digit)
- Mapping yang dibuat: PIN **123456** (6 digit)
- **Solusi**: Edit mapping atau tambah mapping baru dengan PIN **12345**

### Next Steps Setelah Login:

1. Buka halaman Mapping ID Mesin
2. Edit mapping yang ada atau tambah mapping baru:
   - Device: MESIN ABSEN X900
   - Device User ID (PIN): **12345** (BUKAN 123456)
   - Student: RIZKI
3. Setelah mapping benar, log absensi akan menampilkan nama siswa

## Jika Masih Belum Muncul:

1. Buka Console Browser (F12 > Console)
2. Lihat error message
3. Pastikan tidak ada error 401 Unauthorized
4. Refresh halaman beberapa kali
