<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    public const PROVIDER_HOUSE = 'house';
    public const PROVIDER_ADSENSE = 'adsense';

    protected $fillable = [
        'ad_placement_id',
        'title',
        'image',
        'target_url',
        'provider',
        'external_slot_id',
        'is_active',
        'starts_at',
        'ends_at',
        'weight',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'weight' => 'integer',
        ];
    }

    public function placement()
    {
        return $this->belongsTo(AdPlacement::class, 'ad_placement_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function events()
    {
        return $this->hasMany(AdEvent::class);
    }

    public function impressions()
    {
        return $this->events()->where('type', AdEvent::TYPE_IMPRESSION);
    }

    public function clicks()
    {
        return $this->events()->where('type', AdEvent::TYPE_CLICK);
    }

    /**
     * Ads that are switched on and within their scheduling window right now.
     */
    public function scopeEligible(Builder $query): Builder
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            });
    }

    public function scopeForPlacement(Builder $query, string $placementSlug): Builder
    {
        return $query->whereHas('placement', fn (Builder $q) => $q->where('slug', $placementSlug));
    }

    /**
     * Whether this ad is switched on and inside its scheduling window right now.
     * Mirrors scopeEligible() for a single already-loaded instance.
     */
    public function isCurrentlyActive(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $this->starts_at->isAfter($now)) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->isBefore($now)) {
            return false;
        }

        return true;
    }
}
