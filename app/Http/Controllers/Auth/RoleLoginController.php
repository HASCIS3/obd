<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RoleLoginController extends Controller
{
    /**
     * Affiche la page de sélection du portail
     */
    public function showSelect(): View
    {
        return view('auth.login-select');
    }

    /**
     * Affiche le formulaire de login staff (admin/coach)
     */
    public function showStaffLogin(): View
    {
        return view('auth.login-staff');
    }

    /**
     * Traite le login staff
     */
    public function loginStaff(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'Les identifiants sont incorrects.',
            ])->withInput($request->only('email'));
        }

        if (!$user->isAdmin() && !$user->isCoach()) {
            return back()->withErrors([
                'email' => 'Ce compte n\'a pas accès à l\'espace administration.',
            ])->withInput($request->only('email'));
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Affiche le formulaire de login athlète
     */
    public function showAthleteLogin(): View
    {
        return view('auth.login-athlete');
    }

    /**
     * Traite le login athlète
     */
    public function loginAthlete(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'Les identifiants sont incorrects.',
            ])->withInput($request->only('email'));
        }

        if (!$user->isAthlete()) {
            return back()->withErrors([
                'email' => 'Ce compte n\'est pas un compte athlète.',
            ])->withInput($request->only('email'));
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('athlete.dashboard'));
    }

    /**
     * Affiche le formulaire de login parent
     */
    public function showParentLogin(): View
    {
        return view('auth.login-parent');
    }

    /**
     * Traite le login parent
     */
    public function loginParent(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'Les identifiants sont incorrects.',
            ])->withInput($request->only('email'));
        }

        if (!$user->isParent()) {
            return back()->withErrors([
                'email' => 'Ce compte n\'est pas un compte parent.',
            ])->withInput($request->only('email'));
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('parent.dashboard'));
    }
}
