<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Daily QR/short-link scan count for a case, per flyer placement. No personal data.
 */
class DossierScan extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'dossier_id',
        'source',
        'date',
        'count',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'count' => 'integer',
        ];
    }

    public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }
}
