<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_mailer_development" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Ssch\T3MailerDevelopment\EventListener;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;
use TYPO3\CMS\Core\Mail\Event\BeforeMailerSentMessageEvent;

final class EnvelopeListener
{
    private ?Address $sender = null;

    /**
     * @var Address[]|null
     */
    private ?array $recipients = null;

    /**
     * @param array<int, string> $recipients
     */
    public function __construct(string $sender = null, array $recipients = null)
    {
        if ($sender !== null) {
            $this->sender = Address::create($sender);
        }
        if ($recipients !== null) {
            $this->recipients = Address::createArray($recipients);
        }
    }

    public function __invoke(BeforeMailerSentMessageEvent $event): void
    {
        $envelope = $event->getEnvelope() ?? Envelope::create($event->getMessage());
        $message = $event->getMessage();
        if ($this->sender !== null) {
            $envelope->setSender($this->sender);

            if ($message instanceof Message) {
                if (! $message->getHeaders()->has('Sender') && ! $message->getHeaders()->has('From')) {
                    $message->getHeaders()
                        ->addMailboxHeader('Sender', $this->sender);
                }
            }
        }

        if ($this->recipients !== null) {
            $envelope->setRecipients($this->recipients);
        }

        $event->setMessage($message);
        $event->setEnvelope($envelope);
    }
}
