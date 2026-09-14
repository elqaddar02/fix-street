<?php

namespace App\Models;

use App\Enums\ConfirmationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A neighbor saying a case's problem is still there, fixed, or not fixed.
 */
class DossierConfirmation extends Model
{
    use HasFactory;

    protected $fillable = [
        'dossier_id',
        'user_id',
        'type',
        'photo',
        'note',
        'cosign_consent',
        'hidden_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => ConfirmationType::class,
            'cosign_consent' => 'boolean',
            'hidden_at' => 'datetime',
        ];
    }

    /**
     * Confirmations not hidden by moderation.
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->whereNull('hidden_at');
    }

    public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
