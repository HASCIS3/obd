<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CoachMiddleware
{
    /**
     * Handle an incoming request.
     * Autorise les coachs ET les admins (les admins ont tous les droits)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Non authentifié.'], 401);
            }
            
            return redirect()->route('login');
        }

        // Les admins et les coachs peuvent accéder
        if (!$request->user()->isAdmin() && !$request->user()->isCoach()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Accès non autorisé.'], 403);
            }
            
            abort(403, 'Accès réservé aux coachs et administrateurs.');
        }

        return $next($request);
    }
}
