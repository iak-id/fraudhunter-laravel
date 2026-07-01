# FraudHunter Laravel SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fraudhunter/laravel-sdk.svg?style=flat-square)](https://packagist.org/packages/fraudhunter/laravel-sdk)
[![Total Downloads](https://img.shields.io/packagist/dt/fraudhunter/laravel-sdk.svg?style=flat-square)](https://packagist.org/packages/fraudhunter/laravel-sdk)

Integrasikan aplikasi Laravel Anda dengan sistem deteksi fraud **FraudHunter** secara mudah menggunakan Event Listeners.

## Fitur
- **Otomatis**: Melacak Login, Logout, dan Reset Password tanpa kode tambahan.
- **Fleksibel**: Kirim transaksi kustom untuk analisis risiko mendalam.
- **Kompetibel**: Mendukung Laravel 5.0 sampai 11.0+.
- **Synchronous**: Pengiriman data dilakukan secara real-time.

## Instalasi

1. Tambahkan package via composer:
```bash
composer require fraudhunter/laravel-sdk
```

2. Tambahkan Service Provider (untuk Laravel < 5.5):
```php
// config/app.php
'providers' => [
    // ...
    FraudHunter\Laravel\FraudHunterServiceProvider::class,
],
```

3. Publish konfigurasi:
```bash
php artisan vendor:publish --provider="FraudHunter\Laravel\FraudHunterServiceProvider"
```

4. Tambahkan environment variables di file `.env`:
```env
FRAUDHUNTER_API_URL=http://your-fraudhunter-server:8080
FRAUDHUNTER_API_KEY=fh_live_xxxxxxxxxxxx
FRAUDHUNTER_PLATFORM=WL
```

## Penggunaan

### 1. Pelacakan Otomatis
Secara default, SDK ini mendengarkan event berikut:
- `Illuminate\Auth\Events\Login`
- `Illuminate\Auth\Events\Logout`
- `Illuminate\Auth\Events\PasswordReset`

Anda bisa menyesuaikan mapping ini di file `config/fraudhunter.php`.

### 2. Analisis Transaksi Manual
Anda bisa memanggil `FraudHunterClient` untuk menganalisis transaksi sebelum diproses:

```php
use FraudHunter\Laravel\FraudHunterClient;

public function processPayment(FraudHunterClient $fraudHunter)
{
    $result = $fraudHunter->analyzeTransaction([
        'account_id'         => 'ACC001',
        'user_id'            => '123',
        'tenant_id'          => 'TENANT_A',
        'amount'             => 5000000,
        'currency'           => 'IDR',
        'transaction_type'   => 'PAYMENT',
        'platform'           => 'WL',
        'ip_address'         => request()->ip(),
        'device_id'          => 'DEV-123',
        'destination_number' => '08123456789',  // opsional: nomor tujuan
        'product_code'       => 'PULSA-10K',    // opsional: kode produk
    ]);

    if ($result['recommended_action'] === 'REJECT') {
        return response()->json(['error' => 'Transaksi ditolak oleh sistem keamanan'], 403);
    }

    // Lanjutkan proses pembayaran...
}
```

### 3. Log Aktivitas Kustom
```php
$fraudHunter->logActivity([
    'account_id'    => 'ACC001',
    'user_id'       => '123',
    'tenant_id'     => 'TENANT_A',
    'platform'      => 'WL',
    'activity_type' => 'UPDATE_PROFILE',
    'status'        => 'SUCCESS',
]);
```

## Lisensi
MIT
