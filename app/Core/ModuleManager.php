<?php
/**
 * app/Core/ModuleManager.php
 *
 * Rôle :
 * - Centralise l'activation/désactivation des modules (bundles) TomTroc.
 * - Permet au Router et aux Views (menu/header) de savoir quelles fonctionnalités sont actives.
 *
 * Ordre d'exécution typique :
 * public/index.php → app/bootstrap.php → Router->run()
 *   → Router->defineRoutes() → ModuleManager->registerRoutes($router)
 *   → Controller → Model(s) → View(s)
 *
 * @author @aboukrim
 */

namespace App\Core;

final class ModuleManager
{
    /**
     * @var array<string,bool>  ex: ['Auth'=>true, 'Book'=>true, ...]
     */
    private array $modules;

    public function __construct()
    {
        // Source unique de vérité : app/config/modules.php
        $this->modules = require __DIR__ . '/../config/modules.php';
    }

    /**
     * Indique si un module est activé.
     */
    public function isEnabled(string $module): bool
    {
        return !empty($this->modules[$module]);
    }

    /**
     * Enregistre les routes de tous les modules activés.
     *
     * Convention :
     * - Un module expose soit une méthode statique ::register(Router $router)
     * - Soit une méthode d'instance ->registerRoutes(Router $router)
     */
    public function registerRoutes(Router $router): void
    {
        foreach ($this->modules as $module => $enabled) {
            if (!$enabled) {
                continue;
            }

            $class = "\\App\\Modules\\{$module}\\{$module}Module";

            if (!class_exists($class)) {
                // Module activé mais classe absente : on ignore pour éviter de casser l'app
                continue;
            }

            // On instancie au cas où le module utilise registerRoutes()
            $instance = new $class();

            if (method_exists($instance, 'registerRoutes')) {
                $instance->registerRoutes($router);
                continue;
            }

            if (method_exists($class, 'register')) {
                $class::register($router);
                continue;
            }
        }
    }
}
