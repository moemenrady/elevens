<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'color_id',
        'size_id',
        'price',
        'cost',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

public function stocks()
{
    return $this->hasMany(VariantStock::class, 'product_variant_id');
}

    public function printedStock()
    {
        return $this->hasOne(VariantStock::class)
            ->where('is_printed', true);
    }

    public function plainStock()
    {
        return $this->hasOne(VariantStock::class)
            ->where('is_printed', false);
    }

    // 🔥 Helper جاهز للعرض
    public function totalQuantity(): int
    {
        return $this->stocks->sum('quantity');
    }
}

