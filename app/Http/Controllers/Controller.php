<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Flotas API",
 *     version="1.0.0",
 *     description="API para gestión de flotas vehiculares"
 * )
 *
 * @OA\Server(
 *     url="/api/v1",
 *     description="Servidor local"
 * )
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
