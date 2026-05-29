<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KioskSession extends Model
{
    protected $fillable = [
        'uuid',
        'device_name',
        'branch_id',
        'is_locked',
        'last_activity',
        'exit_token',
    ];
}