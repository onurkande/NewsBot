<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'check_name',
        'status',
        'response_time_ms',
        'details_json',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'details_json' => 'array',
            'checked_at' => 'datetime',
            'response_time_ms' => 'integer',
        ];
    }
}
