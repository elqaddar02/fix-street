<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'status',
        'user_id',
        'category_id',
        'city_id',
        'district_id',
        'quartier_id',
        'latitude',
        'longitude',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
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

    // dossier_id is deliberately not fillable: reports are attached to a case by admins only.
    public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }

    public function comments()
    {
        return $this->hasMany(ReportComment::class);
    }

    public function visibleComments()
    {
        return $this->hasMany(ReportComment::class)->visible();
    }

    public function likes()
    {
        return $this->hasMany(ReportLike::class);
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'report_likes', 'report_id', 'user_id')
                    ->withTimestamps();
    }
}
