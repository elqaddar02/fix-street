<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdEvent extends Model
{
    use HasFactory;

    public const TYPE_IMPRESSION = 'impression';
    public const TYPE_CLICK = 'click';

    // Append-only log: Eloquent still sets created_at automatically on
    // create(), but there is no updated_at column to maintain.
    public const UPDATED_AT = null;

    protected $fillable = [
        'ad_id',
        'type',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
