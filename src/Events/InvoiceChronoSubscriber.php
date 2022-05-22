<?php

namespace App\Events;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class InvoiceChronoSubscriber implements EventSubscriberInterface
{
    /* @var Security */
    private $security;

    /* @var Security */
    private $repository;

    public function __construct(Security $security, InvoiceRepository $repository)
    {
        $this->security = $security;
        $this->repository = $repository;
    }


    // Subscribing to a pre validate event (invoice creation for instance)
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [
                'setChronoAndSentAtForInvoice', EventPriorities::PRE_VALIDATE
            ]
        ];
    }

    // Function to use kernel.view event to add an chrono if Invoice POST
    public function setChronoAndSentAtForInvoice(ViewEvent $event)
    {
        $invoice = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($invoice instanceof Invoice && $method === "POST") {
            // Get the current user
            $user = $this->security->getUser();

            // Assign a new chrono to the current invoice
            $nextChrono = $this->repository->findNextChrono($this->security->getUser());
            $invoice->setChrono($nextChrono);

            // Assign a new sent at if blank
            if (empty($invoice->getSentAt())) {
                $invoice->setSentAt(new \DateTime());
            }
        }
    }
}
