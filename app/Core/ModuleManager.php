<?php
/**
 * app/Core/ModuleManager.php
 *
 * Rôle :
 * - Point d’entrée / composant du MVC TomTroc.
 * - Commentaires ajoutés pour faciliter debug & évolutions (V4 stable).
 *
 * Ordre d’exécution (général) :
 * public/index.php → app/bootstrap.php → Router → Controller → Model(s) → View(s)
 *
 * @author aboukrim
 * @date 2026-02-10
 */

namespace App\Core;

final class ModuleManager
{
    private array $modules;

    /**
     * Méthode : __construct()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    public function __construct()
    {
        $this->modules = require __DIR__ . '/../config/modules.php';
    }

    /**
     * Méthode : isEnabled()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    public function isEnabled(string $module): bool
    {
        return !empty($this->modules[$module]);
    }

    /**
     * Méthode : registerRoutes()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    public function registerRoutes(Router $router): void
    {
        foreach ($this->modules as $module => $enabled) {
            if (!$enabled) continue;

            $class = "\\App\\Modules\\{$module}\\{$module}Module";
            if (class_exists($class)) {
                $class::register($router);
            }
        }
    }
}
