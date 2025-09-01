<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/admin/posts';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

     protected function authenticated(Request $request, $user)
    {
        if ($user->isAdmin()) {
            return redirect()->intended('/admin/dashboard');
        } elseif ($user->isEditor()) {
            return redirect()->intended('/admin/editor-dashboard');
        }

        return redirect()->intended('/');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.password.forgot-password');
    }
}
