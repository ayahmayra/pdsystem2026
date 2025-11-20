# Global Rekap Design Document

## Overview

Rancangan untuk membuat rekap global yang menampilkan informasi dari semua dokumen perjalanan dinas dalam satu tabel terpadu, termasuk nota dinas, SPT, SPPD, laporan perjalanan dinas, kwitansi, dan link dokumen pendukung.

## Database Structure Analysis

### **Current Document Flow**
```
Nota Dinas → SPT → SPPD → Receipt → Trip Report
     ↓         ↓      ↓       ↓         ↓
Supporting Documents (attached to each document)
```

### **Key Relationships**
- **Nota Dinas**: `nota_dinas` table
- **SPT**: `spts` table (belongs to Nota Dinas)
- **SPPD**: `sppds` table (belongs to SPT)
- **Receipt**: `receipts` table (belongs to SPPD)
- **Trip Report**: `trip_reports` table (belongs to SPPD)
- **Supporting Documents**: `supporting_documents` table (polymorphic)

## Global Rekap Table Structure

### **Main Columns**

#### **1. Document Information**
| Column | Type | Description | Source |
|--------|------|-------------|---------|
| `id` | BigInt | Unique identifier | Auto-increment |
| `nota_dinas_id` | BigInt | Reference to Nota Dinas | `nota_dinas.id` |
| `spt_id` | BigInt | Reference to SPT | `spts.id` |
| `sppd_id` | BigInt | Reference to SPPD | `sppds.id` |
| `receipt_id` | BigInt | Reference to Receipt | `receipts.id` |
| `trip_report_id` | BigInt | Reference to Trip Report | `trip_reports.id` |

#### **2. Nota Dinas Information**
| Column | Type | Description | Source |
|--------|------|-------------|---------|
| `nota_dinas_number` | String | Nomor Nota Dinas | `nota_dinas.number` |
| `nota_dinas_date` | Date | Tanggal Nota Dinas | `nota_dinas.date` |
| `nota_dinas_purpose` | Text | Tujuan Perjalanan | `nota_dinas.purpose` |
| `nota_dinas_origin` | String | Tempat Asal | `nota_dinas.origin_place.name` |
| `nota_dinas_destination` | String | Tempat Tujuan | `nota_dinas.destination_city.name` |
| `nota_dinas_duration` | Integer | Lama Perjalanan (hari) | `nota_dinas.duration` |

#### **3. SPT Information**
| Column | Type | Description | Source |
|--------|------|-------------|---------|
| `spt_number` | String | Nomor SPT | `spts.number` |
| `spt_date` | Date | Tanggal SPT | `spts.date` |
| `spt_members_count` | Integer | Jumlah Anggota | `spt_members.count()` |
| `spt_members_names` | Text | Nama Anggota | `spt_members.user.name` (comma-separated) |

#### **4. SPPD Information**
| Column | Type | Description | Source |
|--------|------|-------------|---------|
| `sppd_number` | String | Nomor SPPD | `sppds.number` |
| `sppd_date` | Date | Tanggal SPPD | `sppds.date` |
| `sppd_departure_date` | Date | Tanggal Berangkat | `sppds.departure_date` |
| `sppd_return_date` | Date | Tanggal Kembali | `sppds.return_date` |
| `sppd_actual_duration` | Integer | Lama Perjalanan Aktual | Calculated |

#### **5. Receipt Information**
| Column | Type | Description | Source |
|--------|------|-------------|---------|
| `receipt_number` | String | Nomor Kwitansi | `receipts.number` |
| `receipt_date` | Date | Tanggal Kwitansi | `receipts.date` |
| `receipt_total_amount` | Decimal | Total Biaya | `receipts.total_amount` |
| `receipt_status` | String | Status Kwitansi | `receipts.status` |

#### **6. Trip Report Information**
| Column | Type | Description | Source |
|--------|------|-------------|---------|
| `trip_report_number` | String | Nomor Laporan | `trip_reports.number` |
| `trip_report_date` | Date | Tanggal Laporan | `trip_reports.date` |
| `trip_report_status` | String | Status Laporan | `trip_reports.status` |

#### **7. Supporting Documents**
| Column | Type | Description | Source |
|--------|------|-------------|---------|
| `supporting_documents_count` | Integer | Jumlah Dokumen Pendukung | `supporting_documents.count()` |
| `supporting_documents_links` | Text | Link Dokumen Pendukung | `supporting_documents.file_path` (comma-separated) |

#### **8. Status & Metadata**
| Column | Type | Description | Source |
|--------|------|-------------|---------|
| `overall_status` | String | Status Keseluruhan | Calculated |
| `created_at` | Timestamp | Tanggal Dibuat | Auto |
| `updated_at` | Timestamp | Tanggal Diupdate | Auto |

## UI/UX Design

### **Main Table Layout**

#### **Header Row**
```
| No | Nota Dinas | SPT | SPPD | Receipt | Trip Report | Dokumen Pendukung | Status | Aksi |
```

#### **Data Rows**
```
| 1 | ND-001/2024<br/>01/01/2024<br/>Jakarta → Bali<br/>3 hari | SPT-001/2024<br/>01/01/2024<br/>2 orang<br/>John, Jane | SPPD-001/2024<br/>02/01/2024<br/>02/01 - 05/01<br/>3 hari | KW-001/2024<br/>06/01/2024<br/>Rp 5.000.000<br/>Approved | LP-001/2024<br/>07/01/2024<br/>Completed | 3 dokumen<br/>📎 View | ✅ Complete | 👁️ View |
```

### **Filter Options**

#### **Date Range Filter**
- Tanggal Mulai
- Tanggal Selesai
- Filter berdasarkan: Nota Dinas, SPT, SPPD, Receipt, Trip Report

#### **Status Filter**
- Status Keseluruhan: All, Complete, Incomplete, Pending
- Status Dokumen Individual: All, Draft, Approved, Rejected

#### **Location Filter**
- Tempat Asal
- Tempat Tujuan
- Provinsi

#### **User Filter**
- Nama Pegawai
- Unit/Bidang
- Jabatan

### **Action Buttons**

#### **Per Row**
- 👁️ **View**: Lihat detail dokumen
- 📄 **PDF**: Download PDF dokumen
- ✏️ **Edit**: Edit dokumen (jika memungkinkan)

#### **Bulk Actions**
- 📊 **Export PDF**: Export semua data ke PDF
- 📈 **Export Excel**: Export semua data ke Excel
- 📧 **Send Email**: Kirim laporan via email

## Technical Implementation

### **1. Livewire Component Structure**

```php
class GlobalRekap extends Component
{
    // Properties
    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $statusFilter = '';
    public $locationFilter = '';
    public $userFilter = '';
    
    // Data
    public $rekapData = [];
    public $totalRecords = 0;
    public $perPage = 25;
    public $currentPage = 1;
    
    // Methods
    public function mount()
    public function updatedSearch()
    public function updatedDateFrom()
    public function updatedDateTo()
    public function updatedStatusFilter()
    public function updatedLocationFilter()
    public function updatedUserFilter()
    public function loadRekapData()
    public function exportPdf()
    public function exportExcel()
    public function viewDocument($type, $id)
    public function downloadPdf($type, $id)
}
```

### **2. Data Fetching Strategy**

#### **Option A: Single Query with Joins**
```sql
SELECT 
    nd.id as nota_dinas_id,
    nd.number as nota_dinas_number,
    nd.date as nota_dinas_date,
    nd.purpose as nota_dinas_purpose,
    op.name as nota_dinas_origin,
    dc.name as nota_dinas_destination,
    nd.duration as nota_dinas_duration,
    
    spt.id as spt_id,
    spt.number as spt_number,
    spt.date as spt_date,
    
    sppd.id as sppd_id,
    sppd.number as sppd_number,
    sppd.date as sppd_date,
    sppd.departure_date,
    sppd.return_date,
    
    r.id as receipt_id,
    r.number as receipt_number,
    r.date as receipt_date,
    r.total_amount as receipt_total_amount,
    r.status as receipt_status,
    
    tr.id as trip_report_id,
    tr.number as trip_report_number,
    tr.date as trip_report_date,
    tr.status as trip_report_status,
    
    COUNT(sd.id) as supporting_documents_count
    
FROM nota_dinas nd
LEFT JOIN spts spt ON nd.id = spt.nota_dinas_id
LEFT JOIN sppds sppd ON spt.id = sppd.spt_id
LEFT JOIN receipts r ON sppd.id = r.sppd_id
LEFT JOIN trip_reports tr ON sppd.id = tr.sppd_id
LEFT JOIN supporting_documents sd ON (
    (sd.documentable_type = 'App\\Models\\NotaDinas' AND sd.documentable_id = nd.id) OR
    (sd.documentable_type = 'App\\Models\\Spt' AND sd.documentable_id = spt.id) OR
    (sd.documentable_type = 'App\\Models\\Sppd' AND sd.documentable_id = sppd.id) OR
    (sd.documentable_type = 'App\\Models\\Receipt' AND sd.documentable_id = r.id) OR
    (sd.documentable_type = 'App\\Models\\TripReport' AND sd.documentable_id = tr.id)
)
LEFT JOIN org_places op ON nd.origin_place_id = op.id
LEFT JOIN cities dc ON nd.destination_city_id = dc.id
GROUP BY nd.id, spt.id, sppd.id, r.id, tr.id
ORDER BY nd.date DESC
```

#### **Option B: Multiple Queries (More Flexible)**
```php
public function loadRekapData()
{
    $notaDinas = NotaDinas::with([
        'spt.sppd.receipt',
        'spt.sppd.tripReport',
        'spt.sptMembers.user',
        'originPlace',
        'destinationCity',
        'supportingDocuments'
    ])->get();
    
    $this->rekapData = $notaDinas->map(function($nd) {
        return $this->formatRekapRow($nd);
    });
}
```

### **3. View Structure**

#### **Main View** (`resources/views/livewire/rekap/global.blade.php`)
```blade
<div>
    <!-- Filters -->
    <div class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label>Search</label>
                <input type="text" wire:model.live="search" placeholder="Search...">
            </div>
            <div>
                <label>Date From</label>
                <input type="date" wire:model.live="dateFrom">
            </div>
            <div>
                <label>Date To</label>
                <input type="date" wire:model.live="dateTo">
            </div>
            <div>
                <label>Status</label>
                <select wire:model.live="statusFilter">
                    <option value="">All</option>
                    <option value="complete">Complete</option>
                    <option value="incomplete">Incomplete</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nota Dinas</th>
                    <th>SPT</th>
                    <th>SPPD</th>
                    <th>Receipt</th>
                    <th>Trip Report</th>
                    <th>Dokumen Pendukung</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekapData as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="text-sm">
                            <div class="font-medium">{{ $row['nota_dinas_number'] }}</div>
                            <div class="text-gray-500">{{ $row['nota_dinas_date'] }}</div>
                            <div class="text-gray-500">{{ $row['nota_dinas_origin'] }} → {{ $row['nota_dinas_destination'] }}</div>
                            <div class="text-gray-500">{{ $row['nota_dinas_duration'] }} hari</div>
                        </div>
                    </td>
                    <!-- Other columns... -->
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $rekapData->links() }}
    </div>
</div>
```

## Routes & Navigation

### **Routes**
```php
// Global Rekap Routes
Route::get('rekap/global', GlobalRekap::class)->name('rekap.global');
Route::get('rekap/global/pdf', [GlobalRekapController::class, 'exportPdf'])->name('rekap.global.pdf');
Route::get('rekap/global/excel', [GlobalRekapController::class, 'exportExcel'])->name('rekap.global.excel');
```

### **Sidebar Menu**
```blade
@if(\App\Helpers\PermissionHelper::can('menu.rekap'))
<flux:navlist variant="outline">
    <flux:navlist.group :heading="__('Rekapitulasi')" class="grid">
        <flux:navlist.item icon="chart-bar" :href="route('rekap.global')" :current="request()->routeIs('rekap.global')" wire:navigate>{{ __('Rekap Global') }}</flux:navlist.item>
        <flux:navlist.item icon="users" :href="route('rekap.pegawai')" :current="request()->routeIs('rekap.pegawai')" wire:navigate>{{ __('Rekap Pegawai') }}</flux:navlist.item>
    </flux:navlist.group>
</flux:navlist>
@endif
```

## Performance Considerations

### **1. Database Optimization**
- Add indexes on frequently queried columns
- Use database views for complex joins
- Implement query result caching

### **2. Frontend Optimization**
- Implement pagination
- Use lazy loading for large datasets
- Add search debouncing
- Implement virtual scrolling for very large tables

### **3. Export Optimization**
- Use queue jobs for large exports
- Implement chunked processing
- Add progress indicators

## Security Considerations

### **1. Access Control**
- Implement role-based access control
- Add unit scope filtering
- Ensure data privacy compliance

### **2. Data Validation**
- Validate all filter inputs
- Sanitize search queries
- Implement rate limiting for exports

## Future Enhancements

### **1. Advanced Features**
- Real-time updates
- Custom column selection
- Saved filter presets
- Dashboard widgets

### **2. Analytics**
- Document completion rates
- Cost analysis
- Travel pattern analysis
- Performance metrics

## Implementation Phases

### **Phase 1: Basic Structure**
1. Create Livewire component
2. Implement basic data fetching
3. Create simple table view
4. Add basic filters

### **Phase 2: Enhanced Features**
1. Add advanced filters
2. Implement pagination
3. Add export functionality
4. Improve UI/UX

### **Phase 3: Optimization**
1. Performance optimization
2. Caching implementation
3. Advanced analytics
4. Mobile responsiveness

## Conclusion

Rancangan rekap global ini akan memberikan visibilitas lengkap terhadap semua dokumen perjalanan dinas dalam satu tempat, memudahkan monitoring dan analisis. Implementasi akan dilakukan secara bertahap untuk memastikan kualitas dan performa yang optimal.
