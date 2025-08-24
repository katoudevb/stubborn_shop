#!/usr/bin/env php
<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Service\TestRunnerService;

echo "=== Lancement des tests ===" . PHP_EOL;

$runnerService = new TestRunnerService();

try {
    // On peut appeler sans argument, les tests seront détectés automatiquement
    $runnerService->runTests();

} catch (\Throwable $e) {
    echo "Erreur lors de l'exécution des tests : " . $e->getMessage() . PHP_EOL;
    exit(1);
}

echo PHP_EOL . "Lancement de l'application..." . PHP_EOL;
