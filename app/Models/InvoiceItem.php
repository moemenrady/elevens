<?php

// app/Models/InvoiceItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
  protected $table = 'invoice_items';
  protected $fillable = [
        'invoice_id','item_type','product_id',
        'name','qty','price','cost','total','description'
    ];
 protected $guarded = [];
    public function invoice(): BelongsTo {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo {
        return $this->belongsTo(Product::class);
    }


}
