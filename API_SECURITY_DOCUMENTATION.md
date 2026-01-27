# Dokumentasi Keamanan API

## Ringkasan

API ini dilindungi dengan sistem **API Key Authentication** yang memastikan hanya aplikasi yang terdaftar dan terotorisasi yang dapat mengakses data referensi.

---

## Metode Keamanan

### 1. API Key Authentication

Setiap request ke API harus menyertakan API key yang valid. API key disimpan dalam database dengan hashing (bcrypt) untuk keamanan maksimal.

**Fitur Keamanan:**
- ✅ API Key di-hash menggunakan bcrypt
- ✅ IP Whitelisting (opsional)
- ✅ Status aktif/nonaktif per client
- ✅ Tracking penggunaan (request count, last used)
- ✅ Multiple API keys untuk aplikasi berbeda

---

## Cara Menggunakan API Key

### Opsi 1: Header (Recommended)
```bash
curl -X GET "https://your-domain.com/api/users" \
  -H "X-API-Key: your_api_key_here"
```

### Opsi 2: Authorization Header
```bash
curl -X GET "https://your-domain.com/api/users" \
  -H "Authorization: Bearer your_api_key_here"
```

### Opsi 3: Query Parameter
```bash
curl -X GET "https://your-domain.com/api/users?api_key=your_api_key_here"
```

**Catatan:** Opsi 1 (Header X-API-Key) adalah yang paling direkomendasikan karena lebih aman dan tidak terlihat di URL.

---

## Response Error

### 401 Unauthorized - API Key Tidak Disediakan
```json
{
  "success": false,
  "message": "API key is required. Please provide X-API-Key header or api_key query parameter."
}
```

### 401 Unauthorized - API Key Tidak Valid
```json
{
  "success": false,
  "message": "Invalid API key."
}
```

### 403 Forbidden - API Key Nonaktif
```json
{
  "success": false,
  "message": "API key is inactive. Please contact administrator."
}
```

### 403 Forbidden - IP Tidak Terdaftar
```json
{
  "success": false,
  "message": "IP address is not whitelisted."
}
```

---

## Manajemen API Clients

### Membuat API Client Baru

#### Via Artisan Command (Recommended)
```bash
php artisan api:create-client "Nama Aplikasi" \
  --description="Deskripsi aplikasi" \
  --ip="192.168.1.100,192.168.1.101" \
  --active
```

**Contoh:**
```bash
php artisan api:create-client "Aplikasi HRIS" \
  --description="Aplikasi Human Resources Information System" \
  --ip="203.142.80.100" \
  --active
```

**Output:**
```
API Client created successfully!

+----+------------------+------------------+------------+--------+
| ID | Name             | Description      | IP         | Status |
+----+------------------+------------------+------------+--------+
| 1  | Aplikasi HRIS    | Aplikasi HRIS... | 203.142... | Active |
+----+------------------+------------------+------------+--------+

API Key (save this securely, it won't be shown again):
pd_abc123xyz456def789ghi012jkl345mno678pqr901stu234vwx567yz

Use this API key in your requests:
Header: X-API-Key: pd_abc123xyz456def789ghi012jkl345mno678pqr901stu234vwx567yz
Or Query: ?api_key=pd_abc123xyz456def789ghi012jkl345mno678pqr901stu234vwx567yz
```

**⚠️ PENTING:** Simpan API key dengan aman! API key hanya ditampilkan sekali saat dibuat dan tidak dapat dilihat lagi setelahnya.

#### Via Seeder
```bash
php artisan db:seed --class=ApiClientSeeder
```

#### Via Database Langsung
```php
use App\Models\ApiClient;

$apiKey = ApiClient::generateApiKey();
$client = ApiClient::create([
    'name' => 'Nama Aplikasi',
    'api_key' => $apiKey,
    'description' => 'Deskripsi',
    'ip_whitelist' => '192.168.1.100,192.168.1.101', // Opsional
    'is_active' => true,
]);

// Simpan $apiKey dengan aman!
```

---

### Melihat Daftar API Clients

```bash
php artisan api:list-clients
```

**Output:**
```
API Clients:

+----+----------------------+------------------+------------+--------+-----------+---------------------+------------+
| ID | Name                 | Description      | IP         | Status | Requests  | Last Used           | Created    |
+----+----------------------+------------------+------------+--------+-----------+---------------------+------------+
| 1  | Aplikasi HRIS        | Aplikasi HRIS... | 203.142... | ✓ Active | 1,234    | 2026-01-27 10:30:00 | 2026-01-27 |
| 2  | Aplikasi Payroll     | Aplikasi Pay...  | All IPs    | ✓ Active | 5,678    | 2026-01-27 09:15:00 | 2026-01-26 |
+----+----------------------+------------------+------------+--------+-----------+---------------------+------------+
```

---

### Mengelola API Client via Database

#### Menonaktifkan API Client
```php
$client = ApiClient::find(1);
$client->update(['is_active' => false]);
```

#### Mengaktifkan Kembali
```php
$client = ApiClient::find(1);
$client->update(['is_active' => true]);
```

#### Update IP Whitelist
```php
$client = ApiClient::find(1);
$client->update(['ip_whitelist' => '192.168.1.100,192.168.1.101,203.142.80.100']);
```

#### Menghapus IP Whitelist (Allow All IPs)
```php
$client = ApiClient::find(1);
$client->update(['ip_whitelist' => null]);
```

---

## IP Whitelisting

IP Whitelisting adalah fitur opsional yang membatasi akses API hanya dari IP address tertentu.

### Konfigurasi IP Whitelist

**Format:** Comma-separated list of IP addresses
```
192.168.1.100,192.168.1.101,203.142.80.100
```

**Contoh:**
```bash
php artisan api:create-client "Aplikasi Internal" \
  --ip="192.168.1.100,192.168.1.101"
```

### Menghapus IP Whitelist

Jika `ip_whitelist` adalah `null` atau kosong, semua IP address diizinkan.

```php
$client->update(['ip_whitelist' => null]);
```

---

## Best Practices

### 1. Simpan API Key dengan Aman
- ✅ Jangan commit API key ke version control (Git)
- ✅ Gunakan environment variables
- ✅ Simpan di secure vault atau password manager
- ✅ Jangan share API key melalui email atau chat yang tidak aman

### 2. Rotasi API Key
- Ganti API key secara berkala (misalnya setiap 3-6 bulan)
- Buat API client baru, update aplikasi, lalu nonaktifkan yang lama

### 3. IP Whitelisting
- Gunakan IP whitelisting untuk aplikasi yang berjalan di server dengan IP tetap
- Jangan gunakan IP whitelisting untuk aplikasi mobile atau yang IP-nya berubah

### 4. Monitoring
- Pantau `request_count` dan `last_used_at` untuk mendeteksi aktivitas mencurigakan
- Nonaktifkan API key yang tidak digunakan lagi

### 5. Error Handling
- Implementasikan retry logic dengan exponential backoff
- Log semua error response untuk debugging

---

## Contoh Implementasi di Aplikasi Lain

### PHP (Guzzle)
```php
use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => 'https://your-domain.com/api/',
    'headers' => [
        'X-API-Key' => env('PD_SYSTEM_API_KEY'),
        'Accept' => 'application/json',
    ],
]);

$response = $client->get('users');
$users = json_decode($response->getBody(), true);
```

### JavaScript (Axios)
```javascript
const axios = require('axios');

const apiClient = axios.create({
  baseURL: 'https://your-domain.com/api/',
  headers: {
    'X-API-Key': process.env.PD_SYSTEM_API_KEY,
    'Accept': 'application/json',
  },
});

const response = await apiClient.get('users');
const users = response.data;
```

### Python (Requests)
```python
import requests
import os

headers = {
    'X-API-Key': os.getenv('PD_SYSTEM_API_KEY'),
    'Accept': 'application/json',
}

response = requests.get(
    'https://your-domain.com/api/users',
    headers=headers
)

users = response.json()
```

### cURL
```bash
# Set API key sebagai environment variable
export API_KEY="your_api_key_here"

# Gunakan dalam request
curl -X GET "https://your-domain.com/api/users" \
  -H "X-API-Key: $API_KEY" \
  -H "Accept: application/json"
```

---

## Troubleshooting

### Error: "API key is required"
**Solusi:** Pastikan Anda menyertakan API key di header atau query parameter.

### Error: "Invalid API key"
**Solusi:** 
- Periksa apakah API key yang digunakan benar
- Pastikan tidak ada spasi atau karakter tambahan
- Pastikan API key belum diubah atau dihapus

### Error: "API key is inactive"
**Solusi:** Hubungi administrator untuk mengaktifkan API key Anda.

### Error: "IP address is not whitelisted"
**Solusi:** 
- Periksa IP address Anda: `curl ifconfig.me`
- Hubungi administrator untuk menambahkan IP Anda ke whitelist
- Atau minta administrator menghapus IP whitelist jika tidak diperlukan

---

## Struktur Database

### Tabel: `api_clients`

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| name | string | Nama aplikasi/client |
| api_key | string(64) | API key (hashed) |
| description | text | Deskripsi aplikasi |
| ip_whitelist | string | IP whitelist (comma-separated) |
| is_active | boolean | Status aktif/nonaktif |
| last_used_at | timestamp | Terakhir digunakan |
| request_count | integer | Jumlah request |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diupdate |

---

## Security Considerations

1. **API Key Storage**: API key di-hash menggunakan bcrypt, sehingga tidak dapat dilihat kembali setelah dibuat.

2. **HTTPS**: Pastikan API diakses melalui HTTPS di production untuk mencegah API key terintercept.

3. **Rate Limiting**: Pertimbangkan untuk menambahkan rate limiting untuk mencegah abuse.

4. **Logging**: Semua request dicatat untuk audit trail.

5. **Rotation**: Rotasi API key secara berkala untuk meningkatkan keamanan.

---

## Support

Untuk pertanyaan atau masalah terkait keamanan API:
1. Hubungi administrator sistem
2. Periksa log aplikasi untuk detail error
3. Pastikan API key masih aktif dan IP terdaftar (jika menggunakan whitelist)
