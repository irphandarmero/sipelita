# Panduan Sosialisasi dan Juknis Penggunaan SI-PELITA

**SI-PELITA RS** adalah Sistem Pelaporan & Informasi Terpadu Antar-Unit Rumah Sakit. Panduan ini dapat digunakan sebagai bahan sosialisasi, naskah presentasi, dan petunjuk operasional pengguna.

> Nama menu dan kewenangan dalam panduan ini mengikuti alur aplikasi. Hak akses aktual ditentukan oleh akun, unit kerja, status laporan, dan jenis laporan.

## 1. Tujuan dan manfaat

SI-PELITA digunakan untuk:

- mencatat laporan rutin dan aduan insidentil antar-unit;
- meneruskan laporan kepada unit penanggung jawab;
- membagi tindak lanjut aduan insidentil melalui disposisi ke unit pendukung;
- memantau status, prioritas, penanggung jawab, dan bukti tindak lanjut; serta
- menyediakan timeline perubahan sebagai jejak proses laporan.

## 2. Alur singkat untuk bahan presentasi

```text
Pengguna masuk
      |
      v
Isi dan kirim laporan
      |
      +-- Dibuat oleh Petugas? -- Ya --> Menunggu tinjauan Kepala Unit asal
      |                                  |
      |                                  +-- Ditolak --> Status Ditolak; proses berhenti
      |                                  |
      |                                  +-- Disetujui
      |                                       |
      +-- Tidak perlu tinjauan --------------+
                                              v
                                  Unit tujuan menerima laporan
                                              |
                                 Tinjau dan tindak lanjuti
                                              |
                 +----------------------------+-------------------+
                 |                                                |
        Laporan Rutin                                  Laporan Insidentil
        tindak lanjut unit tujuan                      dapat didisposisikan
                                                                 |
                                                   Unit pendukung memberi respons
                                                                 |
                                              Semua disposisi aktif sudah selesai
                                              atau tidak ada disposisi aktif
                                                                 |
                                                                 v
                                                       Laporan ditandai Selesai
```

## 3. Peran pengguna dan kewenangan

| Peran | Penggunaan utama |
|---|---|
| **Petugas / Staf** | Membuat laporan. Laporan yang dibuat oleh akun dengan peran ini masuk ke tinjauan Kepala Unit asal sebelum diteruskan. Petugas yang menjadi petugas penanganan dapat mengirim disposisi untuk laporan insidentil. |
| **Kepala Unit** | Meninjau laporan dari Petugas di unit asalnya: menyetujui atau menolak. Kepala Unit juga dapat membuat laporan; laporan yang dibuat bukan oleh peran Petugas tidak masuk ke gerbang tinjauan tersebut. Jika unitnya menjadi unit tujuan, Kepala Unit dapat mengelola status laporan. |
| **Admin** | Mengakses seluruh laporan, menyetujui/menolak laporan yang menunggu tinjauan, menindaklanjuti status laporan, mengirim dan menindaklanjuti disposisi lintas unit, serta mengelola data master unit kerja, kategori laporan, dan akun pengguna. Laporan Selesai tetap tidak dapat diedit. |
| **Direksi** | Memantau seluruh laporan. Pengubahan status tetap mengikuti kewenangan unit tujuan; peran Direksi sendiri bukan kewenangan Admin untuk mengelola status atau master data. |

**Catatan akses:** akun harus terhubung dengan unit kerja agar dapat membuat laporan. Pengguna melihat laporan yang dibuatnya, laporan unit asal/tujuannya, atau laporan yang terkait dengan disposisi unitnya. Admin dan Direksi dapat melihat seluruh laporan.

## 4. Sebelum mulai menggunakan

1. Pastikan pengguna telah menerima akun SI-PELITA dari Administrator.
2. Pastikan nama pengguna, email, unit kerja, dan peran akses sudah benar.
3. Siapkan informasi laporan: kategori, unit tujuan, waktu kejadian, ringkasan, rincian, tingkat prioritas, serta bukti bila tersedia.
4. Untuk aduan insidentil, siapkan juga catatan tindakan awal yang telah dilakukan.
5. Akses aplikasi melalui alamat yang ditetapkan oleh pengelola SI-PELITA.

## 5. Masuk, dashboard, dan navigasi

1. Buka halaman SI-PELITA dan masukkan **email atau username terdaftar** serta kata sandi.
2. Pilih **Masuk Aplikasi**. Opsi **Ingat Saya** dapat dipilih bila sesuai kebijakan perangkat.
3. Setelah berhasil masuk, pengguna berada di **Dashboard**. Dashboard menampilkan ringkasan total, laporan Diajukan, Diproses, Selesai, dan beberapa laporan terbaru sesuai cakupan akses pengguna.
4. Gunakan menu di sisi aplikasi:
   - **Buat Laporan Baru** untuk mengirim laporan;
   - **Laporan Saya** untuk melihat laporan yang dapat diakses pengguna;
   - **Laporan Masuk Unit** untuk daftar laporan yang masuk ke unit atau disposisi yang diterima (tersedia untuk peran tertentu);
   - **Rekap Routine / Sensus** untuk membuka daftar laporan Rutin;
   - **Master Data** untuk Administrator.
5. Untuk mengganti kata sandi, buka menu akun lalu **Ubah Password**. Masukkan kata sandi saat ini, kata sandi baru minimal 8 karakter, dan konfirmasinya. Kata sandi baru harus berbeda dari kata sandi saat ini.
6. Setelah selesai, gunakan **Keluar (Logout)** dari menu akun.

## 6. Membuat dan mengirim laporan

1. Pilih **Buat Laporan Baru**.
2. Periksa informasi pelapor dan unit asal yang ditampilkan otomatis.
3. Isi formulir:
   - **Kategori Laporan**: pilih kategori yang sesuai. Kategori yang dapat dipilih adalah kategori aktif.
   - **Unit Kerja Tujuan**: pilih unit penanggung jawab. Unit asal tidak dapat dipilih sebagai unit tujuan.
   - **Judul Laporan**: tulis ringkasan yang jelas dan spesifik.
   - **Tingkat Prioritas**:
     - Rendah — rutinitas biasa;
     - Sedang — perlu penanganan standar;
     - Tinggi — berdampak pada pelayanan;
     - Darurat — layanan kritis terhenti.
   - **Tanggal & Waktu Kejadian**: masukkan waktu kejadian yang sebenarnya.
   - **Rincian Kejadian / Isi Laporan**: tulis lokasi, kronologi, kondisi, dampak, atau data yang dilaporkan secara jelas.
   - **Lampiran Bukti** (opsional): format JPG/JPEG/PNG atau PDF, maksimal 5 MB.
4. Jika kategori berjenis **Insidentil**, isi **Tindakan Awal Yang Sudah Dilakukan**. Form akan menampilkan panduan kronologi, lokasi, dampak, dan tindakan awal.
5. Periksa kembali data, lalu pilih **Kirim Laporan**.
6. Sistem membuat kode transaksi dengan awalan `RUT` untuk laporan Rutin atau `INC` untuk laporan Insidentil, tanggal pembuatan, dan rangkaian karakter.
7. Laporan tercatat dalam sistem dengan status utama **Diajukan** dan muncul pada daftar yang sesuai hak akses.

### Mengedit laporan sebelum diproses

- Pembuat laporan dapat memilih **Edit Laporan** selama status utama masih **Diajukan** dan tinjauan Kepala Unit belum diputuskan (termasuk saat **Menunggu Tinjauan**). Admin juga dapat mengedit laporan dalam kondisi tersebut.
- Jika Kepala Unit sudah menyetujui/menolak, laporan sudah diproses, atau statusnya **Selesai**, isi laporan tidak dapat diedit melalui fitur ini.
- Perubahan yang disimpan tercatat pada timeline. Lampiran lama tetap digunakan jika pengguna tidak mengunggah lampiran pengganti.

### Gerbang tinjauan Kepala Unit

- Jika laporan dibuat oleh akun **Petugas**, status peninjauan menjadi **Menunggu**. Laporan belum diteruskan untuk ditangani unit tujuan sampai ditinjau Kepala Unit dari unit asal.
- Kepala Unit membuka laporan yang menunggu tinjauan, lalu memilih:
  - **Setujui dan Teruskan**: laporan disetujui dan dapat dilihat unit tujuan;
  - **Tolak Laporan**: alasan penolakan wajib diisi; status laporan menjadi **Ditolak** dan laporan tidak diteruskan ke unit tujuan.
- Laporan yang dibuat oleh akun selain Petugas tidak melalui gerbang tinjauan Petugas tersebut.

## 7. Melihat, mencari, dan memantau laporan

1. Buka **Laporan Saya** atau **Laporan Masuk Unit**.
2. Cari berdasarkan kode transaksi, judul laporan, atau nama pelapor.
3. Gunakan filter yang tersedia, seperti jenis, status, prioritas, dan unit tujuan; pilih reset untuk menghapus filter.
4. Pilih tombol **Detail** untuk melihat rincian, lampiran, disposisi, dan timeline.
5. Baca **Timeline Progress & Audit Trail** untuk mengetahui siapa melakukan tindakan, perubahan status, dan catatan yang menyertainya.
6. Gunakan kode transaksi ketika berkoordinasi agar laporan yang dimaksud mudah ditemukan.

## 8. Menindaklanjuti laporan di unit tujuan

1. Unit tujuan membuka laporan yang telah tersedia untuk unitnya melalui **Laporan Masuk Unit** atau daftar laporan.
2. Periksa judul, rincian kejadian, prioritas, tindakan awal, dan lampiran.
3. Pada detail laporan atau daftar, pilih **Update Status** / **Tindak Lanjut Status**.
4. Pilih status yang sesuai dan isi catatan progress atau keterangan tindakan. Catatan wajib diisi dan akan muncul di timeline.
5. Pilih **Simpan Perubahan**.

Makna status laporan utama:

| Status | Arti operasional |
|---|---|
| **Diajukan** | Laporan sudah dikirim. Jika dikirim oleh Petugas, periksa dahulu apakah masih menunggu tinjauan Kepala Unit. |
| **Diproses** | Laporan sedang ditangani unit tujuan. |
| **Selesai** | Penanganan laporan utama dinyatakan tuntas. |
| **Ditolak** | Laporan ditolak; dapat terjadi karena keputusan Kepala Unit atau tindak lanjut unit tujuan/Admin. |

### Ketentuan penyelesaian

- Sebelum memilih **Selesai**, pastikan penanganan telah tuntas dan catatan penyelesaian menjelaskan hasilnya.
- Laporan tidak dapat diselesaikan selama masih ada disposisi berstatus **Pending** atau **Diproses**.
- Setelah laporan berstatus **Selesai**, status utama tidak dapat diubah oleh siapa pun. Status **Ditolak** hanya dapat diubah oleh Admin.
- Disposisi baru tidak dapat ditambahkan pada laporan **Selesai** atau **Ditolak**.

## 9. Disposisi lintas unit untuk laporan Insidentil

Disposisi digunakan untuk meminta bantuan atau tindak lanjut dari unit pendukung. Fitur ini hanya tersedia untuk laporan **Insidentil**, setelah laporan lolos persetujuan Kepala Unit bila persetujuan diperlukan, dan selama laporan belum berstatus Selesai atau Ditolak.

### Mengirim disposisi

1. Buka detail laporan Insidentil yang aktif.
2. Pilih **Kirim Disposisi**. Tombol hanya ditampilkan kepada pengguna yang berwenang, yaitu Admin, pengguna unit tujuan, atau petugas penanganan yang tercatat.
3. Pilih **Unit Kerja Pendukung** dan tulis instruksi/catatan dengan jelas.
4. Pilih **Kirim Disposisi**.
5. Disposisi tercatat dengan status **Pending**. Jika status laporan utama sebelumnya Diajukan, sistem mengubahnya menjadi Diproses.
6. Sistem mencegah pengiriman disposisi aktif ganda ke unit penerima yang sama pada laporan yang sama. Disposisi aktif adalah **Pending** atau **Diproses**.

### Menerima dan memperbarui disposisi

1. Unit penerima membuka laporan/disposisi yang diterima.
2. Pilih **Respond Disposisi** pada kartu disposisi.
3. Pilih status disposisi:
   - **Pending** — belum mulai ditindaklanjuti;
   - **Diproses** — sedang dikerjakan;
   - **Selesai** — tindak lanjut unit penerima telah tuntas;
   - **Ditolak** — unit penerima menolak tindak lanjut tersebut.
4. Isi **Catatan Tindak Lanjut Pengerjaan**. Catatan wajib diisi.
5. Bila tersedia, unggah bukti pengerjaan berupa JPG/JPEG/PNG/PDF maksimal 5 MB.
6. Pilih **Simpan Response**. Perubahan status, petugas, catatan, dan waktu tercatat dalam riwayat laporan.

Unit penerima hanya dapat memperbarui disposisi yang ditujukan kepada unitnya; Admin dapat menindaklanjuti disposisi dari unit mana pun. Disposisi dengan status **Pending** dapat dibatalkan Admin atau unit pengirim; disposisi yang sudah diproses/selesai tidak dapat dibatalkan.

> Penyelesaian disposisi dan penyelesaian laporan utama adalah dua hal berbeda. Setelah unit pendukung menyelesaikan disposisinya, unit tujuan tetap perlu menilai keseluruhan laporan dan memperbarui status laporan utama.

## 10. Administrasi master data (khusus Admin)

Menu **Administrator → Master Data** berisi:

- **Unit Kerja RS**: menambah, mengubah, atau menghapus unit. Unit yang masih terhubung ke pengguna atau laporan tidak dapat dihapus.
- **Kategori Laporan**: membuat kategori Rutin atau Insidentil dan mengaktifkan/menonaktifkannya. Kategori yang sudah digunakan dalam transaksi akan dinonaktifkan, bukan dihapus.
- **Manajemen Users**: membuat, mengubah, atau menghapus akun; mengatur nama, username, email, unit kerja, peran akses, dan kata sandi. Username wajib diisi untuk akun baru dan dapat dikosongkan pada akun lama yang masih menggunakan login email.

Saat membuat akun, pilih peran yang benar. Peran **Petugas** memiliki alur tinjauan Kepala Unit ketika membuat laporan, sehingga kesalahan pemilihan peran dapat mengubah alur kerja laporan.

## 11. Praktik pengisian yang disarankan

- Pilih unit tujuan yang memang bertanggung jawab terhadap masalah utama.
- Bedakan masalah utama dengan pekerjaan pendukung: unit tujuan tetap menjadi penanggung jawab laporan, sementara disposisi membagi pekerjaan spesifik ke unit lain.
- Gunakan judul yang ringkas dan dapat dicari, misalnya menyebut perangkat/lokasi dan kendala.
- Tulis kronologi faktual; hindari catatan terlalu umum seperti “mohon ditindaklanjuti”.
- Pilih prioritas sesuai dampak layanan, khususnya saat layanan kritis terhenti.
- Untuk Insidentil, tulis tindakan awal agar unit tujuan mengetahui langkah yang sudah dilakukan.
- Catat hasil aktual pada setiap pembaruan status/disposisi dan lampirkan bukti bila relevan.
- Jangan menandai laporan utama selesai sebelum seluruh pekerjaan terkait tuntas.
- Jangan membagikan akun atau kata sandi. Keluar dari aplikasi setelah memakai perangkat bersama.

## 12. Skenario demo untuk sesi sosialisasi

Gunakan data contoh/non-sensitif saat demonstrasi.

1. Masuk sebagai Petugas.
2. Buat laporan Insidentil, pilih unit tujuan, tentukan prioritas, isi kronologi dan tindakan awal, lalu kirim.
3. Tunjukkan status **Menunggu Tinjauan** kepada peserta.
4. Masuk sebagai Kepala Unit asal, buka laporan, lalu setujui.
5. Masuk sebagai unit tujuan, ubah laporan menjadi **Diproses** dengan catatan tindak lanjut.
6. Kirim disposisi ke unit pendukung dan tunjukkan status **Pending**.
7. Masuk sebagai unit penerima disposisi, ubah menjadi **Diproses** lalu **Selesai** dengan catatan/bukti.
8. Kembali sebagai unit tujuan, pastikan tidak ada disposisi Pending/Diproses, lalu tandai laporan utama **Selesai** dengan catatan hasil.
9. Tunjukkan timeline sebagai bukti jejak proses.

Untuk memperagakan alur penolakan, gunakan laporan terpisah dan tunjukkan bahwa Kepala Unit harus mengisi alasan penolakan.

## 13. Rangka presentasi sosialisasi (8 slide)

1. **Apa itu SI-PELITA?** Tujuan, masalah yang dibantu, dan manfaat pencatatan terpusat.
2. **Siapa melakukan apa?** Petugas, Kepala Unit, unit tujuan, unit pendukung, Admin, dan Direksi.
3. **Peta alur laporan** Diajukan → tinjauan bila dibuat Petugas → penanganan → Selesai/Ditolak.
4. **Demo membuat laporan** Kategori, unit tujuan, prioritas, rincian, tindakan awal Insidentil, dan lampiran.
5. **Demo tinjauan dan penanganan** Persetujuan Kepala Unit dan pembaruan status unit tujuan.
6. **Demo disposisi Insidentil** Mengirim instruksi, menerima respons, bukti tindak lanjut, dan syarat menutup laporan utama.
7. **Cara memantau** Dashboard, daftar laporan, pencarian/filter, detail, dan timeline audit trail.
8. **Pesan kunci dan tanya jawab** Isi laporan lengkap, pilih unit/prioritas tepat, catat setiap tindakan, dan jaga keamanan akun.

## 14. Ringkasan cepat

- **Rutin**: laporan untuk rekap/sensus/survei; tidak menggunakan disposisi.
- **Insidentil**: aduan/kejadian; tindakan awal wajib dan dapat menggunakan disposisi lintas unit.
- **Petugas yang membuat laporan**: menunggu tinjauan Kepala Unit asal.
- **Unit tujuan**: bertanggung jawab memperbarui dan menutup status laporan utama.
- **Unit penerima disposisi**: memperbarui status dan catatan disposisi yang ditujukan kepadanya.
- **Selesai**: status laporan utama tidak dapat dicapai selama ada disposisi Pending/Diproses.
- **Semua pembaruan**: sertakan catatan yang jelas agar tercatat pada timeline.
