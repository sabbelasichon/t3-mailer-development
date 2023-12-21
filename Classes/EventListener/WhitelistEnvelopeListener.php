<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_mailer_development" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Ssch\T3MailerDevelopment\EventListener;

use TYPO3\CMS\Core\Mail\Event\BeforeMailerSentMessageEvent;

final class WhitelistEnvelopeListener
{
    public function __construct(
        private readonly EnvelopeListener $envelopeListener,
        private readonly array $whitelistRecipients
    ) {
    }

    public function __invoke(BeforeMailerSentMessageEvent $event): void
    {
        $matchingRecipients = [];
        if ($this->whitelistRecipients) {
            $recipients = $event->getEnvelope()
                ->getRecipients();

            foreach ($recipients as $recipient) {
                foreach ($this->whitelistRecipients as $whitelistRegex) {
                    if (preg_match($whitelistRegex, $recipient->getAddress())) {
                        $matchingRecipients[] = $recipient;
                    }
                }
            }
        }

        $this->envelopeListener->__invoke($event);

        if ($matchingRecipients !== []) {
            $event->getEnvelope()
                ->setRecipients($matchingRecipients);
        }
    }
}
