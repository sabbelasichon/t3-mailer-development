<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_mailer_development" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Ssch\T3MailerDevelopment\EventListener\EnvelopeListener;
use Ssch\T3MailerDevelopment\EventListener\MessageLoggerListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\Core\Mail\Event\AfterMailerSentMessageEvent;
use TYPO3\CMS\Core\Mail\Event\BeforeMailerSentMessageEvent;

return static function (ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void {
    $services = $containerConfigurator->services();
    $services->defaults()
        ->private()
        ->autowire()
        ->autoconfigure();

    $services->load('Ssch\\T3MailerDevelopment\\', __DIR__ . '/../Classes/');

    $services->set(MessageLoggerListener::class)->tag('event.listener', [
        'event' => AfterMailerSentMessageEvent::class,
    ]);
    $services->set(EnvelopeListener::class)->tag('event.listener', [
        'event' => BeforeMailerSentMessageEvent::class,
    ]);
};
