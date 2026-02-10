<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    /**
     * Connexion utilisateur
     * 
     * @OA\Post(
     *     path="/login",
     *     tags={"Authentification"},
     *     summary="Connexion utilisateur",
     *     description="Authentifie un utilisateur et retourne un token d'accès",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@centresport.ml"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="1|abc123..."),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Identifiants incorrects"),
     *     @OA\Response(response=429, description="Trop de tentatives")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        // Révoquer les anciens tokens
        $user->tokens()->delete();

        // Créer un nouveau token
        $token = $user->createToken('mobile-app')->plainTextToken;

        // Déterminer le type de portail et les permissions
        $portail = $this->determinerPortail($user);

        return response()->json([
            'token' => $token,
            'user' => $this->formatUser($user),
            'portail' => $portail,
        ]);
    }

    /**
     * Connexion athlète uniquement
     */
    public function loginAthlete(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        if (!$user->isAthlete()) {
            throw ValidationException::withMessages([
                'email' => ['Ce compte n\'est pas un compte athlète.'],
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('mobile-athlete')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->formatUser($user),
            'portail' => 'athlete',
        ]);
    }

    /**
     * Connexion parent uniquement
     */
    public function loginParent(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        if (!$user->isParent()) {
            throw ValidationException::withMessages([
                'email' => ['Ce compte n\'est pas un compte parent.'],
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('mobile-parent')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->formatUser($user),
            'portail' => 'parent',
        ]);
    }

    /**
     * Connexion admin/coach uniquement
     */
    public function loginStaff(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        if (!$user->isAdmin() && !$user->isCoach()) {
            throw ValidationException::withMessages([
                'email' => ['Ce compte n\'a pas accès à l\'espace staff.'],
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('mobile-staff')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->formatUser($user),
            'portail' => $user->isAdmin() ? 'admin' : 'coach',
        ]);
    }

    /**
     * Déterminer le portail de l'utilisateur
     */
    private function determinerPortail(User $user): string
    {
        if ($user->isAdmin()) {
            return 'admin';
        }
        if ($user->isCoach()) {
            return 'coach';
        }
        if ($user->isAthlete()) {
            return 'athlete';
        }
        if ($user->isParent()) {
            return 'parent';
        }
        return 'unknown';
    }

    /**
     * Déconnexion
     * 
     * @OA\Post(
     *     path="/logout",
     *     tags={"Authentification"},
     *     summary="Déconnexion",
     *     description="Révoque le token d'accès actuel",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Déconnexion réussie"),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie',
        ]);
    }

    /**
     * Récupérer l'utilisateur courant
     * 
     * @OA\Get(
     *     path="/user",
     *     tags={"Authentification"},
     *     summary="Utilisateur courant",
     *     description="Retourne les informations de l'utilisateur authentifié",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Informations utilisateur"),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function user(Request $request)
    {
        return response()->json($this->formatUser($request->user()));
    }

    /**
     * Mot de passe oublié
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json([
            'message' => 'Un lien de réinitialisation a été envoyé.',
        ]);
    }

    /**
     * Formater les données utilisateur
     */
    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'athlete_id' => $user->athlete_id,
            'photo' => $user->photo_url,
            'email_verified_at' => $user->email_verified_at?->toISOString(),
            'created_at' => $user->created_at?->toISOString(),
            'updated_at' => $user->updated_at?->toISOString(),
        ];
    }
}
