<?php
declare(strict_types=1);

namespace Ssch\T3MailerDevelopment\EventListener;

use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mailer\Event\MessageEvents;
use TYPO3\CMS\Core\Mail\DelayedTransportInterface;
use TYPO3\CMS\Core\Mail\Event\AfterMailerSentMessageEvent;
use TYPO3\CMS\Core\Mail\Event\BeforeMailerSentMessageEvent;
use TYPO3\CMS\Core\Mail\Mailer;

final class MessageLoggerListener
{
    private MessageEvents $events;

    public function __construct()
    {
        $this->events = new MessageEvents();
    }

    public function __invoke(AfterMailerSentMessageEvent $event): void
    {
        $mailer = $event->getMailer();

        if(!$mailer instanceof Mailer) {
            return;
        }

        $sentMessage = $mailer->getSentMessage();

        if($sentMessage === null) {
            return;
        }

        $transport = $mailer->getTransport();

        $queued = false;
        if($transport instanceof DelayedTransportInterface) {
            $queued = true;
        }

        $this->events->add(new MessageEvent($sentMessage->getOriginalMessage(), $sentMessage->getEnvelope(), (string)$mailer->getTransport(), $queued));
    }

    public function getEvents(): MessageEvents
    {
        return $this->events;
    }
}
