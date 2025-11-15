<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class Credential extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'system',
        'username',
        'password',
        'password_encrypted',
        'notes',
        'url',
        'created_by',
        'updated_by',
    ];

    /**
     * Sempre que setar a senha, já criptografa.
     */
    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password_encrypted'] = Crypt::encryptString($value);
    }

    /**
     * Acessor "virtual" para pegar a senha de forma descriptografada.
     * Use com MUITO critério.
     */
    public function getPasswordAttribute(): ?string
    {
        if (! isset($this->attributes['password_encrypted'])) {
            return null;
        }

        return Crypt::decryptString($this->attributes['password_encrypted']);
    }

    // Helpers para created_by/updated_by
    protected static function booted()
    {
        static::creating(function (Credential $credential) {
            if (Auth::check()) {
                $credential->created_by = Auth::id();
                $credential->updated_by = Auth::id();
            }
        });

        static::updating(function (Credential $credential) {
            if (Auth::check()) {
                $credential->updated_by = Auth::id();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function viewLogs(): HasMany
    {
        return $this->hasMany(CredentialViewLog::class);
    }
}
