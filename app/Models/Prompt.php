<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prompt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'prompt_text',
        'source_category_id',
        'version',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function sourceCategory(): BelongsTo
    {
        return $this->belongsTo(SourceCategory::class, 'source_category_id');
    }

    public function generations(): HasMany
    {
        return $this->hasMany(AiGeneration::class);
    }
}
