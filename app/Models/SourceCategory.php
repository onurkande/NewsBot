<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SourceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function sourceAccounts(): HasMany
    {
        return $this->hasMany(SourceAccount::class, 'category_id');
    }

    public function storyClusters(): HasMany
    {
        return $this->hasMany(StoryCluster::class, 'category_id');
    }
}
