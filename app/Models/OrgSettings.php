<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrgSettings extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'address',
        'city',
        'province',
        'phone',
        'email',
        'website',
        'head_user_id',
        'head_title',
        'signature_path',
        'stamp_path',
        'logo_path',
        'settings',
        'singleton',
    ];

    protected $casts = [
        'settings' => 'array',
        'singleton' => 'boolean',
    ];

    /**
     * Relationship with head user
     */
    public function headUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    /**
     * Get the singleton instance
     */
    public static function getInstance(): self
    {
        return self::firstOrCreate(['singleton' => true], [
            'name' => 'Nama Organisasi',
            'short_name' => 'ORG',
            'head_title' => 'Kepala Dinas',
            'settings' => [
                'ym_separator' => '/',
                'qr_footer_text' => 'Verifikasi keaslian dokumen via QR.',
                'letterhead' => [
                    'show_left_logo' => true,
                    'show_right_logo' => false,
                ],
            ],
        ]);
    }

    /**
     * Get setting value by key
     */
    public function getSetting(string $key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Set setting value by key
     */
    public function setSetting(string $key, $value): void
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->settings = $settings;
    }

    /**
     * Get full organization info
     */
    public function getFullInfo(): string
    {
        $info = $this->name;
        if ($this->short_name) {
            $info .= " ({$this->short_name})";
        }
        return $info;
    }
}