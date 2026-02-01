<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\ImportantProduct;
use App\Models\Product;
use App\Models\Size;
use App\Models\VariantStock;
use DB;
use Illuminate\Http\Request;

class ProductController extends Controller
{

public function updateName(Request $request, Product $product)
{
    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    $product->update([
        'name' => $request->name
    ]);

    return redirect()->back()->with('success', 'تم تحديث اسم المنتج بنجاح');
}
  public function index()
  {
    $products = Product::with('variants')->get();
    $countProducts = $products->count();

    return view('products.index', compact('products', 'countProducts'));
  }

  public function show(Product $product)
  {
    $product->load([
      'variants.color',
      'variants.size',
      'variants.stocks'
    ]);

    // grouping جاهز للـ UI
    $colors = $product->variants->groupBy('color_id');

    return view('products.show', [
      'product'   => $product,
      'colors'    => $colors,
      'allColors' => Color::all(),
      'allSizes'  => Size::all(),
    ]);
  }





  public function createImportant()
  {

    $importantProducts = ImportantProduct::with('product')->latest()->paginate(20);
    // $products not mandatory here (we use AJAX search), لكن لو بدك تمرر:
    // $products = \App\Models\Product::take(30)->get();

    return view('managment.changes.important_products.create', compact('importantProducts'));
  }
  public function storeImportant(Request $request)
  {
    $data = $request->validate([
      'name' => 'required|string|max:255',
      'product_id' => 'required|exists:products,id',
    ]);

    // التحقق إذا الاسم موجود بالفعل
    $nameExists = ImportantProduct::where('name', $data['name'])->exists();
    if ($nameExists) {
      return redirect()->back()->with('error', 'هذا الاسم مرتبط بمنتج مهم بالفعل.');
    }

    // التحقق إذا المنتج مرتبط بالفعل
    $productExists = ImportantProduct::where('product_id', $data['product_id'])->exists();
    if ($productExists) {
      return redirect()->back()->with('error', 'هذا المنتج موجود بالفعل كمنتج مهم.');
    }

    // إنشاء المنتج المهم
    ImportantProduct::create([
      'product_id' => $data['product_id'],
      'name' => $data['name'],
    ]);

    return redirect()->back()->with('success', 'تم حفظ المنتج المهم بنجاح.');
  }
  public function updateImportant(Request $request, ImportantProduct $importantProduct)
  {
    $data = $request->validate([
      'name' => 'required|string|max:255',
      'product_id' => 'nullable|exists:products,id',
    ]);

    // تحقق من الاسم المكرر (باستثناء نفسه)
    $existsByName = ImportantProduct::where('name', $data['name'])
      ->where('id', '!=', $importantProduct->id)
      ->exists();
    if ($existsByName) {
      return redirect()->back()->with('error', 'هذا الاسم مستخدم بالفعل.');
    }

    // تحقق من المنتج المرتبط (باستثناء نفسه)
    if (!empty($data['product_id'])) {
      $existsByProduct = ImportantProduct::where('product_id', $data['product_id'])
        ->where('id', '!=', $importantProduct->id)
        ->exists();
      if ($existsByProduct) {
        return redirect()->back()->with('error', 'هذا المنتج مرتبط بالفعل بمنتج مهم آخر.');
      }
    }

    // التحديث
    $importantProduct->update([
      'name' => $data['name'],
      'product_id' => $data['product_id'] ?? $importantProduct->product_id,
    ]);

    return redirect()->back()->with('success', 'تم تحديث المنتج المهم بنجاح.');
  }
  public function update(Request $request, Product $product)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'price' => 'required|numeric|min:0',
      'cost' => 'required|numeric|min:0',
      'quantity' => 'required|integer|min:0',
    ]);

    $product->update($validated);

    return response()->json(['status' => 'success', 'message' => 'Product updated successfully']);
  }


  public function addQuantityPage()
  {
    return view('products.add-quantity');
  }
  public function store(Request $request)
  {
    // 1️⃣ Validation كامل
    $request->validate([
      'name'         => 'required|string|max:255',
    ]);



    // 3️⃣ تحقق من تكرار الاسم
    if (Product::where('name', $request->name)->exists()) {
      return redirect()->back()->with('error', "هذا المنتج موجود في المخزن ❕");
    }

    // 4️⃣ إدخال البيانات مع timestamps
    Product::create([
      'name'         => $request->name,
      'created_at'   => now(),
      'updated_at'   => now(),
    ]);

    return redirect()->back()->with('success', 'تمت إضافة المنتج بنجاح ✅');
  }

  public function addQuantity(Request $request, $id)
  {
    $request->validate([
      'quantity' => 'required|integer|min:1',
    ]);

    $product = Product::findOrFail($id);
    $product->quantity += $request->quantity;
    $product->save();

    return redirect()->route('products.index')->with('success', 'تمت إضافة الكمية بنجاح ✅');
  }
  public function searchId(Request $request)
  {
    $code = trim($request->get('query'));

    if (!$code || strlen($code) < 5) {
      return response()->json([
        'status' => 'error',
        'message' => 'كود غير صالح'
      ], 422);
    }

    $products = Product::where('id', $code)
      ->select('id', 'name', 'price', 'cost', 'quantity')
      ->limit(2)
      ->get();

    if ($products->count() === 1) {
      return response()->json([
        'status' => 'success',
        'type' => 'single',
        'product' => $products->first()
      ]);
    }

    if ($products->count() > 1) {
      return response()->json([
        'status' => 'warning',
        'type' => 'multiple',
        'products' => $products
      ]);
    }

    return response()->json([
      'status' => 'not_found',
      'message' => 'المنتج غير موجود'
    ], 404);
  }


  public function colors(Product $product)
  {
    return Color::whereHas('variants', function ($q) use ($product) {
      $q->where('product_id', $product->id);
    })->get();
  }

  public function sizes(Request $request)
  {
    return Size::whereHas('variants', function ($q) use ($request) {
      $q->where([
        'product_id' => $request->product_id,
        'color_id'   => $request->color_id,
      ]);
    })->get();
  }

  public function stock(Request $request)
  {
    return VariantStock::whereHas('variant', function ($q) use ($request) {
      $q->where([
        'product_id' => $request->product_id,
        'color_id'   => $request->color_id,
        'size_id'    => $request->size_id,
      ]);
    })->get();
  }
  public function search(Request $request)
  {
    $query = $request->get('query');
    $products = Product::where('name', 'LIKE', "%{$query}%")
      ->with(['variants.stocks']) // مهم للأداء
      ->get()
      ->map(function ($product) {
        return [
          'id' => $product->id,
          'name' => $product->name,
          'total_quantity' => $product->variants->flatMap->stocks->sum('quantity'),
          'colors_count' => $product->variants->groupBy('color_id')->count(),
        ];
      });
    return response()->json($products);
  }
}
