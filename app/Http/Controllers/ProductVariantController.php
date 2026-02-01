<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{


    public function index()
    {
        $variants = ProductVariant::with(['product', 'color', 'size'])->get();
        return view('variants.index', compact('variants'));
    }
    public function create(Product $product)
    {
        return view('variants.create', [
            'product'   => $product,
            'colors'    => Color::all(),
            'sizes'     => Size::all(),
        ]);
    }


    public function store(Request $request, Product $product)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'cost'  => 'required|numeric|min:0',
        ]);

        // 🎨 اللون
        if ($request->filled('new_color')) {
            $color = Color::create([
                'name' => $request->new_color
            ]);
        } else {
            $color = Color::findOrFail($request->color_id);
        }

        // 📐 المقاسات
        $sizeIds = $request->sizes ?? [];

        if ($request->filled('new_sizes')) {
            foreach (explode(',', $request->new_sizes) as $name) {
                $size = Size::create([
                    'name' => trim($name)
                ]);
                $sizeIds[] = $size->id;
            }
        }

        // 🧩 إنشاء Variants + السعر
        foreach ($sizeIds as $sizeId) {
            ProductVariant::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'color_id'   => $color->id,
                    'size_id'    => $sizeId,
                ],
                [
                    'price' => $request->price,
                    'cost'  => $request->cost,
                ]
            );
        }

        return redirect()
            ->route('products.show', $product->id)
            ->with('success', 'تمت إضافة اللون والمقاسات والسعر بنجاح ✅');
    }
    public function edit(ProductVariant $variant)
    {
        return view('variants.edit', [
            'variant'  => $variant,
            'products' => Product::all(),
            'colors'   => Color::all(),
            'sizes'    => Size::all(),
        ]);
    }

    public function update(Request $request, ProductVariant $variant)
    {
        $data = $request->validate([
            'price'        => 'required|numeric|min:0',
            'cost'         => 'required|numeric|min:0',
            'quantity'     => 'required|integer|min:0',
            'min_quantity' => 'required|integer|min:0',
        ]);

        $variant->update($data);

        return redirect()->back()->with('success', 'تم التحديث بنجاح ✅');
    }
    public function variants(Product $product)
    {
        return $product->variants()
            ->with(['color', 'size', 'stocks'])
            ->get()
            ->map(function ($v) {
                return [
                    'variant_id' => $v->id,
                    'color' => $v->color->name,
                    'size'  => $v->size->name,
                    'stocks' => $v->stocks->map(fn($s) => [
                        'is_printed' => $s->is_printed,
                        'qty'        => $s->quantity,
                        'price'      => $s->price,
                    ])
                ];
            });
    }

    public function addStock(Request $request, ProductVariant $variant)
    {
        $data = $request->validate([
            'quantity'   => 'required|integer|min:1',
            'is_printed' => 'required|boolean',
        ]);

        $stock = $variant->stocks()
            ->firstOrCreate(
                ['is_printed' => $data['is_printed']],
                ['quantity' => 0]
            );

        $stock->increment('quantity', $data['quantity']);

        return back()->with('success', 'تمت إضافة الكمية بنجاح ✅');
    }
    public function destroy(ProductVariant $variant)
    {
        $variant->delete();
        return redirect()->back()->with('success', 'تم الحذف بنجاح 🗑️');
    }
}
