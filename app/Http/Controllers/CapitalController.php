<?php

namespace App\Http\Controllers;

use App\Models\Capital;
use App\Models\ExpenseType;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CapitalController extends Controller
{

    public function index()
    {
        // إجمالي رأس المال
        $totalCapital = Transaction::where('type', 'in')->sum('amount')
            - Transaction::where('type', 'out')->sum('amount');

        // الحركات
        $transactions = Transaction::with('expense.type')
            ->orderBy('created_at', 'desc')
            ->get();

        $expenseTypes = ExpenseType::orderBy('name')->get();

        return view('dashboard.capitals.index', compact(
            'totalCapital',
            'transactions',
            'expenseTypes'
        ));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        return Capital::create($data);
    }

    public function destroy($id)
    {
        $capital = Capital::findOrFail($id);
        $capital->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
