# Alur Aplikasi Tarbiyah Tracker

## 1. Penyiapan sekolah

1. Superadmin membuat sekolah dan admin sekolah.
2. Admin sekolah membuat tahun ajaran, semester, dan kelas aktif.
3. Admin menempatkan siswa di kelas dan menugaskan guru pengampu untuk semester tersebut.
4. Admin menghubungkan wali murid dengan satu atau lebih siswa.

## 2. Pengaturan kegiatan global

1. Superadmin membuat kelompok kegiatan, misalnya Ibadah, Akhlak, dan Sosial.
2. Superadmin menambahkan item kegiatan, tipe input (`checklist` atau teks), urutan, dan bobot poin.
3. Item lama dinonaktifkan, bukan dihapus, agar riwayat laporan tetap utuh.

## 3. Pelaporan dan penguncian harian

1. Siswa mengisi atau memperbarui kegiatan pada tanggal berjalan.
2. Aplikasi membuat `student_activity_logs` berstatus `submitted`; tidak adanya baris log berarti belum dilaporkan dan tidak memperoleh poin.
3. Scheduler menjalankan `app:lock-expired-activity-logs` setiap pukul 00.05 dan mengubah laporan hari sebelumnya menjadi `locked`.
4. Laporan terkunci tidak lagi dapat diubah oleh siswa.

## 4. Verifikasi dan poin

1. Guru membuka laporan kelas yang diampu berdasarkan tanggal.
2. Guru menyetujui atau menolak log serta dapat menambahkan catatan.
3. Laporan disetujui menghasilkan satu `activity_verifications` dan satu catatan `student_points` yang tidak dapat diduplikasi.
4. Ranking kelas menghitung total `student_points` berdasarkan kelas, semester, dan periode yang dipilih.

## 5. Monitoring dan laporan

1. Wali murid memilih anak yang terhubung ke akunnya.
2. Wali melihat rekap harian, mingguan, bulanan, tren poin, dan status verifikasi.
3. Guru, admin sekolah, dan superadmin memakai cakupan data sesuai sekolah/kelas/perannya untuk laporan PDF atau Excel.
