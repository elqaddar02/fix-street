<?php

namespace App\Models;

use App\Enums\ConfirmationType;
use App\Enums\ContributionStrength;
use App\Enums\DossierEventType;
use App\Enums\DossierStage;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A "Case" (FR: dossier, AR: ملف) grouping the reports for one street problem.
 * Named Dossier in code because "case" is a reserved word in PHP.
 */
class Dossier extends Model
{
    use HasFactory;

    // "stage" is deliberately not fillable: only App\Services\DossierTimeline changes it.
    protected $fillable = [
        'slug',
        'short_code',
        'title',
        'summary',
        'city_id',
        'district_id',
        'quartier_id',
        'authority_name',
        'cover_image',
        'published_at',
        'contribution_strength',
        'contribution_notes',
        'scorecard_targets',
        'created_by',
    ];

    protected $attributes = [
        'stage' => 'collecting',
    ];

    protected function casts(): array
    {
        return [
            'stage' => DossierStage::class,
            'contribution_strength' => ContributionStrength::class,
            'published_at' => 'datetime',
            'scorecard_targets' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function quartier()
    {
        return $this->belongsTo(Quartier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function events()
    {
        return $this->hasMany(DossierEvent::class)->orderBy('occurred_at')->orderBy('id');
    }

    public function confirmations()
    {
        return $this->hasMany(DossierConfirmation::class);
    }

    public function visibleConfirmations()
    {
        return $this->hasMany(DossierConfirmation::class)->visible();
    }

    public function scans()
    {
        return $this->hasMany(DossierScan::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /**
     * Number of different people with a visible confirmation of this type, optionally only recent ones.
     */
    public function confirmerCount(ConfirmationType $type, ?DateTimeInterface $since = null): int
    {
        return $this->visibleConfirmations()
            ->where('type', $type)
            ->when($since, fn ($query) => $query->where('created_at', '>=', $since))
            ->distinct()
            ->count('user_id');
    }

    public function latestEventOfType(DossierEventType $type): ?DossierEvent
    {
        return $this->events()
            ->reorder()
            ->where('type', $type)
            ->latest('occurred_at')
            ->latest('id')
            ->first();
    }
}
