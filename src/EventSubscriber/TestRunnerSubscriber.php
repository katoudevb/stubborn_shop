<?php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Service\TestRunnerService;

class TestRunnerSubscriber implements EventSubscriberInterface
{
    private $testRunner;
    private static $testsRun = false;

    public function __construct(TestRunnerService $testRunner)
    {
        $this->testRunner = $testRunner;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (self::$testsRun) return;

        self::$testsRun = true;

        $this->testRunner->runTests();
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
