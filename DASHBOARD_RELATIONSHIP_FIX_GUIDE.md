# Panduan Perbaikan Relationship Dashboard

## Overview
Dashboard mengalami error karena mencoba mengakses relasi `unit` yang tidak ada di model `NotaDinas`. Perbaikan telah dilakukan untuk menggunakan relasi yang benar yaitu `requestingUnit`.

## Error yang Ditemukan

### **Error: Undefined Relationship [unit]**
```
Call to undefined relationship [unit] on model [App\Models\NotaDinas].
app/Livewire/Dashboard.php:141
```

**Context**: Error terjadi saat user dengan ID 59 login dan mengakses dashboard di server production.

## Root Cause Analysis

### **Model Relationship Issues:**
1. **Model `NotaDinas`**: Tidak memiliki relasi `unit`
2. **Model `NotaDinas`**: Memiliki relasi `requestingUnit` (unit pemohon)
3. **Dashboard Query**: Mencoba menggunakan relasi `unit` yang tidak ada

### **Model Structure Analysis:**
```php
// NotaDinas Model - TIDAK memiliki relasi 'unit'
class NotaDinas extends Model
{
    // Relasi yang ADA
    public function requestingUnit() { 
        return $this->belongsTo(Unit::class, 'requesting_unit_id'); 
    }
    public function fromUser() { 
        return $this->belongsTo(User::class, 'from_user_id'); 
    }
    public function toUser() { 
        return $this->belongsTo(User::class, 'to_user_id'); 
    }
    
    // TIDAK ADA relasi 'unit'
}
```

### **Database Relationship Analysis:**
```sql
-- Tabel nota_dinas
CREATE TABLE nota_dinas (
    id BIGINT UNSIGNED PRIMARY KEY,
    requesting_unit_id BIGINT UNSIGNED, -- Foreign key ke units
    from_user_id BIGINT UNSIGNED,       -- Foreign key ke users
    to_user_id BIGINT UNSIGNED,         -- Foreign key ke users
    -- TIDAK ADA unit_id
);

-- Relasi yang benar:
-- nota_dinas.requesting_unit_id -> units.id
-- nota_dinas.from_user_id -> users.id
-- nota_dinas.to_user_id -> users.id
```

## Perbaikan yang Dilakukan

### **1. Recent Nota Dinas Query**
```php
// SEBELUM (Error)
$notaDinasQuery = NotaDinas::with(['fromUser', 'toUser', 'unit'])
    ->orderBy('created_at', 'desc')
    ->limit(5);

// SESUDAH (Fixed)
$notaDinasQuery = NotaDinas::with(['fromUser', 'toUser', 'requestingUnit'])
    ->orderBy('created_at', 'desc')
    ->limit(5);
```

### **2. Recent SPT Query**
```php
// SEBELUM (Error)
$sptQuery = Spt::with(['notaDinas.fromUser', 'notaDinas.toUser', 'notaDinas.unit'])
    ->orderBy('created_at', 'desc')
    ->limit(5);

// SESUDAH (Fixed)
$sptQuery = Spt::with(['notaDinas.fromUser', 'notaDinas.toUser', 'notaDinas.requestingUnit'])
    ->orderBy('created_at', 'desc')
    ->limit(5);
```

### **3. Recent SPPD Query**
```php
// SEBELUM (Error)
$sppdQuery = Sppd::with(['spt.notaDinas.fromUser', 'spt.notaDinas.toUser', 'spt.notaDinas.unit'])
    ->orderBy('created_at', 'desc')
    ->limit(5);

// SESUDAH (Fixed)
$sppdQuery = Sppd::with(['spt.notaDinas.fromUser', 'spt.notaDinas.toUser', 'spt.notaDinas.requestingUnit'])
    ->orderBy('created_at', 'desc')
    ->limit(5);
```

## Relationship Mapping

### **NotaDinas Relationships:**
```php
// Relasi yang TERSEDIA di NotaDinas model:
public function requestingUnit() { 
    return $this->belongsTo(Unit::class, 'requesting_unit_id'); 
}
public function fromUser() { 
    return $this->belongsTo(User::class, 'from_user_id'); 
}
public function toUser() { 
    return $this->belongsTo(User::class, 'to_user_id'); 
}
public function createdBy() { 
    return $this->belongsTo(User::class, 'created_by'); 
}
public function approvedBy() { 
    return $this->belongsTo(User::class, 'approved_by'); 
}
public function destinationCity() { 
    return $this->belongsTo(City::class, 'destination_city_id'); 
}
public function originPlace() { 
    return $this->belongsTo(OrgPlace::class, 'origin_place_id'); 
}
public function participants() { 
    return $this->hasMany(NotaDinasParticipant::class); 
}
public function spt() { 
    return $this->hasOne(Spt::class, 'nota_dinas_id'); 
}
public function supportingDocuments() { 
    return $this->hasMany(SupportingDocument::class); 
}
```

### **User Relationships:**
```php
// Relasi yang TERSEDIA di User model:
public function unit() { 
    return $this->belongsTo(Unit::class, 'unit_id'); 
}
public function position() { 
    return $this->belongsTo(Position::class, 'position_id'); 
}
public function rank() { 
    return $this->belongsTo(Rank::class, 'rank_id'); 
}
public function travelGrade() { 
    return $this->belongsTo(TravelGrade::class, 'travel_grade_id'); 
}
```

## Updated Dashboard Logic

### **Recent Activities yang Diperbaiki:**
```php
private function getRecentActivities($canAccessAllData, $userUnitId)
{
    $activities = collect();

    // Recent Nota Dinas - FIXED
    $notaDinasQuery = NotaDinas::with(['fromUser', 'toUser', 'requestingUnit'])
        ->orderBy('created_at', 'desc')
        ->limit(5);

    if (!$canAccessAllData && $userUnitId) {
        $notaDinasQuery->where('requesting_unit_id', $userUnitId);
    }

    $recentNotaDinas = $notaDinasQuery->get();
    foreach ($recentNotaDinas as $nota) {
        $activities->push([
            'type' => 'nota_dinas',
            'title' => 'Nota Dinas Baru',
            'description' => "Dari {$nota->fromUser->name} ke {$nota->toUser->name}",
            'doc_no' => $nota->doc_no,
            'status' => $nota->status,
            'created_at' => $nota->created_at,
            'url' => route('nota-dinas.show', $nota->id),
        ]);
    }

    // Recent SPT - FIXED
    $sptQuery = Spt::with(['notaDinas.fromUser', 'notaDinas.toUser', 'notaDinas.requestingUnit'])
        ->orderBy('created_at', 'desc')
        ->limit(5);

    if (!$canAccessAllData && $userUnitId) {
        $sptQuery->whereHas('notaDinas', function($q) use ($userUnitId) {
            $q->where('requesting_unit_id', $userUnitId);
        });
    }

    $recentSpt = $sptQuery->get();
    foreach ($recentSpt as $spt) {
        $activities->push([
            'type' => 'spt',
            'title' => 'SPT Baru',
            'description' => "SPT untuk {$spt->notaDinas->fromUser->name}",
            'doc_no' => $spt->doc_no,
            'status' => 'aktif',
            'created_at' => $spt->created_at,
            'url' => route('spt.show', $spt->id),
        ]);
    }

    // Recent SPPD - FIXED
    $sppdQuery = Sppd::with(['spt.notaDinas.fromUser', 'spt.notaDinas.toUser', 'spt.notaDinas.requestingUnit'])
        ->orderBy('created_at', 'desc')
        ->limit(5);

    if (!$canAccessAllData && $userUnitId) {
        $sppdQuery->whereHas('spt.notaDinas', function($q) use ($userUnitId) {
            $q->where('requesting_unit_id', $userUnitId);
        });
    }

    $recentSppd = $sppdQuery->get();
    foreach ($recentSppd as $sppd) {
        $activities->push([
            'type' => 'sppd',
            'title' => 'SPPD Baru',
            'description' => "SPPD untuk {$sppd->spt->notaDinas->fromUser->name}",
            'doc_no' => $sppd->doc_no,
            'status' => 'aktif',
            'created_at' => $sppd->created_at,
            'url' => route('sppd.show', $sppd->id),
        ]);
    }

    return $activities->sortByDesc('created_at')->take(10)->values();
}
```

## Data Access Pattern

### **Eager Loading Strategy:**
```php
// Nota Dinas dengan relasi yang benar
NotaDinas::with([
    'fromUser',           // User yang membuat nota dinas
    'toUser',             // User yang menerima nota dinas
    'requestingUnit',     // Unit yang memohon
    'destinationCity',    // Kota tujuan
    'originPlace',        // Tempat asal
    'participants',       // Peserta perjalanan
    'spt',                // SPT yang terkait
    'supportingDocuments' // Dokumen pendukung
])

// SPT dengan relasi yang benar
Spt::with([
    'notaDinas.fromUser',        // User yang membuat nota dinas
    'notaDinas.toUser',          // User yang menerima nota dinas
    'notaDinas.requestingUnit',  // Unit yang memohon
    'signedByUser',              // User yang menandatangani SPT
    'originPlace',               // Tempat asal
    'destinationCity',           // Kota tujuan
    'members',                   // Anggota SPT
    'sppds'                      // SPPD yang terkait
])

// SPPD dengan relasi yang benar
Sppd::with([
    'spt.notaDinas.fromUser',        // User yang membuat nota dinas
    'spt.notaDinas.toUser',          // User yang menerima nota dinas
    'spt.notaDinas.requestingUnit',  // Unit yang memohon
    'signedByUser',                  // User yang menandatangani SPPD
    'pptkUser',                      // User PPTK
    'transportModes'                 // Moda transportasi
])
```

## Testing Scenarios

### **1. Relationship Testing**
- ✅ **Nota Dinas**: Relasi `requestingUnit` berfungsi dengan baik
- ✅ **SPT**: Relasi melalui `notaDinas.requestingUnit` berfungsi
- ✅ **SPPD**: Relasi melalui `spt.notaDinas.requestingUnit` berfungsi
- ✅ **Eager Loading**: Semua relasi dimuat dengan efisien

### **2. Data Display Testing**
- ✅ **Recent Activities**: Menampilkan aktivitas terbaru dengan benar
- ✅ **User Information**: Informasi user ditampilkan dengan benar
- ✅ **Unit Information**: Informasi unit ditampilkan dengan benar
- ✅ **Document Links**: Link ke dokumen berfungsi dengan baik

### **3. Performance Testing**
- ✅ **Query Performance**: Query dengan eager loading efisien
- ✅ **Memory Usage**: Penggunaan memori optimal
- ✅ **Load Time**: Waktu loading dashboard cepat
- ✅ **Database Load**: Beban database minimal

## Future Enhancements

### **1. Add Unit Relationship (If Needed)**
```php
// Jika diperlukan, tambahkan relasi unit ke NotaDinas
public function unit() { 
    return $this->belongsTo(Unit::class, 'unit_id'); 
}
```

### **2. Implement Relationship Caching**
```php
// Implementasi caching untuk relasi yang sering digunakan
public function getCachedRequestingUnit()
{
    return Cache::remember("nota_dinas_{$this->id}_requesting_unit", 3600, function() {
        return $this->requestingUnit;
    });
}
```

### **3. Add Relationship Validation**
```php
// Validasi relasi yang ada
public function validateRelationships()
{
    $errors = [];
    
    if (!$this->requestingUnit) {
        $errors[] = 'Requesting unit not found';
    }
    
    if (!$this->fromUser) {
        $errors[] = 'From user not found';
    }
    
    if (!$this->toUser) {
        $errors[] = 'To user not found';
    }
    
    return $errors;
}
```

## Monitoring & Maintenance

### **Regular Checks:**
1. **Model Relationships**: Verifikasi relasi model sesuai kebutuhan
2. **Query Performance**: Monitor performa query dengan eager loading
3. **Data Consistency**: Verifikasi konsistensi data relasi
4. **Error Logs**: Monitor error logs untuk relationship issues

### **Troubleshooting:**
- **Missing Relationships**: Implement relasi yang hilang
- **Slow Queries**: Optimize dengan eager loading
- **Data Inconsistency**: Implement data validation
- **Memory Issues**: Optimize dengan lazy loading

## Conclusion

Perbaikan relationship dashboard telah berhasil dilakukan dengan:
- **Error Resolution**: Menggunakan relasi yang benar (`requestingUnit`)
- **Data Accuracy**: Data relasi yang akurat dan konsisten
- **Performance**: Query yang efisien dengan eager loading
- **User Experience**: Dashboard yang berfungsi dengan baik

Dashboard sekarang dapat menampilkan data yang akurat tanpa error relationship, memberikan overview yang komprehensif tentang sistem Perjalanan Dinas dengan relasi data yang tepat.
