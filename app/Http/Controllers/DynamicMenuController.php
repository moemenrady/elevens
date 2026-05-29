<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DynamicMenu;
use Illuminate\Http\Request;

class DynamicMenuController extends Controller
{
    public function unlock(Request $request)
    {
        $request->validate([
            'pin' => 'required'
        ]);

        // مثال PIN (لازم تغيره لاحقًا لDB staff auth)
        if ($request->pin !== '1234') {
            return response()->json([
                'success' => false
            ]);
        }

        $uuid = session('kiosk_uuid');

        $kiosk = \App\Models\KioskSession::where('uuid', $uuid)->first();

        if (!$kiosk) {
            return response()->json(['success' => false]);
        }

        $kiosk->update([
            'is_locked' => false
        ]);
        return redirect('/dashboard');
    }
    public function index()
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->with([
                'products' => function ($query) {
                    $query->where('is_available', true)
                        ->orderBy('sort_order');
                }
            ])
            ->orderBy('sort_order')
            ->get();

        return view('layouts.client', compact('categories'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $menu = DynamicMenu::create([
            'order_data' => $request->items
        ]);

        return response()->json([
            'success' => true,
            'menu_id' => $menu->id
        ]);
    }
}
