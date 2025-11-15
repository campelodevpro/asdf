<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CredentialViewLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'credential_id',
        'user_id',
        'event',
        'ip_address',
        'user_agent',
        'request_path',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function credential(): BelongsTo
    {
        return $this->belongsTo(Credential::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
