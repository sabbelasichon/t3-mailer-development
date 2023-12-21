<?php
declare(strict_types=1);

use Ssch\T3MailerDevelopment\EventListener\MessageLoggerListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\Core\Mail\Event\AfterMailerSentMessageEvent;

return static function (ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void {
    $services = $containerConfigurator->services();
    $services->defaults()
        ->private()
        ->autowire()
        ->autoconfigure();

    $services->load('Ssch\\T3MailerDevelopment\\', __DIR__ . '/../Classes/');

    $services->set(MessageLoggerListener::class)->tag('event.listener', [
        'event' => AfterMailerSentMessageEvent::class
    ]);
};
