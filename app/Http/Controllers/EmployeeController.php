<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{

    public function index()
    {
        $employees = Employee::with('user')->latest()->paginate(20);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $users = User::all();
        return view('employees.create', compact('users'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'phone' => 'nullable',
            'salary' => 'required|numeric',
            'user_id' => 'nullable|exists:users,id'
        ]);

        Employee::create($request->all());

        return redirect()
            ->route('employees.index')
            ->with('success', 'تم إضافة الموظف');
    }

    // داخل EmployeeController.php
    public function show($id)
    {
        $employee = Employee::with(['transactions' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }, 'user'])->findOrFail($id);

        // تجميع المسحوبات حسب الشهر للسجل التاريخي
        $history = $employee->transactions->groupBy(function ($date) {
            return \Carbon\Carbon::parse($date->created_at)->format('Y-m');
        });

        // بيانات الشهر الحالي فقط
        $currentMonth = now()->format('Y-m');
        $currentTransactions = $employee->transactions->filter(function ($t) use ($currentMonth) {
            return $t->created_at->format('Y-m') === $currentMonth;
        });

        $totalAdvances = $currentTransactions->where('type', 'advance')->sum('amount');
        $totalPurchases = $currentTransactions->where('type', 'purchase')->sum('amount');
        $netSalary = $employee->salary - ($totalAdvances + $totalPurchases);

        return view('employees.show', compact('employee', 'history', 'totalAdvances', 'totalPurchases', 'netSalary', 'currentTransactions'));
    }

    public function edit(Employee $employee)
    {
        $users = Employee::pluck('name', 'id');

        return view('employees.edit', compact('employee', 'users'));
    }

    public function update(Request $request, Employee $employee)
    {

        $request->validate([
            'name' => 'required',
            'phone' => 'nullable',
            'salary' => 'required|numeric',
            'user_id' => 'nullable|exists:users,id'
        ]);

        $employee->update($request->all());

        return redirect()
            ->route('employees.index')
            ->with('success', 'تم تحديث الموظف');
    }

    public function destroy(Employee $employee)
    {

        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', 'تم حذف الموظف');
    }
}
