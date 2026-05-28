<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublishJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_cluster_id',
        'status',
        'attempt_count',
        'last_error',
        'scheduled_at',
        'executed_at',
    ];

    protected function casts(): array
    {
        return [
            'attempt_count' => 'integer',
            'scheduled_at' => 'datetime',
            'executed_at' => 'datetime',
        ];
    }
}
