<?php

namespace App\Models;

use App\Enums\DossierEventType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use LogicException;

/**
 * One dated entry in a case's public timeline. Add through App\Services\DossierTimeline.
 */
class DossierEvent extends Model
{
    use HasFactory;

    // Append-only log: Eloquent still sets created_at on create(), but there is no updated_at.
    public const UPDATED_AT = null;

    protected $fillable = [
        'dossier_id',
        'type',
        'occurred_at',
        'description',
        'channel',
        'reference_number',
        'attachment',
        'is_public',
        'recorded_by',
        'corrects_event_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => DossierEventType::class,
            'occurred_at' => 'datetime',
            'is_public' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Evidence must not change after the fact: mistakes are fixed with a correction event.
        static::updating(function () {
            throw new LogicException('Case timeline events cannot be edited; record a correction instead.');
        });

        static::deleting(function () {
            throw new LogicException('Case timeline events cannot be deleted; record a correction instead.');
        });
    }

    public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function corrects()
    {
        return $this->belongsTo(DossierEvent::class, 'corrects_event_id');
    }

    public function corrections()
    {
        return $this->hasMany(DossierEvent::class, 'corrects_event_id');
    }
}
