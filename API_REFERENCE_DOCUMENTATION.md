# Dokumentasi API Referensi

API ini menyediakan akses read-only ke data referensi yang dapat digunakan oleh aplikasi lain.

## Base URL
```
/api
```

**Catatan:** Semua endpoint memerlukan API key authentication. Lihat [API_SECURITY_DOCUMENTATION.md](API_SECURITY_DOCUMENTATION.md) untuk detail.

---

## Endpoint yang Tersedia

### 1. User API

#### List Users
```
GET /api/users
```

**Query Parameters:**
- `unit_id` (integer) - Filter by unit ID
- `instansi_id` (integer) - Filter by instansi ID
- `position_id` (integer) - Filter by position ID
- `rank_id` (integer) - Filter by rank ID
- `travel_grade_id` (integer) - Filter by travel grade ID
- `employee_type` (string) - Filter by employee type (PNS, PPPK, PPPK PW, Non ASN)
- `search` (string) - Search by name, NIP, or email
- `with` (string) - Comma-separated list of relationships to include
- `per_page` (integer) - Number of items per page (default: 15, max: 100)
- `page` (integer) - Page number

**Example:**
```bash
curl -X GET "https://your-domain.com/api/users?unit_id=1&search=john&per_page=20" \
  -H "X-API-Key: your_api_key_here"
```

**Response:**
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
      ...
    }
  ],
  "links": {
    "first": "http://example.com/api/users?page=1",
    "last": "http://example.com/api/users?page=10",
    "prev": null,
    "next": "http://example.com/api/users?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "per_page": 15,
    "to": 15,
    "total": 150
  }
}
```

#### Get Single User
```
GET /api/users/{id}
```

**Query Parameters:**
- `with` (string) - Comma-separated list of relationships to include

**Example:**
```
GET /api/users/1?with=unit,position.echelon,rank
```

---

### 2. Unit API

#### List Units
```
GET /api/units
```

**Query Parameters:**
- `parent_id` (integer|null) - Filter by parent ID (use 'null' for root units)
- `search` (string) - Search by code or name
- `with` (string) - Comma-separated list of relationships (parent, children)
- `per_page` (integer) - Number of items per page
- `page` (integer) - Page number

**Example:**
```
GET /api/units?parent_id=null&with=parent,children
```

#### Get Single Unit
```
GET /api/units/{id}
```

---

### 3. Instansi API

#### List Instansis
```
GET /api/instansis
```

**Query Parameters:**
- `search` (string) - Search by code or name
- `per_page` (integer) - Number of items per page
- `page` (integer) - Page number

#### Get Single Instansi
```
GET /api/instansis/{id}
```

---

### 4. Position API

#### List Positions
```
GET /api/positions
```

**Query Parameters:**
- `echelon_id` (integer) - Filter by echelon ID
- `type` (string) - Filter by position type
- `search` (string) - Search by name
- `with` (string) - Comma-separated list of relationships (echelon)
- `per_page` (integer) - Number of items per page
- `page` (integer) - Page number

**Example:**
```
GET /api/positions?echelon_id=1&with=echelon
```

#### Get Single Position
```
GET /api/positions/{id}
```

---

### 5. Rank API

#### List Ranks
```
GET /api/ranks
```

**Query Parameters:**
- `search` (string) - Search by code or name
- `per_page` (integer) - Number of items per page
- `page` (integer) - Page number

#### Get Single Rank
```
GET /api/ranks/{id}
```

---

### 6. Travel Grade API

#### List Travel Grades
```
GET /api/travel-grades
```

**Query Parameters:**
- `search` (string) - Search by code or name
- `per_page` (integer) - Number of items per page
- `page` (integer) - Page number

#### Get Single Travel Grade
```
GET /api/travel-grades/{id}
```

---

### 7. Echelon API

#### List Echelons
```
GET /api/echelons
```

**Query Parameters:**
- `search` (string) - Search by code or name
- `per_page` (integer) - Number of items per page
- `page` (integer) - Page number

#### Get Single Echelon
```
GET /api/echelons/{id}
```

---

### 8. SPT API

#### List SPTs
```
GET /api/spts
```

**Query Parameters:**
- `signed_by_user_id` (integer) - Filter by signed by user ID
- `nota_dinas_id` (integer) - Filter by nota dinas ID
- `status` (string) - Filter by status
- `search` (string) - Search by doc_no or assignment_title
- `with` (string) - Comma-separated list of relationships (signedByUser, members.user)
- `per_page` (integer) - Number of items per page
- `page` (integer) - Page number

**Example:**
```
GET /api/spts?signed_by_user_id=1&with=signedByUser,members.user
```

#### Get Single SPT
```
GET /api/spts/{id}
```

---

### 9. SPT Member API

#### List SPT Members
```
GET /api/spt-members
```

**Query Parameters:**
- `spt_id` (integer) - Filter by SPT ID
- `user_id` (integer) - Filter by user ID
- `with` (string) - Comma-separated list of relationships (user, spt)
- `per_page` (integer) - Number of items per page
- `page` (integer) - Page number

**Example:**
```
GET /api/spt-members?spt_id=1&with=user
```

#### Get Single SPT Member
```
GET /api/spt-members/{id}
```

---

## Response Format

### Success Response
Semua endpoint mengembalikan response dalam format JSON dengan struktur:

```json
{
  "data": [...],
  "links": {...},
  "meta": {...}
}
```

Untuk single resource (show endpoint), response langsung berupa object:

```json
{
  "id": 1,
  "name": "...",
  ...
}
```

### Error Response
```json
{
  "message": "Error message",
  "errors": {
    "field": ["Error detail"]
  }
}
```

**HTTP Status Codes:**
- `200` - Success
- `404` - Resource not found
- `422` - Validation error
- `500` - Server error

---

## Contoh Penggunaan

### 1. Mendapatkan semua user dalam unit tertentu
```bash
curl -X GET "http://your-domain.com/api/users?unit_id=1&with=unit,position,rank"
```

### 2. Mencari user berdasarkan NIP
```bash
curl -X GET "http://your-domain.com/api/users?search=198001012010011001"
```

### 3. Mendapatkan semua unit dengan hierarki
```bash
curl -X GET "http://your-domain.com/api/units?with=parent,children"
```

### 4. Mendapatkan semua jabatan dengan eselon
```bash
curl -X GET "http://your-domain.com/api/positions?with=echelon"
```

### 5. Mendapatkan SPT dengan anggota
```bash
curl -X GET "http://your-domain.com/api/spts/1?with=signedByUser,members.user"
```

---

## Pagination

Semua list endpoint menggunakan pagination dengan default 15 items per page. Maksimal 100 items per page.

**Pagination Response:**
```json
{
  "data": [...],
  "links": {
    "first": "http://example.com/api/users?page=1",
    "last": "http://example.com/api/users?page=10",
    "prev": null,
    "next": "http://example.com/api/users?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "per_page": 15,
    "to": 15,
    "total": 150
  }
}
```

---

## Eager Loading Relationships

Gunakan parameter `with` untuk memuat relasi yang diperlukan:

**Contoh:**
```
GET /api/users/1?with=unit,position.echelon,rank,travelGrade
```

**Relasi yang tersedia:**

**User:**
- `unit`
- `instansi`
- `position`
- `position.echelon`
- `rank`
- `travelGrade`

**Unit:**
- `parent`
- `children`

**Position:**
- `echelon`

**SPT:**
- `signedByUser`
- `members`
- `members.user`

**SPT Member:**
- `user`
- `spt`

---

## Filtering & Searching

### Filtering
Gunakan query parameter untuk filter data:
- Filter by ID: `?unit_id=1`
- Filter by status: `?status=active`
- Filter by type: `?employee_type=PNS`

### Searching
Gunakan parameter `search` untuk pencarian:
- `?search=keyword` - Mencari di field yang relevan (name, code, dll)

---

## Catatan Penting

1. **Read-Only**: API ini hanya untuk membaca data (GET requests)
2. **API Key Authentication**: Semua endpoint memerlukan API key. Lihat [API_SECURITY_DOCUMENTATION.md](API_SECURITY_DOCUMENTATION.md)
3. **Pagination**: Semua list endpoint menggunakan pagination
4. **Soft Delete**: Data yang di-soft delete tidak ditampilkan secara default
5. **Performance**: Gunakan parameter `with` dengan bijak untuk menghindari N+1 query problem

---

## Rate Limiting

Saat ini tidak ada rate limiting yang diterapkan. Jika diperlukan, dapat ditambahkan di middleware.

---

## Support

Untuk pertanyaan atau masalah terkait API, silakan hubungi tim development.
