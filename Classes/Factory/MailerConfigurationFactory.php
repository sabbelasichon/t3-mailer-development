<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_mailer_development" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Ssch\T3MailerDevelopment\Factory;

use Ssch\T3MailerDevelopment\Domain\Configuration\MailerConfiguration;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

final class MailerConfigurationFactory
{
    public function __construct(
        private readonly ExtensionConfiguration $extensionConfiguration
    ) {
    }

    public function create(): MailerConfiguration
    {
        try {
            $mailerConfiguration = $this->extensionConfiguration->get('t3_mailer_development');
        } catch (ExtensionConfigurationExtensionNotConfiguredException) {
            $mailerConfiguration = [];
        }

        return MailerConfiguration::fromArray($mailerConfiguration);
    }
}
