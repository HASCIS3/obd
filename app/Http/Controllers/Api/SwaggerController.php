<?php

namespace App\Http\Controllers\Api;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="OBD - API Centre Sportif",
 *     description="API REST pour la gestion du centre sportif OBD (Olympique de Bamako Développement)",
 *     @OA\Contact(
 *         email="admin@centresport.ml",
 *         name="Support OBD"
 *     ),
 *     @OA\License(
 *         name="Propriétaire",
 *         url="https://obd.ml"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="/api",
 *     description="Serveur API principal"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Token d'authentification Sanctum. Obtenu via POST /api/login"
 * )
 * 
 * @OA\Tag(name="Authentification", description="Endpoints d'authentification")
 * @OA\Tag(name="Dashboard", description="Statistiques et tableau de bord")
 * @OA\Tag(name="Athlètes", description="Gestion des athlètes")
 * @OA\Tag(name="Disciplines", description="Gestion des disciplines sportives")
 * @OA\Tag(name="Paiements", description="Gestion des paiements et arriérés")
 * @OA\Tag(name="Présences", description="Suivi des présences")
 * @OA\Tag(name="Performances", description="Évaluations et performances sportives")
 */
class SwaggerController
{
}
