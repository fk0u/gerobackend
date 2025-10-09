<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Gerobaks REST API",
 *     version="1.0.0",
 *     description="Official REST API specification for the Gerobaks waste management platform.",
 *     @OA\Contact(
 *         name="Gerobaks Engineering",
 *         email="dev@gerobaks.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Local development (php artisan serve)"
 * )
 * 
 * @OA\Server(
 *     url="https://gerobaks.dumeg.com",
 *     description="Production (placeholder)"
 * )
 */
abstract class Controller
{
    //
}
