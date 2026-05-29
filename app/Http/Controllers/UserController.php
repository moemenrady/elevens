<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // عرض صفحة التعديل
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('managment.changes.users.edit', compact('user'));
    }

    // تحديث البيانات
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,user',
            'password' => 'nullable|min:8',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        // تحديث كلمة المرور فقط في حال إدخال قيمة جديدة
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }
    public function toggle(User $user)
{
    if($user->email_verified_at) {
        // الحظر → نجعل التاريخ null
        $user->email_verified_at = null;
    } else {
        // التفعيل → نضع التاريخ الحالي
        $user->email_verified_at = now();
    }

    $user->save();

    $status = $user->email_verified_at ? 'تم تفعيل الحساب' : 'تم تعطيل الحساب';
    return redirect()->back()->with('success', $status);
}
}