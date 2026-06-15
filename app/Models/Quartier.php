<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quartier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_fr',
        'name_ar',
        'slug',
        'district_id',
        'latitude',
        'longitude',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
