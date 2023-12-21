<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_mailer_development" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Ssch\T3MailerDevelopment\Domain\Configuration\MailerConfiguration;
use Ssch\T3MailerDevelopment\EventListener\EnvelopeListener;
use Ssch\T3MailerDevelopment\EventListener\MessageLoggerListener;
use Ssch\T3MailerDevelopment\Factory\MailerConfigurationFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\Core\Mail\Event\AfterMailerSentMessageEvent;
use TYPO3\CMS\Core\Mail\Event\BeforeMailerSentMessageEvent;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->defaults()
        ->private()
        ->autowire()
        ->autoconfigure();

    $services->load('Ssch\\T3MailerDevelopment\\', __DIR__ . '/../Classes/')->exclude([
        __DIR__ . '/../Classes/Domain/Configuration',
    ]);

    $services->set('t3_mailer_development.configuration', MailerConfiguration::class)
        ->factory([service(MailerConfigurationFactory::class), 'create']);

    $services->set(MessageLoggerListener::class)->tag('event.listener', [
        'event' => AfterMailerSentMessageEvent::class,
    ]);
    $services->set(EnvelopeListener::class)
        ->args([
            expr("service('t3_mailer_development.configuration').getSender()"),
            expr("service('t3_mailer_development.configuration').getRecipients()"),
        ])
        ->tag('event.listener', [
            'event' => BeforeMailerSentMessageEvent::class,
        ]);
};
