<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ParentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Vérifier si l'utilisateur est un parent
        if ($user->role !== 'parent') {
            abort(403, 'Accès réservé aux parents.');
        }

        // Vérifier si le parent est actif
        if ($user->parentProfile && !$user->parentProfile->actif) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Votre compte parent a été désactivé.');
        }

        return $next($request);
    }
}
