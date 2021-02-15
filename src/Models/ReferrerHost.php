<?php

namespace NormanHuth\ReferrerLogging\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferrerHost extends Model
{
    use HasFactory;

    protected $fillable = [
        'host',
        'count',
    ];

    public function referrers()
    {
        return $this->hasMany(Referrer::class);
    }
}
