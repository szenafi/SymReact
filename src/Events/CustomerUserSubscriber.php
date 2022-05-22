<?php

namespace App\Events;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Customer;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class CustomerUserSubscriber implements EventSubscriberInterface
{
    /* @var Security */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    // Subscribing to a pre validate event (customer creation for instance)
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [
                'setUserForCustomer', EventPriorities::PRE_VALIDATE
            ]
        ];
    }

    // Function to use kernel.view event to add a user if Customer POST
    public function setUserForCustomer(ViewEvent $event)
    {
        $customer = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($customer instanceof Customer && $method === "POST") {
            // Get the current user
            $user = $this->security->getUser();
            // Assign to a customer the user
            $customer->setUser($user);
        }
    }
}
