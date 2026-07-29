<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminPasswordConfirmController extends Controller
{
    public function show()
    {
        return view('admin.password-confirm');
    }

    public function store(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ], [
            'password.required' => '비밀번호를 입력해 주세요.',
        ]);

        if (! Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => '비밀번호가 올바르지 않습니다.']);
        }

        session(['admin_password_confirmed_at' => now()->timestamp]);

        return redirect()->intended('/admin');
    }
}
