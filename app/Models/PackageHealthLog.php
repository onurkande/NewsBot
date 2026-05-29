<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageHealthLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_name',
        'status',
        'version',
        'last_check_at',
        'error_message',
        'response_time_ms',
    ];

    protected function casts(): array
    {
        return [
            'last_check_at' => 'datetime',
            'response_time_ms' => 'integer',
        ];
    }
}
