<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'percentage',
        'last_capital_snapshot',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

} 