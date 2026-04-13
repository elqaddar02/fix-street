<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'name_ar', 'active'];

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function districts()
    {
        return $this->hasMany(District::class);
    }

    public function getDisplayNameAttribute()
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->name_ar) {
            return $this->name_ar;
        }
        return $this->name;
    }
}
