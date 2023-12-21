<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_mailer_development" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Ssch\T3MailerDevelopment\Tests\Functional\EventListener;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class EnvelopeListenerTest extends FunctionalTestCase
{
    protected bool $initializeDatabase = false;

    protected array $testExtensionsToLoad = ['typo3conf/ext/t3_mailer_development'];

    protected array $configurationToUseInTestInstance = [
        'MAIL' => [
            'transport' => 'null',
        ],
    ];

    private Mailer $mailer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mailer = $this->get(Mailer::class);
    }

    public function test(): void
    {
        // Arrange
        $message = GeneralUtility::makeInstance(MailMessage::class);
        $message->addTo('max.mustermann@domain.com');
        $message->text('Test');

        // Act
        $this->mailer->send($message);

        $sentMessage = $this->mailer->getSentMessage();

        // Assert
        self::assertInstanceOf(SentMessage::class, $sentMessage);
        self::assertEquals([new Address('catchall@domain.com')], $sentMessage->getEnvelope()->getRecipients());
        self::assertEquals(new Address('sender@domain.com'), $sentMessage->getEnvelope()->getSender());
    }
}
