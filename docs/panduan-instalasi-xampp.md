# Panduan Instalasi Server SI-PELITA di Windows dengan XAMPP

Panduan ini untuk memasang SI-PELITA pada **komputer server Windows 10/11 yang baru**, tanpa Laragon. Contoh instalasi menggunakan `C:\xampp` untuk XAMPP dan `C:\xampp\htdocs\sipelita` untuk aplikasi.

> **Catatan penting:** XAMPP terutama ditujukan untuk pengembangan. Panduan ini cocok untuk server internal/intranet dengan pengamanan dan pemeliharaan yang memadai, tetapi XAMPP bukan platform yang otomatis aman untuk server publik. Jangan mengekspos phpMyAdmin atau MySQL ke internet. Untuk layanan yang harus tersedia dari internet atau menangani data sensitif, gunakan server produksi yang dikelola dan dikonfigurasi secara profesional. Pada 2026, Windows 10 reguler sudah melewati akhir dukungan; gunakan Windows 11 yang masih menerima pembaruan keamanan jika memungkinkan.

## 1. Siapkan komputer server dan jaringan

1. Gunakan komputer khusus server yang tidak sering dimatikan dan sambungkan ke jaringan yang stabil.
2. Instal seluruh pembaruan Windows dan gunakan akun Windows Administrator hanya untuk instalasi serta pemeliharaan.
3. Pastikan komputer memperoleh alamat IP tetap/reservasi DHCP di jaringan lokal. Catat alamatnya, misalnya `192.168.1.50`.
4. Tentukan alamat yang akan dipakai pengguna, misalnya `http://192.168.1.50`, atau nama DNS internal yang diarahkan ke IP server.
5. Pastikan tidak ada aplikasi lain yang menggunakan port Apache `80`/`443` atau MySQL `3306`.

Pengguna yang mengakses dari komputer lain harus memakai alamat IP/nama DNS server, **bukan** `localhost`. `localhost` selalu merujuk ke komputer pengguna itu sendiri.

## 2. Instal XAMPP

1. Unduh installer XAMPP dari situs resmi Apache Friends.
2. Instal ke `C:\xampp` (hindari folder seperti `Program Files` agar lebih mudah mengatur izin dan konfigurasi).
3. Pastikan paket Apache, MySQL/MariaDB, PHP, dan phpMyAdmin terpasang.
4. Buka XAMPP Control Panel sebagai Administrator dan mulai **Apache** serta **MySQL**.
5. Buka `http://localhost` pada komputer server untuk memastikan Apache berjalan.

Aktifkan ekstensi PHP yang diperlukan Laravel pada `C:\xampp\php\php.ini`. Pastikan baris berikut tidak diawali `;`:

```ini
extension=curl
extension=fileinfo
extension=mbstring
extension=mysqli
extension=openssl
extension=pdo_mysql
extension=zip
```

Beberapa ekstensi mungkin sudah aktif atau ditulis dengan variasi format pada `php.ini`; jangan menambahkan duplikat jika sudah aktif. Pastikan juga ekstensi inti `ctype`, `json`, `tokenizer`, dan `xml` tersedia di build PHP XAMPP. Setelah perubahan, restart Apache.

## 3. Pasang Composer, Git, dan Node.js

Pasang:

- **Composer** untuk memasang dependency PHP;
- **Git** jika source akan diambil langsung dari repository;
- **Node.js versi LTS** untuk memasang dependency frontend dan membangun asset.

Setelah instalasi, buka PowerShell baru dan periksa:

```powershell
where.exe php
php -v
composer --version
git --version
node --version
npm.cmd --version
```

Pastikan `where.exe php` menunjuk ke `C:\xampp\php\php.exe`. Jika tidak, tambahkan folder tersebut ke `PATH` Windows lalu buka ulang PowerShell. Cara sementara untuk sesi PowerShell saat ini:

```powershell
$env:Path = "C:\xampp\php;" + $env:Path
```

Jalankan `php -v` lagi untuk memastikan PHP yang benar digunakan.

## 4. Salin source aplikasi ke server

Pastikan aplikasi berada di:

```text
C:\xampp\htdocs\sipelita
```

Pilih salah satu cara:

- **Clone repository** jika Git dan akses repository tersedia:

  ```powershell
  Set-Location C:\xampp\htdocs
  git clone https://github.com/irphandarmero/sipelita.git sipelita
  ```

- **Salin paket source/release** dari komputer pengembangan ke `C:\xampp\htdocs\sipelita`. Jangan salin folder `.git` jika tidak diperlukan.

Pastikan folder tersebut berisi `artisan`, `composer.json`, `composer.lock`, dan folder `public`. Jangan salin `.env` dari komputer lain sebagai konfigurasi produksi; buat `.env` baru khusus server. Jangan sertakan folder `vendor` atau `node_modules` dari komputer lain.

## 5. Buat database dan akun database aplikasi

1. Buka `http://localhost/phpmyadmin` pada komputer server.
2. Buat database `sipelita` dengan collation `utf8mb4_unicode_ci`.
3. Pada **User accounts**, buat pengguna database baru, misalnya `sipelita_app`, dengan host `localhost` dan password yang kuat serta unik.
4. Berikan pengguna tersebut hak akses yang diperlukan hanya pada database `sipelita` (untuk awal instalasi, beri seluruh privileges pada database itu saja).
5. Simpan nama pengguna dan password dengan aman untuk konfigurasi `.env`.

Jangan gunakan akun `root` untuk koneksi aplikasi. Jika phpMyAdmin meminta kata sandi root, gunakan kredensial MySQL yang sudah ditetapkan pada instalasi server.

## 6. Buat konfigurasi `.env` untuk server

Buka PowerShell sebagai Administrator, lalu:

```powershell
Set-Location C:\xampp\htdocs\sipelita
```

Buat `.env` dari template bila belum tersedia:

```powershell
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
```

Edit `.env` dan sesuaikan nilai berikut dengan IP/nama server serta kredensial database:

```dotenv
APP_NAME="SI-PELITA"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://192.168.1.50

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipelita
DB_USERNAME=sipelita_app
DB_PASSWORD="GANTI_DENGAN_PASSWORD_DATABASE"
```

Ganti `192.168.1.50` dengan alamat server yang sebenarnya. Untuk penggunaan dengan DNS internal atau HTTPS, isi `APP_URL` dengan URL yang akan dipakai pengguna. Pastikan nilai password di `.env` benar; tanda kutip dapat digunakan bila password memiliki karakter khusus.

Jangan membagikan atau memasukkan `.env` ke repository. File tersebut berisi rahasia server.

## 7. Instal dependency dan siapkan Laravel

Dari PowerShell pada folder proyek, jalankan:

```powershell
composer install --no-dev --optimize-autoloader
composer check-platform-reqs
php artisan key:generate
php artisan optimize:clear
```

`key:generate` digunakan sekali saat instalasi baru dan mengisi `APP_KEY` di `.env`. Jangan membuat ulang key pada server yang sudah digunakan karena dapat membuat data terenkripsi atau sesi pengguna yang ada tidak dapat dibaca.

Jika `composer install` gagal karena versi PHP atau ekstensi, periksa kembali hasil `where.exe php` dan konfigurasi ekstensi di `C:\xampp\php\php.ini`.

## 8. Buat tabel dan data awal

Pada database baru dan kosong, jalankan:

```powershell
php artisan migrate --seed --force
```

Seeder aplikasi membuat unit kerja, kategori laporan, dan akun awal, termasuk admin:

```text
Email: admin@sipelita.com
Password awal: password123
```

Segera setelah instalasi, login dan ganti password awal dengan password kuat. Tambahkan atau periksa akun, unit kerja, dan kategori yang akan digunakan sebelum aplikasi dibuka untuk seluruh pengguna.

Jika database sudah pernah dimigrasikan atau telah berisi data, gunakan `php artisan migrate --force` saja dan jangan jalankan seeder ulang. Jangan gunakan `migrate:fresh` pada server yang menyimpan data karena perintah itu menghapus semua tabel.

## 9. Siapkan storage dan izin folder

Buat symbolic link lampiran:

```powershell
php artisan storage:link
```

Lampiran yang diunggah disimpan di `storage\app\public` dan diakses melalui `public\storage`.

Pastikan akun Windows yang menjalankan Apache dapat menulis ke folder berikut:

```text
C:\xampp\htdocs\sipelita\storage
C:\xampp\htdocs\sipelita\bootstrap\cache
```

Atur izin melalui **Properties > Security** pada folder tersebut untuk akun yang menjalankan Apache. Berikan izin tulis yang diperlukan hanya pada folder runtime ini; jangan memberi akses tulis umum ke seluruh source aplikasi. Hindari memberi izin `Everyone: Full Control`.

## 10. Bangun asset produksi

Pada server, dari folder proyek jalankan:

```powershell
npm.cmd install
npm.cmd run production
```

Jika asset sudah dibangun sebagai bagian dari release tepercaya, asset tersebut dapat disertakan saat menyalin source dan langkah Node.js di server bisa dilewati. Bila ada perubahan pada CSS atau JavaScript, bangun ulang asset dan pastikan file hasil build berada di folder `public`.

## 11. Konfigurasikan Apache agar bisa diakses pengguna jaringan

Apache harus melayani folder `public` aplikasi. Jangan arahkan document root ke root proyek karena folder selain `public` tidak boleh dapat diakses melalui web.

1. Buka `C:\xampp\apache\conf\httpd.conf`.
2. Pastikan modul rewrite dan konfigurasi VirtualHost aktif (tidak diawali `#`):

   ```apache
   LoadModule rewrite_module modules/mod_rewrite.so
   Include conf/extra/httpd-vhosts.conf
   ```

3. Tambahkan konfigurasi berikut ke `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

   ```apache
   <VirtualHost *:80>
       ServerName sipelita.local
       DocumentRoot "C:/xampp/htdocs/sipelita/public"

       <Directory "C:/xampp/htdocs/sipelita/public">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

4. Periksa konfigurasi Apache:

   ```powershell
   C:\xampp\apache\bin\httpd.exe -t
   ```

5. Jika hasilnya `Syntax OK`, restart Apache di XAMPP Control Panel.

### Cara pengguna membuka aplikasi

- **Dengan IP server:** akses `http://192.168.1.50`. Jika request melalui IP tidak masuk ke VirtualHost tersebut, tambahkan `ServerAlias 192.168.1.50` pada VirtualHost. Ganti dengan IP server yang sebenarnya.
- **Dengan nama DNS internal:** minta administrator jaringan membuat record `sipelita.local` yang menunjuk ke IP server, lalu pengguna membuka `http://sipelita.local`.
- Untuk pengujian satu komputer saja, tambahkan `127.0.0.1 sipelita.local` pada file `C:\Windows\System32\drivers\etc\hosts` di komputer tersebut. Entri ini hanya berlaku untuk komputer yang diedit; pengguna jaringan memerlukan DNS internal atau entri hosts masing-masing yang menunjuk ke IP server.

## 12. Atur Windows Firewall dan layanan otomatis

1. Buat aturan **inbound TCP port 80** pada Windows Defender Firewall, dengan profil jaringan yang sesuai dan cakupan sesempit mungkin—idealnya hanya subnet internal rumah sakit.
2. Jangan membuka port MySQL `3306` atau phpMyAdmin ke jaringan pengguna.
3. Jika memakai HTTPS, atur sertifikat TLS tepercaya dan buka hanya port yang dibutuhkan. Jangan anggap koneksi HTTP aman untuk mengirim data melalui jaringan yang tidak tepercaya.
4. Konfigurasikan Apache dan MySQL agar mulai otomatis saat komputer server dinyalakan, menggunakan pengaturan service XAMPP bila sesuai kebijakan organisasi. Uji restart komputer untuk memastikan keduanya kembali berjalan.
5. Jangan mengekspos port Apache server ini ke internet melalui router tanpa desain keamanan dan administrasi server yang memadai.

## 13. Pemeriksaan sebelum digunakan

- Buka alamat server dari komputer server dan setidaknya satu komputer pengguna di jaringan.
- Login dengan akun admin awal lalu ubah password-nya.
- Periksa data unit kerja, kategori laporan, serta akun dan perannya.
- Buat laporan uji, unggah lampiran, buka kembali lampiran, lalu periksa alur laporan.
- Pastikan pengguna lain tidak dapat membuka phpMyAdmin melalui alamat jaringan server.
- Pastikan `APP_ENV=production` dan `APP_DEBUG=false` tetap terpasang di `.env`.

## 14. Perintah setelah aplikasi berjalan

Jalankan perintah ini dari `C:\xampp\htdocs\sipelita` menggunakan PowerShell Administrator bila memerlukan izin:

```powershell
# Pasang migrasi baru tanpa menghapus data
php artisan migrate --force

# Bersihkan cache setelah mengubah .env
php artisan optimize:clear

# Bangun ulang cache konfigurasi dan tampilan untuk produksi
php artisan config:cache
php artisan view:cache
```

Jangan jalankan `php artisan serve` sebagai cara menjalankan server utama. Apache XAMPP yang melayani aplikasi; MySQL XAMPP menyediakan database.

## 15. Backup dan pemeliharaan

- Jadwalkan backup database `sipelita` dan folder `storage\app\public` secara rutin ke lokasi lain yang aman.
- Simpan backup `.env` secara terpisah dan terenkripsi karena memuat kredensial serta `APP_KEY`.
- Uji pemulihan backup, bukan hanya proses membuat backup.
- Sebelum memperbarui source atau menjalankan migrasi, buat backup dan tinjau perubahan.
- Setelah deploy versi baru, jalankan migrasi yang diperlukan, kompilasi asset jika berubah, lalu bersihkan dan bangun ulang cache.
- Pantau log Laravel di `storage\logs` serta log Apache dan MySQL. Batasi akses ke log tersebut karena dapat memuat informasi sensitif.
- Pasang pembaruan Windows dan XAMPP secara berkala serta tinjau keamanan server dan jaringan secara berkala.

## 16. Pemecahan masalah

| Gejala | Pemeriksaan atau tindakan |
|---|---|
| `where.exe php` tidak menunjuk ke XAMPP | Tambahkan `C:\xampp\php` ke `PATH` Windows, buka PowerShell baru, lalu periksa kembali. |
| Composer melaporkan ekstensi atau versi PHP tidak sesuai | Pastikan Composer memakai PHP XAMPP dan aktifkan ekstensi yang diperlukan pada `C:\xampp\php\php.ini`. |
| Koneksi database gagal | Pastikan MySQL berjalan, database dan pengguna aplikasi sudah dibuat, lalu cocokkan kredensial pada `.env`. |
| Apache atau MySQL gagal start | Periksa konflik port `80`, `443`, atau `3306`, kemudian tinjau log XAMPP untuk mengetahui proses yang menggunakan port. |
| Pengguna lain tidak dapat membuka aplikasi | Pastikan Apache berjalan, IP server benar dan tetap, aturan Firewall mengizinkan TCP 80 dari jaringan yang dimaksud, serta DNS internal sudah diarahkan dengan benar. |
| Apache menampilkan 404 atau halaman Laravel tidak muncul | Periksa VirtualHost, `DocumentRoot` yang harus menunjuk ke `public`, modul `mod_rewrite`, dan `AllowOverride All`. |
| Error izin saat menulis cache atau mengunggah lampiran | Berikan izin tulis pada `storage` dan `bootstrap\cache` kepada akun yang menjalankan Apache, bukan ke seluruh folder proyek. |
| Lampiran tidak dapat dibuka | Periksa keberadaan `public\storage`, jalankan `php artisan storage:link`, dan pastikan file berada di `storage\app\public`. |
| Perubahan `.env` tidak terbaca | Jalankan `php artisan optimize:clear`, lalu jalankan kembali `php artisan config:cache` jika server menggunakan cache konfigurasi. |
