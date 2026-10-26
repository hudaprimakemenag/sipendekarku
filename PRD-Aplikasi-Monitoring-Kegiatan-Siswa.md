# Product Requirements Document (PRD)
## Aplikasi Monitoring Kegiatan Harian Siswa (Ibadah, Akhlak, Sosial)

**Versi:** 1.0
**Tanggal:** 16 September 2026
**Tech Stack:** Laravel (terbaru) + Livewire + Tailwind CSS

---

## 1. Latar Belakang & Tujuan

Aplikasi ini digunakan untuk mencatat dan memonitor kegiatan harian siswa yang berkaitan dengan **ibadah**, **akhlak**, dan **sosial**, dengan sistem verifikasi oleh guru dan pemantauan oleh wali murid. Aplikasi bersifat **multi-sekolah (multi-tenant)**, dikelola secara terpusat oleh Superadmin, dengan kategori kegiatan yang **dinamis dan global** untuk seluruh sekolah.

### Tujuan
- Membiasakan siswa mencatat kegiatan ibadah, akhlak, dan sosial secara mandiri.
- Memberikan sarana verifikasi guru terhadap kejujuran pelaporan siswa.
- Memberikan visibilitas kepada wali murid atas perkembangan anak.
- Mendorong motivasi siswa melalui sistem poin dan ranking per kelas.

---

## 2. Aktor & Role

| Role | Cakupan Akses |
|---|---|
| **Superadmin** | Kelola seluruh sekolah, kelola master kategori & jenis kegiatan (global), lihat laporan lintas sekolah, atur bobot poin |
| **Admin Sekolah** | Kelola guru & siswa di sekolahnya, kelola kelas, tahun ajaran/semester, penugasan guru pengampu, lihat laporan sekolahnya |
| **Guru** | Memverifikasi kegiatan siswa di kelas yang diampu (bisa lebih dari satu kelas), memberi catatan verifikasi |
| **Siswa** | Checklist/isi kegiatan harian sendiri (hanya hari berjalan) |
| **Wali Murid** | Memonitor kegiatan anak (bisa lebih dari satu anak), melihat rekap & grafik, unduh laporan |

---

## 3. Konsep Data Dinamis (Kategori & Jenis Kegiatan)

Struktur bertingkat, dikelola oleh **Superadmin**, berlaku **global** untuk semua sekolah:

```
Kelompok (Group)          → contoh: Ibadah, Akhlak, Sosial (bisa tambah kelompok baru)
  └─ Jenis Kegiatan (Item) → contoh: Sholat Subuh, Puasa, Tadarus, Kerapian Kamar, Infaq
       └─ Tipe Input       → Checklist (ya/tidak) atau Text/Angka (misal jumlah ayat, nominal rupiah)
       └─ Bobot Poin       → nilai poin per kegiatan, diatur Superadmin
```

**CRUD Superadmin mencakup:**
- Kelompok kegiatan (tambah/edit/hapus/nonaktifkan)
- Jenis kegiatan dalam tiap kelompok
- Tipe input per jenis kegiatan (checklist / text)
- Bobot poin per jenis kegiatan
- Urutan tampil (untuk UX checklist siswa)

> Catatan: Menonaktifkan (bukan menghapus permanen) direkomendasikan untuk jenis kegiatan yang sudah punya histori data, agar data lama tidak rusak.

---

## 4. Alur Bisnis Utama

### 4.1 Struktur Sekolah, Kelas, Tahun Ajaran
- Superadmin membuat data sekolah baru beserta Admin Sekolah-nya.
- Admin Sekolah mengelola **tahun ajaran & semester** (aktif/nonaktif).
- Admin Sekolah membuat **kelas** per tahun ajaran (misal: Kelas 5A – TA 2026/2027 Ganjil).
- Admin Sekolah menempatkan **siswa** ke dalam kelas per tahun ajaran.
- Admin Sekolah menugaskan **guru pengampu** ke kelas (satu guru bisa mengampu banyak kelas — kasus guru PAI).
- Saat pindah tahun ajaran, siswa bisa naik kelas/pindah, dan **guru pengampu ikut berubah**, namun **riwayat penugasan tersimpan** (histori: siswa A, semester X, diampu guru B).

### 4.2 Input Kegiatan oleh Siswa
- Siswa login, melihat daftar kegiatan (checklist & isian text) untuk **hari itu saja**.
- Batas waktu: selama tanggal sistem masih sama, siswa boleh isi/ubah checklist.
- Setelah tanggal berganti, input untuk tanggal sebelumnya **terkunci otomatis** — tidak bisa diisi/susulan oleh siapapun.
- Jika siswa tidak checklist di hari itu → status kegiatan hari itu **"tidak ada laporan"**, otomatis **tidak dapat poin**.

### 4.3 Verifikasi oleh Guru
- Guru melihat daftar laporan siswa dari kelas yang diampunya, per tanggal.
- Guru memberi **catatan verifikasi** per kegiatan/siswa (baik untuk item checklist maupun text).
- Verifikasi guru menentukan **kapan poin dihitung** — poin baru dikreditkan setelah guru memverifikasi (bukan otomatis saat siswa checklist).
- Guru dapat memverifikasi banyak siswa/kegiatan sekaligus (bulk action) untuk efisiensi.

### 4.4 Sistem Poin & Ranking
- Tiap jenis kegiatan memiliki bobot poin (diatur Superadmin).
- Poin masuk ke akun siswa **setelah** guru verifikasi.
- **Ranking/leaderboard dihitung per kelas**, berdasarkan akumulasi poin dalam periode tertentu (harian/mingguan/bulanan/semester — direkomendasikan semester berjalan sebagai default, dengan filter periode).
- Riwayat poin per siswa dapat dilihat oleh siswa, guru, admin sekolah, dan wali murid.

### 4.5 Monitoring Wali Murid
- Wali murid melihat rekap kegiatan anak: **harian, mingguan, bulanan**.
- Grafik statistik (contoh: persentase kehadiran sholat 5 waktu per minggu, tren poin, dsb).
- Satu akun wali murid dapat terhubung ke **lebih dari satu anak** (relasi many-to-many wali–siswa).
- Tidak ada notifikasi otomatis (sesuai keputusan awal) — wali murid mengecek aplikasi secara mandiri.

### 4.6 Laporan (PDF & Excel)
Semua role memiliki kebutuhan laporan berbeda:

| Role | Isi Laporan |
|---|---|
| Wali Murid | Rekap kegiatan & poin anak (harian/mingguan/bulanan) |
| Guru | Rekap kegiatan & verifikasi kelas yang diampu |
| Admin Sekolah | Rekap seluruh siswa & guru di sekolahnya, ranking per kelas |
| Superadmin | Rekap lintas sekolah, perbandingan antar sekolah |

Format ekspor: **PDF** (cetak/arsip) dan **Excel** (analisis lanjutan).

---

## 5. Struktur Data (Entitas Utama — Gambaran ERD)

- `schools` (sekolah)
- `users` (semua role, dibedakan via role/relasi)
- `roles` & `permissions` (Spatie Laravel-Permission)
- `academic_years` (tahun ajaran)
- `semesters` (ganjil/genap, relasi ke academic_years)
- `classes` (kelas, relasi ke schools + academic_years)
- `class_teacher` (histori penugasan guru pengampu ke kelas per semester)
- `class_student` (histori penempatan siswa ke kelas per semester)
- `parent_student` (relasi wali murid ke siswa, many-to-many)
- `activity_groups` (kelompok: Ibadah, Akhlak, Sosial, dst — global)
- `activity_items` (jenis kegiatan: Sholat Subuh, Puasa, dst — global, relasi ke group)
- `activity_item_type` (checklist / text) + bobot poin
- `student_activity_logs` (input harian siswa: tanggal, item, nilai/isian, status)
- `activity_verifications` (verifikasi guru: catatan, status verifikasi, poin dikreditkan, timestamp)
- `student_points` (akumulasi/riwayat poin siswa per semester)

---

## 6. Kebutuhan Non-Fungsional

- **Multi-tenancy**: isolasi data antar sekolah (admin sekolah & guru hanya akses data sekolahnya sendiri; superadmin lintas sekolah).
- **Role-based access control**: menggunakan package **Spatie Laravel-Permission** untuk manajemen role & permission granular.
- **Autentikasi**: Laravel Breeze/Fortify + Livewire, dengan multi-role login (redirect dashboard sesuai role).
- **Audit trail**: pencatatan histori penugasan guru-kelas dan perubahan poin agar dapat ditelusuri.
- **Locking data harian**: job/scheduler untuk mengunci input setelah lewat tanggal (mis. cron tengah malam berdasarkan timezone sekolah).
- **Skalabilitas**: struktur multi-sekolah harus siap untuk pertumbuhan jumlah sekolah & siswa.
- **Export**: menggunakan library seperti `barryvdh/laravel-dompdf` (PDF) dan `maatwebsite/excel` (Excel).
- **UI**: Tailwind CSS, komponen interaktif dengan Livewire (real-time checklist, verifikasi bulk, filter laporan tanpa reload).

---

## 7. Modul/Fitur per Role (Ringkasan Menu)

### Superadmin
- Dashboard lintas sekolah (statistik global)
- Kelola Sekolah (CRUD)
- Kelola Kelompok & Jenis Kegiatan (CRUD, bobot poin)
- Kelola Role & Permission
- Laporan lintas sekolah (PDF/Excel)

### Admin Sekolah
- Dashboard sekolah
- Kelola Tahun Ajaran & Semester
- Kelola Kelas
- Kelola Guru (assign ke kelas)
- Kelola Siswa (assign ke kelas)
- Kelola Wali Murid (relasi ke siswa)
- Laporan sekolah & ranking per kelas

### Guru
- Dashboard kelas yang diampu (bisa lebih dari 1 kelas)
- Verifikasi kegiatan siswa per tanggal (dengan catatan)
- Riwayat verifikasi
- Laporan kelas

### Siswa
- Checklist/isi kegiatan harian (hari berjalan saja)
- Riwayat kegiatan pribadi
- Riwayat poin & ranking kelas

### Wali Murid
- Pilih anak (jika lebih dari satu)
- Rekap harian/mingguan/bulanan
- Grafik statistik kegiatan & poin
- Unduh laporan (PDF/Excel)

---

## 8. Hal yang Perlu Didiskusikan Lebih Lanjut (Opsional, di luar cakupan awal)

Beberapa hal berikut tidak dibahas eksplisit di requirement awal, saya tandai sebagai catatan untuk didiskusikan saat masuk tahap desain teknis:
- Format registrasi user baru: apakah admin sekolah yang membuat akun guru/siswa/wali (dengan password default), atau ada self-registration dengan approval?
- Apakah wali murid perlu didaftarkan manual oleh admin sekolah, atau siswa yang mengundang wali murid mereka sendiri?
- Timezone: apakah semua sekolah pakai timezone yang sama, atau perlu per-sekolah (relevan untuk logika "hari berjalan")?
- Skema ranking: apakah ranking direset tiap semester, atau berjalan terus (akumulatif) dan hanya difilter per periode?

---

*Dokumen ini adalah dasar untuk tahap desain database detail, wireframe UI, dan pembagian sprint pengembangan.*
