<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Process\Process;

class StartupTestSubscriber implements EventSubscriberInterface
{
    private bool $alreadyRun = false;

    public function onKernelRequest(RequestEvent $event)
    {
        if ($this->alreadyRun) {
            return;
        }
        $this->alreadyRun = true;

        // Lancement des tests en arrière-plan
        $process = new Process(['php', 'bin/console', 'app:run-startup-tests']);
        $process->start(); // start() exécute en parallèle sans bloquer

        // On peut éventuellement loguer l’info
        file_put_contents(
            __DIR__ . '/../../var/log/startup-tests.log',
            "Tests démarrés le " . date('Y-m-d H:i:s') . "\n",
            FILE_APPEND
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}
