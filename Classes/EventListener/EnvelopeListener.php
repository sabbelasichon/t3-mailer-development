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
     * @var array<int, string>
     */
    private array $whiteListRecipients = [];

    /**
     * @param array<int, string> $recipients
     * @param array<int, string> $whiteListRecipients
     */
    public function __construct(string $sender = null, array $recipients = null, array $whiteListRecipients = null)
    {
        if ($sender !== null) {
            $this->sender = Address::create($sender);
        }
        if ($recipients !== null) {
            $this->recipients = Address::createArray($recipients);
        }
        if ($whiteListRecipients !== null) {
            $this->whiteListRecipients = $whiteListRecipients;
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

        $allowedRecipients = [];
        $notAllowedRecipients = [];
        if ($this->whiteListRecipients !== []) {
            foreach ($envelope->getRecipients() as $recipient) {
                foreach ($this->whiteListRecipients as $whiteListRecipient) {
                    if (preg_match($whiteListRecipient, $recipient->getAddress()) === 1) {
                        $allowedRecipients[] = $recipient;
                    } else {
                        $notAllowedRecipients[] = $recipient;
                    }
                }
            }
        }

        $recipients = [];
        if ($notAllowedRecipients !== [] && $this->recipients !== null) {
            $recipients = array_merge($recipients, $this->recipients);
        }

        if ($allowedRecipients !== []) {
            $recipients = array_merge($recipients, $allowedRecipients);
        }

        if ($recipients !== []) {
            $envelope->setRecipients($recipients);
        } elseif ($this->recipients !== null) {
            $envelope->setRecipients($this->recipients);
        }

        $event->setMessage($message);
        $event->setEnvelope($envelope);
    }
}
