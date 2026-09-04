# Deploy WebGroceries

## Option 0 — Kongsi demo segera (percuma, 2 minit, laptop kena ON)

Sesuai untuk tunjuk kepada orang buat sementara. Tiada signup, tiada kad.

```bash
brew install cloudflared
php -S localhost:8000 -t public        # terminal 1
cloudflared tunnel --url http://localhost:8000   # terminal 2
```

Dapat URL `https://xxx.trycloudflare.com` yang boleh dibuka sesiapa. Tutup laptop = link mati.

---

## Option 1 — Hosting percuma kekal: InfinityFree (tiada kad kredit)

** belonged: akaun percuma InfinityFree (https://www.infinityfree.com) — PHP + MySQL + subdomain + SSL percuma.*

### 1. Daftar & cipta site
- Sign up (email sahaja) → Create Account → pilih **free subdomain** (cth. `webgroceries.rf.gd`).
- Catat: control panel URL, FTP username/password/host.

### 2. Database
- Panel → **MySQL Databases** → Create Database (dapat nama cth. `if0_12345678_grocer`) → catat **host** (cth. `sql123.epizy.com`), DB, user, password.
- Buka **phpMyAdmin** dari panel → pilih DB → **Import**:
  1. `database/schema.sql` — **padam 3 baris teratas dulu** (`CREATE DATABASE` / `USE`), akaun free tak boleh cipta DB.
  2. `database/seed.sql`.
- Jika import FK error, maklumkan saya (ada pelan B tanpa foreign key).

### 3. Upload fail
- ZIP folder projek di laptop → upload ZIP ke `htdocs/` → **Extract** dalam File Manager (lebih laju dari FTP satu-persatu).
- Atau FTP dengan FileZilla ke `htdocs/`.
- Pastikan **hidden files ikut sama**: `.htaccess` di root projek dan dalam `public/`, `public/assets/images/*/`. (File Manager → Settings → tick "Show Hidden Files".)
- Gambar produk (~70 fail) wajib ikut — kalau tidak, produk tiada gambar.

### 4. `.env` di server (JANGAN upload `.env` laptop)
Cipta fail `.env` dalam File Manager di folder projek:
```
DB_HOST=sql123.epizy.com
DB_PORT=3306
DB_NAME=if0_12345678_grocer
DB_USER=if0_12345678
DB_PASS=(password dari panel)
APP_URL=https://webgroceries.rf.gd
APP_ENV=production
APP_KEY=(32 aksara rawak, jana di https://www.uuidgenerator.net/ dan buang dash)
```

### 5. PHP version
- Panel → **Select PHP Version** → pilih **8.2 atau 8.3** → tick `pdo_mysql`, `gd`, `mbstring`, `fileinfo`.

### 6. SSL (HTTPS)
- Panel → **Free SSL Certificates** → request untuk domain → Install. Buka `https://...`.

### 7. Kenapa root `.htaccess` wujud?
Host percuma paksa docroot `htdocs/` — fail `.htaccess` di root projek route semua trafik ke `public/` dan **block** `app/`, `config/`, `database/`, `storage/`, `.env` dari web. Tiada kod perlu diubah.

### 8. Uji
Daftar → login → beli → checkout → order. Login admin → `/admin`.

### Had pelan percuma (jujur)
- Server kongsi: kadang perlahan, ada had inodes/harian. Sesuai demo & portfolio, bukan production trafik tinggi.
- Tiada cron, tiada SSH. Update = upload fail yang berubah sahaja (jangan overwrite `.env` atau gambar yang dimuat naik).

---

## Option 2 — Berbayar (bila dah serius)
Shared hosting Malaysia (Exabytes/Shinjiru/Hostinger, ~RM50–100/tahun) atau VPS — ikut `README.md` bahagian setup + `deploy/apache-vhost.conf` / `deploy/nginx.conf`, dengan document root terus ke `public/`.
