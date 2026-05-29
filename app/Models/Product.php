<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'category_id',
    'name',
    'description',
    'price',
    'cost',
    'quantity',
    'min_quantity',
    'is_produced',
    'image',
    'is_available',
    'is_featured',
    'sort_order',
  ];

  protected $casts = [
    'price' => 'decimal:2',
    'cost' => 'decimal:2',

    'quantity' => 'integer',
    'min_quantity' => 'integer',

    'is_produced' => 'boolean',
    'is_available' => 'boolean',
    'is_featured' => 'boolean',
  ];

  /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

  public function category()
  {
    return $this->belongsTo(Category::class);
  }

  public function purchases()
  {
    return $this->hasMany(SessionPurchase::class);
  }

  public function importantProducts()
  {
    return $this->hasMany(ImportantProduct::class);
  }

  public function recipe()
  {
    return $this->hasMany(ProductRecipe::class);
  }

  public function ingredients()
  {
    return $this->belongsToMany(
      Ingredient::class,
      'product_recipes'
    )->withPivot([
      'amount',
      'unit_id',
    ]);
  }

  public function transactions()
  {
    return $this->hasMany(EmployeeTransaction::class);
  }

  /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

  public function scopeAvailable($query)
  {
    return $query->where('is_available', true);
  }

  public function scopeFeatured($query)
  {
    return $query->where('is_featured', true);
  }

  /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

  public function getImageUrlAttribute()
  {
    if (!$this->image) {
      return null;
    }

    return asset('storage/' . $this->image);
  }

  public function getProfitAttribute()
  {
    return $this->price - $this->cost;
  }

  public function getIsLowStockAttribute()
  {
    return $this->quantity <= $this->min_quantity;
  }
}
