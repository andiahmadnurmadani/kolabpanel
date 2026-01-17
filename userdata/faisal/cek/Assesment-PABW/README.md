# DonGiv - Platform Donasi Terpercaya

DonGiv adalah platform donasi terpercaya yang memungkinkan pengguna untuk membantu sesama melalui donasi yang mudah dan aman.

## Fitur

- Donasi mudah dan aman
- Transparansi penggunaan dana
- Dampak nyata untuk penerima bantuan
- Sistem admin untuk mengelola kampanye donasi

## Prasyarat Sistem

- PHP >= 8.1
- Composer
- Database (MySQL, PostgreSQL, atau SQLite)
- Node.js & npm (opsional, untuk asset compilation)

## Instalasi

Ikuti langkah-langkah berikut untuk menginstal dan menjalankan aplikasi:

### 1. Clone repository atau download kode

```bash
git clone <repository-url>
cd Assesment-PABW
```

### 2. Install dependencies PHP

```bash
composer install
```

### 3. Salin file .env dan konfigurasi

```bash
cp .env.example .env
```

Edit file `.env` dan sesuaikan konfigurasi database Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

### 4. Generate application key

```bash
php artisan key:generate
```

### 5. Migrate dan seed database

```bash
php artisan migrate --seed
```

Perintah ini akan:
- Membuat struktur database
- Membuat pengguna admin default (email: admin@gmail.com, password: 12345678)

### 6. Jalankan aplikasi

```bash
php artisan serve
```

Aplikasi akan berjalan di `http://127.0.0.1:8000`

## Akun Default

### Akun Admin
- Email: `admin@gmail.com`
- Password: `12345678`

Anda dapat mengganti password default setelah login.

## Kontribusi

Saat Anda atau rekan Anda melakukan `git pull` dari repository, pastikan untuk menjalankan perintah berikut agar aplikasi tetap berjalan dengan baik:

```bash
# Update dependency PHP
composer install

# Update struktur database (jika ada perubahan migrasi)
php artisan migrate

# Jalankan seeder jika diperlukan (akan menambahkan akun admin jika belum ada)
php artisan db:seed
```

## Troubleshooting

### Masalah Login Setelah Git Pull

Jika Anda atau rekan Anda mengalami masalah login setelah melakukan `git pull`:

1. Pastikan semua dependency PHP terinstal: `composer install`
2. Jalankan migrasi database: `php artisan migrate`
3. Pastikan pengguna admin telah dibuat: `php artisan db:seed`
4. Pastikan file `.env` sudah dikonfigurasi dengan benar
5. Bersihkan cache: `php artisan config:clear` dan `php artisan cache:clear`

### Membuat Akun Admin Manual

Jika akun admin tidak ada, Anda dapat membuatnya melalui Tinker:

```bash
php artisan tinker
```

Lalu di dalam tinker:
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => Hash::make('password123'),
    'role' => 'admin',
]);
```

## API untuk Flutter

Aplikasi ini menyediakan endpoint API untuk digunakan dengan aplikasi Flutter. Endpoint utama untuk mengambil data kampanye donasi adalah:

### Endpoint Campaign
- URL: `http://127.0.0.1:8000/api/campaigns`
- Method: GET
- Response: JSON dengan format:
```json
{
  "message": "Campaigns retrieved successfully",
  "data": [
    {
      "id": 1,
      "title": "Judul Kampanye",
      "description": "Deskripsi kampanye",
      "image": "URL gambar",
      "target_amount": "50000000.00",
      "current_amount": "15000000.00",
      "end_date": "2026-06-30T00:00:00.000000Z",
      "status": "active",
      "created_at": "2025-12-25T08:51:03.000000Z"
    }
  ]
}
```

### Konfigurasi CORS
Aplikasi ini telah dikonfigurasi untuk mengizinkan permintaan dari semua origin (`'*'`) untuk keperluan development. Untuk produksi, sesuaikan konfigurasi di `config/cors.php`.

### Contoh Penggunaan di Flutter
Gunakan package `http` untuk melakukan permintaan ke endpoint API:
```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

Future<List<Map<String, dynamic>>> fetchCampaigns() async {
  final response = await http.get(
    Uri.parse('http://127.0.0.1:8000/api/campaigns'),
    headers: {'Content-Type': 'application/json'},
  );

  if (response.statusCode == 200) {
    final data = json.decode(response.body);
    return List<Map<String, dynamic>>.from(data['data']);
  } else {
    throw Exception('Gagal memuat data kampanye');
  }
}
```
