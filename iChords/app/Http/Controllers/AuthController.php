<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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

    public function settings()
    {
        return view('settings', [
            'user' => Auth::user(),
            'leaders' => Auth::user()->songLeaders()->withCount('songs')->orderBy('name')->get(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'church_name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:80', Rule::unique('users')->ignore(Auth::id())],
        ]);
        $user = Auth::user();
        $user->update($validated + ['email' => $validated['username'] . '@local.ichords']);

        return back()->with('success', 'Your profile was updated.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);
        Auth::user()->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Your password was changed.');
    }

    public function deleteAccount(Request $request)
    {
        $validated = $request->validate([
            'church_name_confirmation' => ['required', 'string'],
        ]);
        $user = Auth::user();
        if ($validated['church_name_confirmation'] !== $user->church_name) {
            return back()->withErrors(['church_name_confirmation' => 'The church name does not match.']);
        }
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Your account was deleted.');
    }
}
