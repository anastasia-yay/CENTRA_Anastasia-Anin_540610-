<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

# 🌍 CENTRA — Central Java Disaster Analytics (WebGIS BPBD)

**CENTRA** adalah sebuah platform WebGIS (Sistem Informasi Geografis Berbasis Web) yang dirancang khusus untuk memetakan, menganalisis, dan mengelola data kejadian bencana di wilayah Jawa Tengah. Aplikasi ini dikembangkan sebagai alat bantu digital bagi **BPBD** dalam mengelola record bencana secara efisien, akurat, dan berbasis spasial.

---

## 🚀 Fitur Unggulan

* 📊 **Manajemen Kejadian Bencana (CRUD)**: Pengelolaan data komprehensif mulai dari jenis bencana, wilayah terdampak, hingga tingkat risiko.
* 🗺️ **Otomatisasi Geospatial (PostGIS Integration)**: Sistem secara otomatis menentukan titik koordinat aktual (`geom_actual`) suatu kejadian bencana menggunakan fungsi spasial `ST_Centroid` dari data geometris wilayah (`regions`).
* 📥 **Import Data Excel & CSV**: Mempercepat input data massal secara batch langsung dari spreadsheet menggunakan engine `Maatwebsite/Excel` yang dilengkapi validasi baris ketat.
* 📤 **Export Data Excel**: Memindahkan ringkasan laporan spasial bencana ke dalam file Excel `.xlsx` dalam satu klik lengkap dengan pemetaan relasi data.
* 🔐 **Sistem Autentikasi Kustom**: Halaman masuk dan pendaftaran kustom bertema *Earth-Tone* yang dilengkapi fitur *Password Strength Meter* interaktif untuk menjamin keamanan akun.

---

## 🛠️ Spesifikasi Teknologi

Aplikasi ini dibangun menggunakan ekosistem teknologi modern:

* **Framework**: Laravel 12.62.0 ✨
* **Bahasa Pemrograman**: PHP 8.2.12
* **Database**: PostgreSQL + Ekstensi **PostGIS** (Untuk kebutuhan spasial/geometris)
* **Library Excel**: Maatwebsite/Excel v3
* **Frontend**: Bootstrap 5 + Blade Templating
