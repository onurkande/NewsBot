<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SourceCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected $appends = [
        'display_initials',
        'avatar_class',
        'description_text',
        'sources_label',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function sourceAccounts(): HasMany
    {
        return $this->hasMany(SourceAccount::class, 'category_id');
    }

    public function storyClusters(): HasMany
    {
        return $this->hasMany(StoryCluster::class, 'category_id');
    }

    public function getDisplayInitialsAttribute(): string
    {
        $words = preg_split('/\s+/u', trim((string) $this->name), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($words === []) {
            return 'SC';
        }

        $initials = collect($words)
            ->take(2)
            ->map(static fn (string $word) => Str::upper(Str::substr($word, 0, 1)))
            ->implode('');

        return $initials !== '' ? $initials : 'SC';
    }

    public function getAvatarClassAttribute(): string
    {
        $palette = ['ma-1', 'ma-2', 'ma-3', 'ma-4', 'ma-5', 'ma-6'];

        if ($this->getKey() === null) {
            return $palette[0];
        }

        return $palette[crc32((string) $this->getKey()) % count($palette)];
    }

    public function getDescriptionTextAttribute(): string
    {
        $description = trim((string) $this->description);

        if ($description === '') {
            return '—';
        }

        return Str::limit($description, 90);
    }

    public function getSourcesLabelAttribute(): string
    {
        $count = (int) ($this->source_accounts_count ?? $this->sourceAccounts()->count());

        return $count . ' hesap';
    }
}
