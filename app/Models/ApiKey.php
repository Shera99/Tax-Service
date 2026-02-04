<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    protected $fillable = [
        'name',
        'public_key',
        'secret_key',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    protected $hidden = [
        'secret_key',
    ];

    /**
     * Generate new API key pair.
     */
    public static function generateKeyPair(): array
    {
        return [
            'public_key' => 'pub_' . Str::random(32),
            'secret_key' => 'sec_' . Str::random(32),
        ];
    }

    /**
     * Create signature for request validation.
     */
    public static function createSignature(string $secretKey, string $payload, string $timestamp): string
    {
        $dataToSign = $timestamp . '.' . $payload;
        return hash_hmac('sha256', $dataToSign, $secretKey);
    }

    /**
     * Verify signature.
     */
    public function verifySignature(string $signature, string $payload, string $timestamp): bool
    {
        $expectedSignature = self::createSignature($this->secret_key, $payload, $timestamp);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Update last used timestamp.
     */
    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }
}
