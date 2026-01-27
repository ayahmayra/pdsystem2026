# Prompt Implementasi API PD System 2026

## Konteks
Saya perlu mengintegrasikan aplikasi saya dengan **PD System 2026** untuk mengambil data referensi pegawai dan master data. PD System adalah sistem manajemen perjalanan dinas yang menyediakan API untuk mengakses data referensi.

## Informasi API

### Base URL
```
https://pdsystem.trust-idn.id/api
```

### Autentikasi
API menggunakan **API Key Authentication**. API key harus disertakan di setiap request melalui:
- **Header (Recommended):** `X-API-Key: your_api_key_here`
- **Authorization Header:** `Authorization: Bearer your_api_key_here`
- **Query Parameter:** `?api_key=your_api_key_here`

**Catatan:** API key akan diberikan oleh administrator PD System. Hubungi administrator untuk mendapatkan API key.

---

## Endpoint yang Tersedia

### 1. User API
**Endpoint:** `/api/users`

**Query Parameters:**
- `unit_id` (integer) - Filter by unit ID
- `instansi_id` (integer) - Filter by instansi ID
- `position_id` (integer) - Filter by position ID
- `rank_id` (integer) - Filter by rank ID
- `travel_grade_id` (integer) - Filter by travel grade ID
- `employee_type` (string) - Filter by employee type (PNS, PPPK, PPPK PW, Non ASN)
- `search` (string) - Search by name, NIP, or email
- `with` (string) - Comma-separated relationships (unit, position.echelon, rank, travelGrade)
- `per_page` (integer) - Items per page (default: 15, max: 100)
- `page` (integer) - Page number

**Contoh Request:**
```bash
GET https://pdsystem.trust-idn.id/api/users?unit_id=1&with=unit,position,rank
Headers:
  X-API-Key: your_api_key_here
```

**Response Format:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john.doe@example.com",
      "nip": "198001012010011001",
      "unit": {
        "id": 1,
        "code": "U001",
        "name": "Bidang Teknologi Informasi"
      },
      "position": {
        "id": 1,
        "name": "Kepala Bidang",
        "echelon": {
          "id": 1,
          "code": "E.II",
          "name": "Eselon II"
        }
      },
      "rank": {
        "id": 1,
        "code": "IV/a",
        "name": "Pembina Utama Muda"
      }
    }
  ],
  "links": {...},
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 150
  }
}
```

---

### 2. Unit API
**Endpoint:** `/api/units`

**Query Parameters:**
- `parent_id` (integer|null) - Filter by parent ID (use 'null' for root units)
- `search` (string) - Search by code or name
- `with` (string) - Relationships (parent, children)
- `per_page` (integer) - Items per page
- `page` (integer) - Page number

**Contoh Request:**
```bash
GET https://pdsystem.trust-idn.id/api/units?with=parent,children
Headers:
  X-API-Key: your_api_key_here
```

---

### 3. Instansi API
**Endpoint:** `/api/instansis`

**Query Parameters:**
- `search` (string) - Search by code or name
- `per_page` (integer) - Items per page
- `page` (integer) - Page number

---

### 4. Position API
**Endpoint:** `/api/positions`

**Query Parameters:**
- `echelon_id` (integer) - Filter by echelon ID
- `type` (string) - Filter by position type
- `search` (string) - Search by name
- `with` (string) - Relationships (echelon)
- `per_page` (integer) - Items per page
- `page` (integer) - Page number

---

### 5. Rank API
**Endpoint:** `/api/ranks`

**Query Parameters:**
- `search` (string) - Search by code or name
- `per_page` (integer) - Items per page
- `page` (integer) - Page number

---

### 6. Travel Grade API
**Endpoint:** `/api/travel-grades`

**Query Parameters:**
- `search` (string) - Search by code or name
- `per_page` (integer) - Items per page
- `page` (integer) - Page number

---

### 7. Echelon API
**Endpoint:** `/api/echelons`

**Query Parameters:**
- `search` (string) - Search by code or name
- `per_page` (integer) - Items per page
- `page` (integer) - Page number

---

### 8. SPT API
**Endpoint:** `/api/spts`

**Query Parameters:**
- `signed_by_user_id` (integer) - Filter by signed by user ID
- `nota_dinas_id` (integer) - Filter by nota dinas ID
- `status` (string) - Filter by status
- `search` (string) - Search by doc_no or assignment_title
- `with` (string) - Relationships (signedByUser, members.user)
- `per_page` (integer) - Items per page
- `page` (integer) - Page number

---

### 9. SPT Member API
**Endpoint:** `/api/spt-members`

**Query Parameters:**
- `spt_id` (integer) - Filter by SPT ID
- `user_id` (integer) - Filter by user ID
- `with` (string) - Relationships (user, spt)
- `per_page` (integer) - Items per page
- `page` (integer) - Page number

---

## Contoh Implementasi

### PHP (Guzzle)
```php
<?php

use GuzzleHttp\Client;

class PdSystemApiClient
{
    private $client;
    private $apiKey;
    private $baseUrl = 'https://pdsystem.trust-idn.id/api';

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->client = new Client([
            'base_uri' => $this->baseUrl . '/',
            'headers' => [
                'X-API-Key' => $this->apiKey,
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Get all users
     */
    public function getUsers($filters = [])
    {
        $response = $this->client->get('users', [
            'query' => $filters
        ]);
        
        return json_decode($response->getBody(), true);
    }

    /**
     * Get single user by ID
     */
    public function getUser($id, $with = [])
    {
        $query = [];
        if (!empty($with)) {
            $query['with'] = implode(',', $with);
        }
        
        $response = $this->client->get("users/{$id}", [
            'query' => $query
        ]);
        
        return json_decode($response->getBody(), true);
    }

    /**
     * Get all units
     */
    public function getUnits($filters = [])
    {
        $response = $this->client->get('units', [
            'query' => $filters
        ]);
        
        return json_decode($response->getBody(), true);
    }

    /**
     * Get all positions
     */
    public function getPositions($filters = [])
    {
        $response = $this->client->get('positions', [
            'query' => $filters
        ]);
        
        return json_decode($response->getBody(), true);
    }
}

// Usage
$apiKey = 'your_api_key_here'; // Get from administrator
$api = new PdSystemApiClient($apiKey);

// Get users with unit and position
$users = $api->getUsers([
    'unit_id' => 1,
    'with' => 'unit,position.echelon,rank',
    'per_page' => 20
]);

// Get single user
$user = $api->getUser(1, ['unit', 'position', 'rank']);

// Get units
$units = $api->getUnits(['with' => 'parent,children']);
```

---

### JavaScript (Axios)
```javascript
class PdSystemApiClient {
    constructor(apiKey) {
        this.apiKey = apiKey;
        this.baseURL = 'https://pdsystem.trust-idn.id/api';
        this.client = axios.create({
            baseURL: this.baseURL,
            headers: {
                'X-API-Key': this.apiKey,
                'Accept': 'application/json',
            },
        });
    }

    /**
     * Get all users
     */
    async getUsers(filters = {}) {
        try {
            const response = await this.client.get('/users', {
                params: filters
            });
            return response.data;
        } catch (error) {
            console.error('Error fetching users:', error);
            throw error;
        }
    }

    /**
     * Get single user by ID
     */
    async getUser(id, withRelations = []) {
        try {
            const params = {};
            if (withRelations.length > 0) {
                params.with = withRelations.join(',');
            }
            
            const response = await this.client.get(`/users/${id}`, {
                params
            });
            return response.data;
        } catch (error) {
            console.error('Error fetching user:', error);
            throw error;
        }
    }

    /**
     * Get all units
     */
    async getUnits(filters = {}) {
        try {
            const response = await this.client.get('/units', {
                params: filters
            });
            return response.data;
        } catch (error) {
            console.error('Error fetching units:', error);
            throw error;
        }
    }
}

// Usage
const apiKey = 'your_api_key_here'; // Get from administrator
const api = new PdSystemApiClient(apiKey);

// Get users
api.getUsers({
    unit_id: 1,
    with: 'unit,position,rank',
    per_page: 20
}).then(data => {
    console.log('Users:', data);
});

// Get single user
api.getUser(1, ['unit', 'position', 'rank']).then(user => {
    console.log('User:', user);
});
```

---

### Python (Requests)
```python
import requests
from typing import Optional, Dict, List

class PdSystemApiClient:
    def __init__(self, api_key: str):
        self.api_key = api_key
        self.base_url = 'https://pdsystem.trust-idn.id/api'
        self.headers = {
            'X-API-Key': self.api_key,
            'Accept': 'application/json',
        }

    def _get(self, endpoint: str, params: Optional[Dict] = None):
        """Internal GET request method"""
        url = f"{self.base_url}/{endpoint}"
        response = requests.get(url, headers=self.headers, params=params)
        response.raise_for_status()
        return response.json()

    def get_users(self, filters: Optional[Dict] = None):
        """Get all users"""
        return self._get('users', params=filters)

    def get_user(self, user_id: int, with_relations: Optional[List[str]] = None):
        """Get single user by ID"""
        params = {}
        if with_relations:
            params['with'] = ','.join(with_relations)
        return self._get(f'users/{user_id}', params=params)

    def get_units(self, filters: Optional[Dict] = None):
        """Get all units"""
        return self._get('units', params=filters)

    def get_positions(self, filters: Optional[Dict] = None):
        """Get all positions"""
        return self._get('positions', params=filters)

# Usage
api_key = 'your_api_key_here'  # Get from administrator
api = PdSystemApiClient(api_key)

# Get users
users = api.get_users({
    'unit_id': 1,
    'with': 'unit,position,rank',
    'per_page': 20
})

# Get single user
user = api.get_user(1, ['unit', 'position', 'rank'])

# Get units
units = api.get_units({'with': 'parent,children'})
```

---

## Error Handling

### Error Response Format
```json
{
  "success": false,
  "message": "Error message here"
}
```

### HTTP Status Codes
- `200` - Success
- `401` - Unauthorized (Invalid or missing API key)
- `403` - Forbidden (API key inactive or IP not whitelisted)
- `404` - Resource not found
- `422` - Validation error
- `500` - Server error

### Contoh Error Handling
```php
try {
    $users = $api->getUsers(['unit_id' => 1]);
} catch (\GuzzleHttp\Exception\ClientException $e) {
    $response = $e->getResponse();
    $statusCode = $response->getStatusCode();
    $body = json_decode($response->getBody(), true);
    
    if ($statusCode === 401) {
        // Invalid API key
        echo "Error: " . $body['message'];
    } elseif ($statusCode === 403) {
        // API key inactive or IP not whitelisted
        echo "Error: " . $body['message'];
    }
}
```

---

## Best Practices

### 1. Simpan API Key dengan Aman
- Gunakan environment variables
- Jangan commit API key ke version control
- Simpan di secure vault atau config file yang di-ignore

### 2. Implementasi Caching
- Cache data master yang jarang berubah (Unit, Position, Rank, dll)
- Set TTL yang sesuai (misalnya 1 jam untuk master data)
- Invalidate cache saat diperlukan

### 3. Error Handling
- Implementasikan retry logic dengan exponential backoff
- Log semua error untuk debugging
- Handle rate limiting jika diterapkan

### 4. Pagination
- Selalu gunakan pagination untuk list endpoints
- Implementasikan infinite scroll atau "Load More" untuk UX yang baik
- Default per_page adalah 15, maksimal 100

### 5. Eager Loading
- Gunakan parameter `with` untuk memuat relasi yang diperlukan
- Hindari N+1 query problem
- Contoh: `with=unit,position.echelon,rank`

---

## Contoh Use Cases

### 1. Sinkronisasi Data Pegawai
```php
// Sync users from PD System to local database
$api = new PdSystemApiClient($apiKey);
$page = 1;

do {
    $response = $api->getUsers([
        'per_page' => 100,
        'page' => $page,
        'with' => 'unit,position,rank'
    ]);
    
    foreach ($response['data'] as $user) {
        // Sync to local database
        LocalUser::updateOrCreate(
            ['nip' => $user['nip']],
            [
                'name' => $user['name'],
                'email' => $user['email'],
                'unit_id' => $user['unit']['id'] ?? null,
                'position_id' => $user['position']['id'] ?? null,
                // ... other fields
            ]
        );
    }
    
    $page++;
} while ($response['meta']['current_page'] < $response['meta']['last_page']);
```

### 2. Dropdown Unit Kerja
```javascript
// Populate unit dropdown
const api = new PdSystemApiClient(apiKey);

api.getUnits({ with: 'parent' }).then(response => {
    const units = response.data;
    const select = document.getElementById('unit-select');
    
    units.forEach(unit => {
        const option = document.createElement('option');
        option.value = unit.id;
        option.textContent = unit.full_name;
        select.appendChild(option);
    });
});
```

### 3. Validasi NIP
```python
# Validate NIP exists in PD System
api = PdSystemApiClient(api_key)

def validate_nip(nip: str) -> bool:
    try:
        response = api.get_users({'search': nip, 'per_page': 1})
        return len(response['data']) > 0
    except:
        return False

# Usage
if validate_nip('198001012010011001'):
    print('NIP valid')
else:
    print('NIP tidak ditemukan')
```

---

## Dokumentasi Lengkap

Untuk dokumentasi lengkap, lihat:
- **API Reference:** `API_REFERENCE_DOCUMENTATION.md`
- **Security:** `API_SECURITY_DOCUMENTATION.md`
- **User Model:** `USER_API_DOCUMENTATION.md`

---

## Support

Jika mengalami masalah:
1. Periksa API key masih aktif
2. Periksa IP address terdaftar (jika menggunakan IP whitelist)
3. Periksa format request sesuai dokumentasi
4. Hubungi administrator PD System untuk bantuan

---

## Catatan Penting

1. **API Key:** Dapatkan dari administrator PD System
2. **Base URL:** `https://pdsystem.trust-idn.id/api`
3. **Authentication:** Wajib menggunakan API key di setiap request
4. **Rate Limiting:** Saat ini tidak ada, tapi pertimbangkan untuk implementasi retry logic
5. **HTTPS:** Semua request harus melalui HTTPS
6. **Read-Only:** API ini hanya untuk membaca data (GET requests)

---

## Quick Start Checklist

- [ ] Dapatkan API key dari administrator
- [ ] Simpan API key di environment variable
- [ ] Install HTTP client library (Guzzle, Axios, Requests, dll)
- [ ] Implementasi API client class
- [ ] Test koneksi dengan endpoint sederhana
- [ ] Implementasi error handling
- [ ] Implementasi caching (opsional)
- [ ] Test dengan data real

---

**Selamat mengintegrasikan!** 🚀
