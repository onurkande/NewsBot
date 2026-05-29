<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'level',
        'module',
        'message',
        'context_json',
    ];

    protected function casts(): array
    {
        return [
            'context_json' => 'array',
        ];
    }
}
