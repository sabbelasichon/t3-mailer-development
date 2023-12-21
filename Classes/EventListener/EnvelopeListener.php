<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_mailer_development" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Ssch\T3MailerDevelopment\EventListener;

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
     * @param array<Address|string> $recipients
     */
    public function __construct(Address|string $sender = null, array $recipients = null)
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
        if ($this->sender !== null) {
            $event->getEnvelope()
                ->setSender($this->sender);

            $message = $event->getMessage();
            if ($message instanceof Message) {
                if (! $message->getHeaders()->has('Sender') && ! $message->getHeaders()->has('From')) {
                    $message->getHeaders()
                        ->addMailboxHeader('Sender', $this->sender);
                }
            }
        }

        if ($this->recipients !== null) {
            $event->getEnvelope()
                ->setRecipients($this->recipients);
        }
    }
}