<?php

// app/Models/InvoiceItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $table = 'invoice_items';
    protected $fillable = [
        'invoice_id',
        'product_id',
        'color_name',
        'size_name',
        'is_printed',
        'name',
        'qty',
        'price',
        'cost',
        'total',
        'description'
    ];
    protected $guarded = [];
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
