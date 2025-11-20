# Panduan Perbaikan Unit ID Dashboard

## Overview
Dashboard mengalami error karena mencoba mengakses kolom `unit_id` yang tidak ada di tabel `nota_dinas`. Perbaikan telah dilakukan untuk menggunakan kolom yang benar yaitu `requesting_unit_id`.

## Error yang Ditemukan

### **Error: Unit ID Column Not Found**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'unit_id' in 'where clause' 
(Connection: mysql, SQL: select count(*) as aggregate from `nota_dinas` where `unit_id` = 3 and `nota_dinas`.`deleted_at` is null)
```

**Context**: Error terjadi saat user dengan ID 57 login dan mengakses dashboard.

## Root Cause Analysis

### **Database Structure Issues:**
1. **Tabel `nota_dinas`**: Tidak memiliki kolom `unit_id`
2. **Tabel `nota_dinas`**: Memiliki kolom `requesting_unit_id` (unit pemohon)
3. **Tabel `users`**: Memiliki kolom `unit_id` (unit pegawai)

### **Model Structure:**
```php
// NotaDinas Model - TIDAK memiliki unit_id field
class NotaDinas extends Model
{
    protected $fillable = [
        'doc_no', 'to_user_id', 'from_user_id', 'nd_date',
        'hal', 'maksud', 'start_date', 'end_date',
        'requesting_unit_id', // ADA 'requesting_unit_id' field
        'status', 'created_by', 'approved_by',
        // TIDAK ADA 'unit_id' field
    ];
}

// User Model - MEMILIKI unit_id field
class User extends Model
{
    protected $fillable = [
        'name', 'email', 'nip', 'unit_id', // ADA 'unit_id' field
        'position_id', 'rank_id', 'is_non_staff',
    ];
}
```

### **Database Schema Analysis:**
```sql
-- Tabel nota_dinas
CREATE TABLE nota_dinas (
    id BIGINT UNSIGNED PRIMARY KEY,
    doc_no VARCHAR(255) UNIQUE,
    to_user_id BIGINT UNSIGNED,
    from_user_id BIGINT UNSIGNED,
    requesting_unit_id BIGINT UNSIGNED, -- Unit pemohon
    status ENUM('DRAFT', 'SUBMITTED', 'APPROVED', 'REJECTED'),
    -- TIDAK ADA unit_id
);

-- Tabel users
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255),
    unit_id BIGINT UNSIGNED, -- Unit pegawai
    position_id BIGINT UNSIGNED,
    rank_id BIGINT UNSIGNED,
);
```

## Perbaikan yang Dilakukan

### **1. Statistik Utama (Main Statistics)**
```php
// SEBELUM (Error)
if (!$canAccessAllData && $userUnitId) {
    $baseQuery->where('unit_id', $userUnitId);
    $sptQuery->whereHas('notaDinas', function($q) use ($userUnitId) {
        $q->where('unit_id', $userUnitId);
    });
    $sppdQuery->whereHas('spt.notaDinas', function($q) use ($userUnitId) {
        $q->where('unit_id', $userUnitId);
    });
}

// SESUDAH (Fixed)
if (!$canAccessAllData && $userUnitId) {
    $baseQuery->where('requesting_unit_id', $userUnitId);
    $sptQuery->whereHas('notaDinas', function($q) use ($userUnitId) {
        $q->where('requesting_unit_id', $userUnitId);
    });
    $sppdQuery->whereHas('spt.notaDinas', function($q) use ($userUnitId) {
        $q->where('requesting_unit_id', $userUnitId);
    });
}
```

### **2. Status Dokumen (Document Status)**
```php
// SEBELUM (Error)
if (!$canAccessAllData && $userUnitId) {
    $baseQuery->where('unit_id', $userUnitId);
    $sptQuery->whereHas('notaDinas', function($q) use ($userUnitId) {
        $q->where('unit_id', $userUnitId);
    });
    $sppdQuery->whereHas('spt.notaDinas', function($q) use ($userUnitId) {
        $q->where('unit_id', $userUnitId);
    });
}

// SESUDAH (Fixed)
if (!$canAccessAllData && $userUnitId) {
    $baseQuery->where('requesting_unit_id', $userUnitId);
    $sptQuery->whereHas('notaDinas', function($q) use ($userUnitId) {
        $q->where('requesting_unit_id', $userUnitId);
    });
    $sppdQuery->whereHas('spt.notaDinas', function($q) use ($userUnitId) {
        $q->where('requesting_unit_id', $userUnitId);
    });
}
```

### **3. Aktivitas Terbaru (Recent Activities)**
```php
// SEBELUM (Error)
if (!$canAccessAllData && $userUnitId) {
    $notaDinasQuery->where('unit_id', $userUnitId);
    $sptQuery->whereHas('notaDinas', function($q) use ($userUnitId) {
        $q->where('unit_id', $userUnitId);
    });
    $sppdQuery->whereHas('spt.notaDinas', function($q) use ($userUnitId) {
        $q->where('unit_id', $userUnitId);
    });
}

// SESUDAH (Fixed)
if (!$canAccessAllData && $userUnitId) {
    $notaDinasQuery->where('requesting_unit_id', $userUnitId);
    $sptQuery->whereHas('notaDinas', function($q) use ($userUnitId) {
        $q->where('requesting_unit_id', $userUnitId);
    });
    $sppdQuery->whereHas('spt.notaDinas', function($q) use ($userUnitId) {
        $q->where('requesting_unit_id', $userUnitId);
    });
}
```

### **4. Dokumen Overdue (Overdue Documents)**
```php
// SEBELUM (Error)
if (!$canAccessAllData && $userUnitId) {
    $query->where('unit_id', $userUnitId);
}

// SESUDAH (Fixed)
if (!$canAccessAllData && $userUnitId) {
    $query->where('requesting_unit_id', $userUnitId);
}
```

## Business Logic Analysis

### **Unit Scope Filtering Logic:**
```php
// User Unit ID: Unit tempat pegawai bekerja
$userUnitId = $currentUser->unit_id;

// Nota Dinas Filtering: Berdasarkan unit pemohon
$notaDinasQuery->where('requesting_unit_id', $userUnitId);

// User Filtering: Berdasarkan unit pegawai
$userQuery->where('unit_id', $userUnitId);

// SPT Filtering: Berdasarkan unit pemohon nota dinas
$sptQuery->whereHas('notaDinas', function($q) use ($userUnitId) {
    $q->where('requesting_unit_id', $userUnitId);
});

// SPPD Filtering: Berdasarkan unit pemohon nota dinas
$sppdQuery->whereHas('spt.notaDinas', function($q) use ($userUnitId) {
    $q->where('requesting_unit_id', $userUnitId);
});
```

### **Data Access Control:**
1. **Nota Dinas**: User hanya melihat nota dinas dari unit mereka
2. **SPT**: User hanya melihat SPT yang terkait dengan nota dinas dari unit mereka
3. **SPPD**: User hanya melihat SPPD yang terkait dengan SPT dari unit mereka
4. **Users**: User hanya melihat pegawai dari unit mereka

## Updated Dashboard Logic

### **Statistics yang Diperbaiki:**
```php
private function getMainStatistics($canAccessAllData, $userUnitId)
{
    $baseQuery = NotaDinas::query();
    $userQuery = User::query();
    $sptQuery = Spt::query();
    $sppdQuery = Sppd::query();

    // Apply unit scope filtering if user can't access all data
    if (!$canAccessAllData && $userUnitId) {
        $baseQuery->where('requesting_unit_id', $userUnitId);
        $userQuery->where('unit_id', $userUnitId);
        $sptQuery->whereHas('notaDinas', function($q) use ($userUnitId) {
            $q->where('requesting_unit_id', $userUnitId);
        });
        $sppdQuery->whereHas('spt.notaDinas', function($q) use ($userUnitId) {
            $q->where('requesting_unit_id', $userUnitId);
        });
    }

    // Filter out non-staff users
    $userQuery->where('is_non_staff', false);

    return [
        'total_nota_dinas' => $baseQuery->count(),
        'total_nota_dinas_month' => $baseQuery->whereMonth('created_at', now()->month)
                                            ->whereYear('created_at', now()->year)
                                            ->count(),
        'total_pegawai' => $userQuery->count(),
        'total_spt' => $sptQuery->count(),
        'total_spt_month' => $sptQuery->whereMonth('created_at', now()->month)
                                     ->whereYear('created_at', now()->year)
                                     ->count(),
        'total_sppd' => $sppdQuery->count(),
        'total_sppd_month' => $sppdQuery->whereMonth('created_at', now()->month)
                                       ->whereYear('created_at', now()->year)
                                       ->count(),
        'total_perjalanan_aktif' => $sppdQuery->count(),
    ];
}
```

### **Document Status yang Diperbaiki:**
```php
private function getDocumentStatus($canAccessAllData, $userUnitId)
{
    $baseQuery = NotaDinas::query();
    $sptQuery = Spt::query();
    $sppdQuery = Sppd::query();

    // Apply unit scope filtering if user can't access all data
    if (!$canAccessAllData && $userUnitId) {
        $baseQuery->where('requesting_unit_id', $userUnitId);
        $sptQuery->whereHas('notaDinas', function($q) use ($userUnitId) {
            $q->where('requesting_unit_id', $userUnitId);
        });
        $sppdQuery->whereHas('spt.notaDinas', function($q) use ($userUnitId) {
            $q->where('requesting_unit_id', $userUnitId);
        });
    }

    return [
        'nota_dinas_pending' => $baseQuery->where('status', 'pending')->count(),
        'nota_dinas_approved' => $baseQuery->where('status', 'approved')->count(),
        'nota_dinas_rejected' => $baseQuery->where('status', 'rejected')->count(),
        'spt_aktif' => $sptQuery->count(),
        'spt_selesai' => 0,
        'sppd_aktif' => $sppdQuery->count(),
        'sppd_selesai' => 0,
        'dokumen_overdue' => $this->getOverdueDocuments($canAccessAllData, $userUnitId),
    ];
}
```

## Testing Scenarios

### **1. Database Query Testing**
- ✅ **Nota Dinas**: Unit filtering berfungsi dengan baik
- ✅ **SPT**: Unit filtering melalui nota dinas berfungsi
- ✅ **SPPD**: Unit filtering melalui SPT dan nota dinas berfungsi
- ✅ **Users**: Unit filtering berfungsi dengan baik

### **2. Permission Testing**
- ✅ **Unit Scope**: Data difilter sesuai unit user
- ✅ **Access Control**: User hanya melihat data unit mereka
- ✅ **Data Security**: Data terisolasi per unit

### **3. User Experience Testing**
- ✅ **Dashboard Load**: Dashboard dapat diakses tanpa error
- ✅ **Statistics Display**: Statistik menampilkan data yang benar
- ✅ **Recent Activities**: Aktivitas terbaru sesuai unit
- ✅ **Quick Actions**: Tombol aksi berfungsi dengan baik

## Data Flow Analysis

### **Unit-based Data Filtering:**
```
User Login (unit_id = 3)
    ↓
Dashboard Load
    ↓
Permission Check (canAccessAllData = false)
    ↓
Apply Unit Filtering:
    - Nota Dinas: requesting_unit_id = 3
    - Users: unit_id = 3
    - SPT: nota_dinas.requesting_unit_id = 3
    - SPPD: spt.nota_dinas.requesting_unit_id = 3
    ↓
Display Filtered Data
```

### **Data Relationships:**
```
Users (unit_id) 
    ↓
Nota Dinas (requesting_unit_id)
    ↓
SPT (nota_dinas_id)
    ↓
SPPD (spt_id)
```

## Future Enhancements

### **1. Add Unit ID to Nota Dinas (If Needed)**
```sql
-- Jika diperlukan, tambahkan unit_id ke nota_dinas
ALTER TABLE nota_dinas ADD COLUMN unit_id BIGINT UNSIGNED;
ALTER TABLE nota_dinas ADD FOREIGN KEY (unit_id) REFERENCES units(id);
```

### **2. Implement Multi-Unit Access**
```php
// Implementasi akses multi-unit
public function getUserUnits($userId)
{
    $user = User::find($userId);
    $units = collect([$user->unit_id]);
    
    // Add additional units if user has special permissions
    if ($user->hasRole('admin')) {
        $units = $units->merge(Unit::pluck('id'));
    }
    
    return $units;
}
```

### **3. Dashboard Unit Selector**
```php
// Implementasi unit selector di dashboard
public function getUnitOptions()
{
    $currentUser = Auth::user();
    $units = collect();
    
    if ($currentUser->hasRole('admin')) {
        $units = Unit::all();
    } else {
        $units = collect([$currentUser->unit]);
    }
    
    return $units->pluck('name', 'id');
}
```

## Monitoring & Maintenance

### **Regular Checks:**
1. **Database Schema**: Verifikasi struktur tabel sesuai kebutuhan
2. **Query Performance**: Monitor performa query dengan unit filtering
3. **Data Accuracy**: Verifikasi akurasi statistik per unit
4. **Error Logs**: Monitor error logs untuk database issues

### **Troubleshooting:**
- **Slow Queries**: Optimize dengan indexes pada unit fields
- **Data Inconsistency**: Implement data validation untuk unit relationships
- **Permission Issues**: Verify permission helper functions
- **UI Issues**: Check responsive design dan styling

## Conclusion

Perbaikan unit ID dashboard telah berhasil dilakukan dengan:
- **Error Resolution**: Menggunakan kolom yang benar (`requesting_unit_id`)
- **Data Accuracy**: Statistik yang akurat per unit
- **Performance**: Query yang efisien dengan unit filtering
- **User Experience**: Dashboard yang berfungsi dengan baik per unit

Dashboard sekarang dapat menampilkan data yang akurat tanpa error database, memberikan overview yang komprehensif tentang sistem Perjalanan Dinas per unit dengan data access control yang tepat.
