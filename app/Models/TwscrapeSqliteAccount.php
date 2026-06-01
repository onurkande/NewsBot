<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwscrapeSqliteAccount extends Model
{
    protected $connection = 'twscrape';
    protected $table = 'accounts';
    protected $primaryKey = 'username';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // twscrape DB doesn't have laravel timestamps

    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
    ];
}
