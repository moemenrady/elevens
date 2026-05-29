<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'type',
        'amount',
        'expense_type_id',
        'partner_id',
        'note',
        'added_by',
        'balance_before',
        'balance_after',
    ];

    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class);
    }
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
