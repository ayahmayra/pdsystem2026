<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nip',
        'nik',
        'gelar_depan',
        'gelar_belakang',
        'phone',
        'whatsapp',
        'address',
        'unit_id',
        'position_id',
        'position_desc',
        'rank_id',
        'travel_grade_id',
        'npwp',
        'bank_name',
        'bank_account_no',
        'bank_account_name',
        'birth_date',
        'gender',
        'signature_path',
        'photo_path',
        'is_signer',
        'is_non_staff',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'is_signer' => 'boolean',
            'is_non_staff' => 'boolean',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Set birth_date attribute
     */
    public function setBirthDateAttribute($value)
    {
        $this->attributes['birth_date'] = ($value === '' || $value === null) ? null : $value;
    }

    /**
     * Get the full name with titles
     */
    public function fullNameWithTitles(): string
    {
        $name = '';
        if ($this->gelar_depan) {
            $name .= $this->gelar_depan . ' ';
        }
        $name .= $this->name;
        if ($this->gelar_belakang) {
            $name .= ', ' . $this->gelar_belakang;
        }
        return $name;
    }

    /**
     * Relationship with Unit
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Relationship with Position
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Relationship with Rank
     */
    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }

    /**
     * Get echelon through position relationship
     */
    public function echelon(): ?Echelon
    {
        return $this->position?->echelon;
    }

    /**
     * Relationship with Travel Grade
     */
    public function travelGrade(): BelongsTo
    {
        return $this->belongsTo(TravelGrade::class);
    }

    /**
     * Get the sub kegiatan records that this user manages as PPTK.
     */
    public function subKegiatan(): HasMany
    {
        return $this->hasMany(SubKeg::class, 'pptk_user_id');
    }

    /**
     * Get nota dinas where this user is the "to" user (only non-deleted)
     */
    public function notaDinasTo(): HasMany
    {
        return $this->hasMany(NotaDinas::class, 'to_user_id')->whereNull('deleted_at');
    }

    /**
     * Get nota dinas where this user is the "from" user (only non-deleted)
     */
    public function notaDinasFrom(): HasMany
    {
        return $this->hasMany(NotaDinas::class, 'from_user_id')->whereNull('deleted_at');
    }

    /**
     * Get nota dinas where this user is the creator (only non-deleted)
     */
    public function notaDinasCreated(): HasMany
    {
        return $this->hasMany(NotaDinas::class, 'created_by')->whereNull('deleted_at');
    }

    /**
     * Get nota dinas where this user is the approver (only non-deleted)
     */
    public function notaDinasApproved(): HasMany
    {
        return $this->hasMany(NotaDinas::class, 'approved_by')->whereNull('deleted_at');
    }

    /**
     * Get nota dinas participants where this user is a participant (only from non-deleted nota dinas)
     */
    public function notaDinasParticipants(): HasMany
    {
        return $this->hasMany(NotaDinasParticipant::class, 'user_id')
            ->whereHas('notaDinas', function($query) {
                $query->whereNull('deleted_at');
            });
    }

    /**
     * Get receipts where this user is the treasurer (bendahara) (only non-deleted)
     */
    public function receiptsAsTreasurer(): HasMany
    {
        return $this->hasMany(Receipt::class, 'treasurer_user_id')->whereNull('deleted_at');
    }

    /**
     * Get receipts where this user is the treasurer (bendahara) (including soft deleted)
     */
    public function receiptsAsTreasurerWithTrashed(): HasMany
    {
        return $this->hasMany(Receipt::class, 'treasurer_user_id')->withTrashed();
    }

    /**
     * Get receipts where this user is the payee (penerima pembayaran) (only non-deleted)
     */
    public function receiptsAsPayee(): HasMany
    {
        return $this->hasMany(Receipt::class, 'payee_user_id')->whereNull('deleted_at');
    }

    /**
     * Get receipts where this user is the payee (penerima pembayaran) (including soft deleted)
     */
    public function receiptsAsPayeeWithTrashed(): HasMany
    {
        return $this->hasMany(Receipt::class, 'payee_user_id')->withTrashed();
    }

    /**
     * Check if user is used in any nota dinas documents
     */
    public function isUsedInNotaDinas(): bool
    {
        return $this->notaDinasTo()->exists() ||
               $this->notaDinasFrom()->exists() ||
               $this->notaDinasCreated()->exists() ||
               $this->notaDinasApproved()->exists() ||
               $this->notaDinasParticipants()->exists();
    }

    /**
     * Check if user is used in any sub kegiatan as PPTK
     */
    public function isUsedInSubKegiatan(): bool
    {
        return $this->subKegiatan()->exists();
    }

    /**
     * Check if user is used as treasurer (bendahara) in any receipts
     */
    public function isUsedAsTreasurer(): bool
    {
        return $this->receiptsAsTreasurer()->exists();
    }

    /**
     * Check if user is used as payee (penerima pembayaran) in any receipts
     */
    public function isUsedAsPayee(): bool
    {
        return $this->receiptsAsPayee()->exists();
    }

    /**
     * Check if user is used in any receipts (including soft deleted) - for foreign key constraint validation
     */
    public function isUsedInReceiptsWithTrashed(): bool
    {
        return $this->receiptsAsTreasurerWithTrashed()->exists() || $this->receiptsAsPayeeWithTrashed()->exists();
    }

    /**
     * Check if user is used in any documents (nota dinas, sub kegiatan, or receipts)
     */
    public function isUsedInDocuments(): bool
    {
        return $this->isUsedInNotaDinas() || $this->isUsedInSubKegiatan() || $this->isUsedAsTreasurer() || $this->isUsedAsPayee();
    }

    /**
     * Check if user is used in any documents (including soft deleted receipts) - for foreign key constraint validation
     */
    public function isUsedInDocumentsWithTrashed(): bool
    {
        return $this->isUsedInNotaDinas() || $this->isUsedInSubKegiatan() || $this->isUsedInReceiptsWithTrashed();
    }

    /**
     * Check if user deletion will cause foreign key constraint violation
     * This checks for any hard references in the database, regardless of soft delete status
     */
    public function willCauseForeignKeyViolation(): bool
    {
        // Check for hard references in receipts table
        $receiptsCount = DB::table('receipts')
            ->where('payee_user_id', $this->id)
            ->orWhere('treasurer_user_id', $this->id)
            ->count();
            
        return $receiptsCount > 0;
    }

    /**
     * Clean up soft deleted receipts that reference this user
     * This allows the user to be deleted if they're only referenced in soft deleted documents
     */
    public function cleanupSoftDeletedReferences(): int
    {
        $cleanedCount = 0;
        
        // Hard delete soft deleted receipts where user is payee (payee_user_id cannot be null)
        $payeeReceipts = DB::table('receipts')
            ->where('payee_user_id', $this->id)
            ->whereNotNull('deleted_at')
            ->delete();
        $cleanedCount += $payeeReceipts;
        
        // Clean up soft deleted receipts where user is treasurer (treasurer_user_id can be null)
        $treasurerReceipts = DB::table('receipts')
            ->where('treasurer_user_id', $this->id)
            ->whereNotNull('deleted_at')
            ->update(['treasurer_user_id' => null]);
        $cleanedCount += $treasurerReceipts;
        
        return $cleanedCount;
    }

    /**
     * Get all nota dinas documents where this user is involved
     */
    public function getAllNotaDinasInvolvement(): array
    {
        $involvements = [];

        // Check as "to" user
        $toNotaDinas = $this->notaDinasTo()->get();
        if ($toNotaDinas->count() > 0) {
            $involvements[] = [
                'type' => 'Kepada',
                'count' => $toNotaDinas->count(),
                'documents' => $toNotaDinas->pluck('doc_no')->toArray()
            ];
        }

        // Check as "from" user
        $fromNotaDinas = $this->notaDinasFrom()->get();
        if ($fromNotaDinas->count() > 0) {
            $involvements[] = [
                'type' => 'Dari',
                'count' => $fromNotaDinas->count(),
                'documents' => $fromNotaDinas->pluck('doc_no')->toArray()
            ];
        }

        // Check as creator
        $createdNotaDinas = $this->notaDinasCreated()->get();
        if ($createdNotaDinas->count() > 0) {
            $involvements[] = [
                'type' => 'Pembuat',
                'count' => $createdNotaDinas->count(),
                'documents' => $createdNotaDinas->pluck('doc_no')->toArray()
            ];
        }

        // Check as approver
        $approvedNotaDinas = $this->notaDinasApproved()->get();
        if ($approvedNotaDinas->count() > 0) {
            $involvements[] = [
                'type' => 'Penyetuju',
                'count' => $approvedNotaDinas->count(),
                'documents' => $approvedNotaDinas->pluck('doc_no')->toArray()
            ];
        }

        // Check as participant
        $participantNotaDinas = $this->notaDinasParticipants()->with('notaDinas')->get();
        if ($participantNotaDinas->count() > 0) {
            $involvements[] = [
                'type' => 'Peserta',
                'count' => $participantNotaDinas->count(),
                'documents' => $participantNotaDinas->pluck('notaDinas.doc_no')->toArray()
            ];
        }

        return $involvements;
    }

    /**
     * Get all sub kegiatan documents where this user is involved as PPTK
     */
    public function getAllSubKegiatanInvolvement(): array
    {
        $involvements = [];

        // Check as PPTK
        $subKegiatan = $this->subKegiatan()->get();
        if ($subKegiatan->count() > 0) {
            $involvements[] = [
                'type' => 'PPTK',
                'count' => $subKegiatan->count(),
                'documents' => $subKegiatan->pluck('display_name')->toArray()
            ];
        }

        return $involvements;
    }

    /**
     * Get all receipts where this user is involved as treasurer (bendahara)
     */
    public function getAllTreasurerInvolvement(): array
    {
        $involvements = [];

        // Check as Bendahara Pengeluaran
        $bendaharaReceipts = $this->receiptsAsTreasurer()->where('treasurer_title', 'Bendahara Pengeluaran')->get();
        if ($bendaharaReceipts->count() > 0) {
            $involvements[] = [
                'type' => 'Bendahara Pengeluaran',
                'count' => $bendaharaReceipts->count(),
                'documents' => $bendaharaReceipts->pluck('receipt_no')->toArray()
            ];
        }

        // Check as Bendahara Pengeluaran Pembantu
        $bendaharaPembantuReceipts = $this->receiptsAsTreasurer()->where('treasurer_title', 'Bendahara Pengeluaran Pembantu')->get();
        if ($bendaharaPembantuReceipts->count() > 0) {
            $involvements[] = [
                'type' => 'Bendahara Pengeluaran Pembantu',
                'count' => $bendaharaPembantuReceipts->count(),
                'documents' => $bendaharaPembantuReceipts->pluck('receipt_no')->toArray()
            ];
        }

        return $involvements;
    }

    /**
     * Get all receipts where this user is involved as payee (penerima pembayaran)
     */
    public function getAllPayeeInvolvement(): array
    {
        $involvements = [];

        // Check as Payee
        $payeeReceipts = $this->receiptsAsPayee()->get();
        if ($payeeReceipts->count() > 0) {
            $involvements[] = [
                'type' => 'Penerima Pembayaran',
                'count' => $payeeReceipts->count(),
                'documents' => $payeeReceipts->pluck('receipt_no')->toArray()
            ];
        }

        return $involvements;
    }

    /**
     * Get all receipts where this user is involved as treasurer (including soft deleted)
     */
    public function getAllTreasurerInvolvementWithTrashed(): array
    {
        $involvements = [];

        // Check as Bendahara Pengeluaran
        $bendaharaReceipts = $this->receiptsAsTreasurerWithTrashed()->where('treasurer_title', 'Bendahara Pengeluaran')->get();
        if ($bendaharaReceipts->count() > 0) {
            $involvements[] = [
                'type' => 'Bendahara Pengeluaran',
                'count' => $bendaharaReceipts->count(),
                'documents' => $bendaharaReceipts->pluck('receipt_no')->toArray()
            ];
        }

        // Check as Bendahara Pengeluaran Pembantu
        $bendaharaPembantuReceipts = $this->receiptsAsTreasurerWithTrashed()->where('treasurer_title', 'Bendahara Pengeluaran Pembantu')->get();
        if ($bendaharaPembantuReceipts->count() > 0) {
            $involvements[] = [
                'type' => 'Bendahara Pengeluaran Pembantu',
                'count' => $bendaharaPembantuReceipts->count(),
                'documents' => $bendaharaPembantuReceipts->pluck('receipt_no')->toArray()
            ];
        }

        return $involvements;
    }

    /**
     * Get all receipts where this user is involved as payee (including soft deleted)
     */
    public function getAllPayeeInvolvementWithTrashed(): array
    {
        $involvements = [];

        // Check as Payee
        $payeeReceipts = $this->receiptsAsPayeeWithTrashed()->get();
        if ($payeeReceipts->count() > 0) {
            $involvements[] = [
                'type' => 'Penerima Pembayaran',
                'count' => $payeeReceipts->count(),
                'documents' => $payeeReceipts->pluck('receipt_no')->toArray()
            ];
        }

        return $involvements;
    }

    /**
     * Get all document involvements (nota dinas, sub kegiatan, and receipts)
     */
    public function getAllDocumentInvolvements(): array
    {
        $involvements = [];

        // Get nota dinas involvements
        $notaDinasInvolvements = $this->getAllNotaDinasInvolvement();
        if (!empty($notaDinasInvolvements)) {
            $involvements['nota_dinas'] = $notaDinasInvolvements;
        }

        // Get sub kegiatan involvements
        $subKegiatanInvolvements = $this->getAllSubKegiatanInvolvement();
        if (!empty($subKegiatanInvolvements)) {
            $involvements['sub_kegiatan'] = $subKegiatanInvolvements;
        }

        // Get treasurer involvements
        $treasurerInvolvements = $this->getAllTreasurerInvolvement();
        if (!empty($treasurerInvolvements)) {
            $involvements['receipts'] = $treasurerInvolvements;
        }

        // Get payee involvements
        $payeeInvolvements = $this->getAllPayeeInvolvement();
        if (!empty($payeeInvolvements)) {
            $involvements['receipts'] = array_merge($involvements['receipts'] ?? [], $payeeInvolvements);
        }

        return $involvements;
    }

    /**
     * Get all document involvements including soft deleted receipts (for foreign key constraint validation)
     */
    public function getAllDocumentInvolvementsWithTrashed(): array
    {
        $involvements = [];

        // Get nota dinas involvements
        $notaDinasInvolvements = $this->getAllNotaDinasInvolvement();
        if (!empty($notaDinasInvolvements)) {
            $involvements['nota_dinas'] = $notaDinasInvolvements;
        }

        // Get sub kegiatan involvements
        $subKegiatanInvolvements = $this->getAllSubKegiatanInvolvement();
        if (!empty($subKegiatanInvolvements)) {
            $involvements['sub_kegiatan'] = $subKegiatanInvolvements;
        }

        // Get treasurer involvements (including soft deleted)
        $treasurerInvolvements = $this->getAllTreasurerInvolvementWithTrashed();
        if (!empty($treasurerInvolvements)) {
            $involvements['receipts'] = $treasurerInvolvements;
        }

        // Get payee involvements (including soft deleted)
        $payeeInvolvements = $this->getAllPayeeInvolvementWithTrashed();
        if (!empty($payeeInvolvements)) {
            $involvements['receipts'] = array_merge($involvements['receipts'] ?? [], $payeeInvolvements);
        }

        return $involvements;
    }

    /**
     * Check if user is superadmin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    /**
     * Check if user can be modified by current user
     * Superadmin can only be modified by other superadmins
     */
    public function canBeModifiedBy(User $currentUser): bool
    {
        // If this user is superadmin, only other superadmins can modify
        if ($this->isSuperAdmin()) {
            return $currentUser->isSuperAdmin();
        }
        
        // Non-superadmin users can be modified by admin or superadmin
        return $currentUser->hasAnyRole(['admin', 'super-admin']);
    }
}
