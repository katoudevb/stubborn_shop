<?php

namespace App\Service;

use PHPUnit\TextUI\Application;

class TestRunnerService
{
    private Application $application;

    public function __construct()
    {
        $this->application = new Application();
    }

    /**
     * Exécute un ou plusieurs tests.
     * 
     * @param string[] $testClasses Facultatif. Si vide, détecte tous les tests.
     */
    public function runTests(array $testClasses = []): void
    {
        // Si aucun test passé, détecte tous les *Test.php dans tests/
        if (empty($testClasses)) {
            $testClasses = glob(__DIR__ . '/../../tests/*Test.php');
        }

        if (empty($testClasses)) {
            echo "Aucun test trouvé." . PHP_EOL;
            return;
        }

        // Préparer les arguments pour PHPUnit
        $args = array_merge(['phpunit'], $testClasses);

        // Exécuter PHPUnit
        $this->application->run($args);
    }
}
