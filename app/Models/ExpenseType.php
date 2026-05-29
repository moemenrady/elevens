<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseType extends Model
{
    protected $fillable = [
        'name',
        'setter_name',
        'user_appearance',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}