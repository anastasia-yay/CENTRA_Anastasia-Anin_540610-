# 🌍 CENTRA — Central Java Disaster Analytics (WebGIS)
> **Visualisasi dan Analisis Data Kebencanaan di Wilayah Provinsi Jawa Tengah**

**CENTRA** adalah platform *WebGIS* (Sistem Informasi Geografis Berbasis Web) integratif yang dirancang khusus untuk memetakan, menganalisis, dan mengelola data kejadian bencana secara spasial. Platform ini dikembangkan sebagai alat bantu digital interaktif bagi **BPBD** serta instansi terkait dalam mendukung manajemen mitigasi, penanganan darurat, dan perencanaan pembangunan berbasis risiko bencana.

---

## 📌 Latar Belakang
Provinsi Jawa Tengah memiliki tingkat kerawanan bencana yang cukup tinggi, meliputi banjir, tanah longsor, kekeringan, gempa bumi, tsunami, erupsi gunung api, hingga cuaca ekstrem. Ketersediaan informasi spasial yang cepat, akurat, dan mudah diakses menjadi kebutuhan krusial. **CENTRA** hadir mengintegrasikan data spasial, statistik kebencanaan, visualisasi interaktif, serta peta digital untuk mempercepat pengambilan keputusan.

---

## 🚀 Fitur CENTRA

### 1. 📊 Dashboard Statistik Interaktif
* Menyajikan ringkasan eksekutif data kebencanaan secara *real-time* dan dinamis.
* Menampilkan metrik total kejadian, jumlah jenis bencana, sebaran wilayah, hingga akumulasi korban jiwa.
* Dilengkapi dengan grafik distribusi jenis bencana dan diagram batang wilayah dengan kejadian tertinggi/terendah (e.g., Cilacap, Purbalingga, dsb).

### 2. 🗺️ Visualisasi Spasial Otomatis (WebGIS)
* **Visualisasi Choropleth Otomatis**: Mengubah data atribut statistik menjadi peta tematik berbasis klasifikasi wilayah. Fitur ini mempermudah pembacaan tingkat kerawanan atau kerapatan distribusi kejadian bencana di 35 Kabupaten/Kota Jawa Tengah menggunakan gradasi warna.
* **Visualisasi Heatmap Otomatis**: Memvisualisasikan konsentrasi persebaran kejadian bencana berdasarkan kepadatan titik koordinat (*density*). Sangat efektif untuk mengidentifikasi area *hotspot* (prioritas utama) penanganan bencana.

### 3. 🛠️ Manajemen Data Spasial & Otomatisasi PostGIS
* Pengelolaan penuh data kejadian (CRUD) lewat antarmuka admin.
* **Otomatisasi Koordinat**: Menggunakan fungsi spasial `ST_Centroid` dari ekstensi PostGIS untuk menentukan titik koordinat aktual (`geom_actual`) secara otomatis berdasarkan referensi geometris tabel wilayah (`regions`).

### 4. 📥 Bulk Import & Export Excel
* **Import Excel/CSV**: Fasilitas unggahan data massal secara instan menggunakan engine `Maatwebsite/Excel` yang terintegrasi dengan database transaction dan validasi baris spasial.
* **Export Excel**: Mengunduh rekapitulasi data tabular bencana berformat `.xlsx` langsung dari sistem.

### 🔐 Desain Antarmuka Keamanan (Auth Scene)
* Halaman Login & Register kustom menggunakan konsep *Glassmorphic Design* bertema *Earth-Tone* (nuansa alam/mitigasi).
* Dilengkapi fitur interaktif *Password Strength Meter* (indikator kekuatan kata sandi) dan *Password Match Validator* real-time berbasis JavaScript untuk menjamin keamanan akun operator.

---

## 🛠️ Spesifikasi Teknologi

* **Framework Utama**: Laravel 12.62.0 ⚡
* **Bahasa Pemrograman**: PHP 8.2.12 & JavaScript (ES6+)
* **Database Spasial**: PostgreSQL + Ekstensi **PostGIS**
* **Library Excel**: Maatwebsite/Excel v3
* **Bundler & Style**: Bootstrap 5 + Bootstrap Icons + Blade Templating
```bash
git clone [https://github.com/username/centra-webgis.git](https://github.com/username/centra-webgis.git)
cd centra-webgis
