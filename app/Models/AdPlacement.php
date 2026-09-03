<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdPlacement extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'type',
    ];

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }
}
