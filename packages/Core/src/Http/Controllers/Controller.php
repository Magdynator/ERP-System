<?php

declare(strict_types=1);

namespace Erp\Core\Http\Controllers;

/**
 * @OA\Info(
 *     title="Ultra ERP API Registry",
 *     version="1.0.0",
 *     description="Global modular API documentation for the Production-grade ERP system.",
 *     @OA\Contact(
 *         email="admin@erp.test"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Primary API Gateway"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter token in 'Bearer {token}' format"
 * )
 */
abstract class Controller
{
    //
}
