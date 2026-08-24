<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['username' => ['required', 'string'], 'password' => ['required', 'string']]);
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }
        return back()->withErrors(['username' => 'Those credentials do not match our records.'])->onlyInput('username');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'church_name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:80', 'unique:users,username'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);
        $user = User::create([
            'name' => $validated['name'],
            'church_name' => $validated['church_name'],
            'username' => $validated['username'],
            'email' => $validated['username'] . '@local.ichords',
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
        ]);
        Auth::login($user);
        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
