<?php
// app/Models/Invoice.php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{

  protected $fillable = [
    'invoice_number',
    'clinet_id',
    'created_by',
    'total',
    'profit',
    'notes'
  ];

  protected $casts = [
    'total' => 'decimal:2',
    'profit' => 'decimal:2',
  ];


  protected $guarded = [];

  public function items()
  {
    return $this->hasMany(InvoiceItem::class);
  }




  public function client()
  {
    return $this->belongsTo(Client::class);
  }
  public function user()
  {
    return $this->belongsTo(User::class, 'client_id');
  }

  public function scopeBetween($query, $from, $to)
  {
    return $query->whereBetween('created_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()]);
  }
}
