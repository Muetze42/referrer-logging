<?php

namespace NormanHuth\ReferrerLogging\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referrer extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer',
        'target',
        'referrer_host_id',
    ];

    public function host()
    {
        return $this->belongsTo(ReferrerHost::class);
    }
}
