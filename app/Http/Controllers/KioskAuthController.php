<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class KioskAuthController extends Controller
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
}
