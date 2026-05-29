<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Capital extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'amount',
        'note',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
    
        public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    
}