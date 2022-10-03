<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException($event): void
    {
        // ...
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Kernel.exception' => 'onKernelException',
        ];
    }
}
