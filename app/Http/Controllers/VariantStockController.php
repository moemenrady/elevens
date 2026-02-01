<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use DB;
use Illuminate\Http\Request;

class VariantStockController extends Controller
{
    public function add(Request $request, ProductVariant $variant)
    {
        $data = $request->validate([
            'quantity'   => 'required|integer|min:1',
            'is_printed' => 'required|boolean',
        ]);

        $stock = $variant->stocks()->firstOrCreate(
            ['is_printed' => $data['is_printed']],
            ['quantity' => 0]
        );

        $stock->increment('quantity', $data['quantity']);

        return back()->with('success', 'تمت إضافة الكمية بنجاح ✅');
    }
}