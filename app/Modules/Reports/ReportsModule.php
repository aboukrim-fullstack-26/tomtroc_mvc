<?php
namespace App\Modules\Reports;

use App\Core\Router;

/**
 * Module Reports (Signalements)
 * - GET  /mes-signalements
 * - POST /signalement/livre
 */
final class ReportsModule
{
    public static function register(Router $router): void
    {
        $router->get('/mes-signalements', [\App\Modules\Reports\Controllers\ReportController::class, 'index']);
        $router->post('/signalement/livre', [\App\Modules\Reports\Controllers\ReportController::class, 'reportBook']);
    }
}
