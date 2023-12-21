<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_mailer_development" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Ssch\T3MailerDevelopment\DependencyInjection\Compiler;

use Ssch\T3MailerDevelopment\EventListener\EnvelopeListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class MailerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $envelopeListener = $container->getDefinition(EnvelopeListener::class);
        $envelopeListener->setArgument(0, 'sender@domain.com');
        $envelopeListener->setArgument(1, ['catchall@domain.com']);
    }
}
