<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash};

class AuthController extends Controller
{
    private const ROLE = 'admin';

    public function showLogin() { return view('auth.login'); }

    public function login(Request $r)
    {
        $cred = $r->validate(['email' => 'required|email', 'password' => 'required']);
        if (Auth::attempt($cred, $r->boolean('remember'))) {
            if (Auth::user()->role !== self::ROLE) {
                Auth::logout();
                $r->session()->invalidate();
                $r->session()->regenerateToken();
                return back()->withErrors(['email' => 'This portal is only for ' . self::ROLE . ' accounts.'])->onlyInput('email');
            }
            $r->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }
        return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showPassword() { return view('auth.password'); }

    public function updatePassword(Request $r)
    {
        $r->validate(['current_password' => 'required|current_password', 'password' => 'required|string|min:8|confirmed']);
        $r->user()->update(['password' => $r->password]);
        return back()->with('ok', 'Password changed.');
    }
}
