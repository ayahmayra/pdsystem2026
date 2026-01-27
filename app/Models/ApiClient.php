<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiClient extends Model
{
    protected $fillable = [
        'name',
        'api_key',
        'description',
        'ip_whitelist',
        'is_active',
        'last_used_at',
        'request_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'request_count' => 'integer',
    ];

    protected $hidden = [
        'api_key',
    ];

    /**
     * Generate a new API key
     */
    public static function generateApiKey(): string
    {
        return 'pd_' . Str::random(48);
    }

    /**
     * Hash the API key before saving
     */
    public function setApiKeyAttribute($value): void
    {
        $this->attributes['api_key'] = Hash::make($value);
    }

    /**
     * Verify API key
     */
    public function verifyApiKey(string $apiKey): bool
    {
        return Hash::check($apiKey, $this->api_key);
    }

    /**
     * Check if IP is whitelisted
     */
    public function isIpWhitelisted(string $ip): bool
    {
        if (empty($this->ip_whitelist)) {
            return true; // No whitelist means all IPs allowed
        }

        $whitelistedIps = array_map('trim', explode(',', $this->ip_whitelist));
        return in_array($ip, $whitelistedIps);
    }

    /**
     * Record API usage
     */
    public function recordUsage(): void
    {
        $this->increment('request_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Find client by API key (plain text)
     */
    public static function findByApiKey(string $apiKey): ?self
    {
        $clients = self::where('is_active', true)->get();
        
        foreach ($clients as $client) {
            if ($client->verifyApiKey($apiKey)) {
                return $client;
            }
        }
        
        return null;
    }
}
