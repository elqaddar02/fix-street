<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quartier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'city_id', 'active'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
