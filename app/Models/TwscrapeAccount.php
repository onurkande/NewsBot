<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwscrapeAccount extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'last_success_at' => 'datetime',
        'last_error_at' => 'datetime',
    ];

    public function sqliteAccount()
    {
        return TwscrapeSqliteAccount::find($this->username);
    }
}
