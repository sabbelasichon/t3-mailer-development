<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_mailer_development" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Ssch\T3MailerDevelopment\Domain\Configuration;

use TYPO3\CMS\Core\Utility\GeneralUtility;

final class MailerConfiguration
{
    public function __construct(
        private readonly ?string $sender = null,
        private readonly ?array $recipients = null,
        private readonly ?array $whitelistRecipients = null
    ) {
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function getRecipients(): ?array
    {
        return $this->recipients;
    }

    public function getWhitelistRecipients(): ?array
    {
        return $this->whitelistRecipients;
    }

    public static function fromArray(mixed $mailerConfiguration): self
    {
        $recipients = $mailerConfiguration['recipients'] ?? null;
        if (is_string($recipients)) {
            $recipients = GeneralUtility::trimExplode(',', $recipients, true);
        }

        $whitelistRecipients = $mailerConfiguration['whiteListRecipients'] ?? null;
        if (is_string($whitelistRecipients)) {
            $whitelistRecipients = GeneralUtility::trimExplode(',', $whitelistRecipients, true);
        }

        return new self($mailerConfiguration['sender'] ?? null, $recipients, $whitelistRecipients);
    }
}
