<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)
            ->orWhere('username', $request->email)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Credentials do not match.']);
        }

        if (!$user->hasRole('Super Admin') && !$user->project_id) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'You do not have any role. Please contact Super Admin.']);
        }

        if ($request->lang) {
            $user->locale = $request->lang;
            $user->save();
            App::setLocale($request->locale);
        }

        Auth::login($user);
        $request->session()->regenerate();

        activity('login')
            ->causedBy($user)
            ->event('login')
            ->withProperties(['ip' => $request->ip()])
            ->log('login');

        if ($user->hasRole('Super Admin')) {
            return to_route('projects');
        }

        return to_route('projects.dashboard', $user->project_id);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        Auth::logout();
        $request->session()->regenerate();

        activity('logout')
            ->causedBy($user)
            ->event('logout')
            ->withProperties(['ip' => $request->ip()])
            ->log('logout');

        return to_route('login.form');
    }
}
