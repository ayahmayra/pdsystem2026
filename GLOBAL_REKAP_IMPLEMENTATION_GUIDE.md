# Global Rekap Implementation Guide

## Overview

Implementasi rekap global yang menampilkan informasi dari semua dokumen perjalanan dinas dalam satu tabel terpadu, termasuk nota dinas, SPT, SPPD, laporan perjalanan dinas, kwitansi, dan link dokumen pendukung.

## Files Created/Modified

### **1. Livewire Component**
- ✅ **Created**: `app/Livewire/Rekap/GlobalRekap.php`
- ✅ **Features**: Data fetching, filtering, pagination, export functionality

### **2. Blade View**
- ✅ **Created**: `resources/views/livewire/rekap/global.blade.php`
- ✅ **Features**: Responsive table, filters, action buttons, status indicators

### **3. Routes**
- ✅ **Modified**: `routes/web.php` - Added global rekap route
- ✅ **Modified**: `routes/permission-routes.php` - Added permission-based route

### **4. Navigation**
- ✅ **Modified**: `resources/views/components/layouts/app/sidebar.blade.php` - Added menu item

### **5. Documentation**
- ✅ **Created**: `GLOBAL_REKAP_DESIGN.md` - Design document
- ✅ **Created**: `GLOBAL_REKAP_IMPLEMENTATION_GUIDE.md` - This guide

## Component Features

### **Data Display**
- ✅ **Nota Dinas**: Number, date, purpose, origin, destination, duration
- ✅ **SPT**: Number, date, member count, member names
- ✅ **SPPD**: Number, date, departure/return dates, actual duration
- ✅ **Receipt**: Number, date, total amount, status
- ✅ **Trip Report**: Number, date, status
- ✅ **Supporting Documents**: Count and links
- ✅ **Overall Status**: Complete, Incomplete, Pending, Rejected, Draft

### **Filtering Options**
- ✅ **Search**: Text search across all document fields
- ✅ **Date Range**: Filter by date from/to
- ✅ **Status**: Filter by overall status
- ✅ **Location**: Filter by destination city
- ✅ **User**: Filter by specific user
- ✅ **Unit**: Filter by unit/bidang
- ✅ **Clear Filters**: Reset all filters

### **Actions**
- ✅ **View Documents**: Direct links to view each document type
- ✅ **Export PDF**: Export functionality (placeholder)
- ✅ **Export Excel**: Export functionality (placeholder)

### **UI/UX Features**
- ✅ **Responsive Design**: Mobile-friendly table
- ✅ **Loading States**: Loading indicators
- ✅ **Status Badges**: Color-coded status indicators
- ✅ **Pagination**: Efficient data loading
- ✅ **Dark Mode**: Dark theme support

## Database Relationships

### **Document Flow**
```
Nota Dinas (1) → SPT (1) → SPPD (1) → Receipt (1)
                              ↓
                        Trip Report (1)
```

### **Supporting Documents**
```
Supporting Documents (polymorphic)
├── Nota Dinas
├── SPT
├── SPPD
├── Receipt
└── Trip Report
```

## Status Calculation Logic

### **Overall Status Priority**
1. **Rejected**: If any document is rejected
2. **Pending**: If any document is pending
3. **Incomplete**: If any document is draft or missing
4. **Complete**: If all 4+ documents exist and none are draft

### **Status Colors**
- ✅ **Complete**: Green
- ⚠️ **Incomplete**: Yellow
- ⏳ **Pending**: Blue
- ❌ **Rejected**: Red
- 📝 **Draft**: Gray

## Route Structure

### **Main Routes**
```php
Route::get('rekap/global', GlobalRekap::class)->name('rekap.global');
```

### **Permission Routes**
```php
Route::middleware(['auth', 'permission:rekap.view', 'unit.scope'])->group(function () {
    Route::get('rekap/global', \App\Livewire\Rekap\GlobalRekap::class)->name('rekap.global');
});
```

## Navigation Menu

### **Sidebar Structure**
```blade
Rekapitulasi
├── 📊 Rekap Global    (NEW)
└── 👥 Rekap Pegawai   (Existing)
```

## Performance Considerations

### **Database Optimization**
- ✅ **Eager Loading**: Uses `with()` for related models
- ✅ **Pagination**: Limits data per page (25 records)
- ✅ **Indexed Queries**: Uses indexed columns for filtering

### **Frontend Optimization**
- ✅ **Debounced Search**: 300ms delay for search input
- ✅ **Live Updates**: Real-time filter updates
- ✅ **Loading States**: User feedback during data loading

## Security Features

### **Access Control**
- ✅ **Authentication**: Requires user login
- ✅ **Permission Check**: `rekap.view` permission required
- ✅ **Unit Scope**: Data filtered by user's unit access

### **Data Validation**
- ✅ **Input Sanitization**: All filter inputs validated
- ✅ **SQL Injection Protection**: Uses Eloquent ORM
- ✅ **XSS Protection**: Blade templating with escaping

## Testing Results

### **Route Verification**
```bash
$ php artisan route:list | grep rekap
GET|HEAD   rekap/global ................................. rekap.global › App\Livewire\Rekap\GlobalRekap
GET|HEAD   rekap/pegawai ................................... rekap.pegawai › App\Livewire\Rekap\Pegawai
GET|HEAD   rekap/pegawai/pdf ................... rekap.pegawai.pdf › RekapPegawaiController@generatePdf
```

### **File Structure**
```
app/Livewire/Rekap/
├── GlobalRekap.php          # ✅ NEW - Global rekap component
└── Pegawai.php              # ✅ Existing - Employee rekap

resources/views/livewire/rekap/
├── global.blade.php         # ✅ NEW - Global rekap view
└── pegawai.blade.php        # ✅ Existing - Employee rekap view
```

## Usage Instructions

### **Accessing Global Rekap**
1. Login to the system
2. Navigate to **Rekapitulasi** → **Rekap Global**
3. Use filters to narrow down results
4. Click on document icons to view specific documents
5. Use export buttons for PDF/Excel export (when implemented)

### **Filtering Data**
1. **Search**: Type in search box to find specific documents
2. **Date Range**: Select start and end dates
3. **Status**: Choose from dropdown (All, Complete, Incomplete, etc.)
4. **Location**: Select destination city
5. **User**: Select specific employee
6. **Unit**: Select unit/bidang
7. **Clear**: Reset all filters

### **Viewing Documents**
- Click on colored icons in the "Aksi" column
- Blue: Nota Dinas
- Green: SPT
- Purple: SPPD
- Orange: Receipt
- Indigo: Trip Report

## Future Enhancements

### **Phase 2 Features**
- 📊 **Advanced Analytics**: Charts and graphs
- 📈 **Export Functionality**: Full PDF/Excel export
- 🔍 **Advanced Search**: More search options
- 📱 **Mobile App**: Mobile-specific interface

### **Phase 3 Features**
- ⚡ **Real-time Updates**: Live data updates
- 🎯 **Custom Dashboards**: Personalized views
- 📊 **Business Intelligence**: Advanced reporting
- 🔔 **Notifications**: Status change alerts

## Troubleshooting

### **Common Issues**

#### **1. Route Not Found**
- Check if route is properly registered
- Verify middleware configuration
- Ensure user has proper permissions

#### **2. Data Not Loading**
- Check database connections
- Verify model relationships
- Check for missing data

#### **3. Permission Denied**
- Verify user has `rekap.view` permission
- Check unit scope access
- Ensure user is properly authenticated

### **Debug Commands**
```bash
# Check routes
php artisan route:list | grep rekap

# Check permissions
php artisan tinker
>>> $user = User::find(1);
>>> $user->can('rekap.view');

# Check data
php artisan tinker
>>> NotaDinas::with('spt.sppd.receipt')->count();
```

## Conclusion

**Global Rekap telah berhasil diimplementasikan dengan fitur-fitur lengkap:**

- ✅ **Comprehensive Data Display**: Menampilkan semua dokumen dalam satu view
- ✅ **Advanced Filtering**: Multiple filter options untuk pencarian data
- ✅ **Responsive Design**: Interface yang user-friendly dan mobile-responsive
- ✅ **Security**: Proper access control dan data validation
- ✅ **Performance**: Optimized queries dan pagination
- ✅ **Extensible**: Siap untuk pengembangan fitur lanjutan

**Implementasi ini memberikan visibilitas lengkap terhadap semua dokumen perjalanan dinas, memudahkan monitoring dan analisis data secara terpusat.**
