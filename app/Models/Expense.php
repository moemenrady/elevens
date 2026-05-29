<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Expense extends Model
{
    protected $fillable = [
        'expense_type_id',
        'amount',
        'note',
        'added_by',
    ];

    public function type()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}