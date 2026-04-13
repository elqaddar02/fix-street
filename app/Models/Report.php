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
        'latitude',
        'longitude',
        'status',
        'user_id',
        'category_id',
        'city_id',
        'district_id',
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

    public function comments()
    {
        return $this->hasMany(ReportComment::class);
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
