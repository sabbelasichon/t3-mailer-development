<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_mailer_development" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Ssch\T3MailerDevelopment\Tests\Functional\EventListener\EnvelopeListener;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class EnvelopeListenerWithWhiteListRecipientsTest extends AbstractEnvelopeListenerTestCase
{
    protected array $configurationToUseInTestInstance = [
        'MAIL' => [
            'transport' => 'null',
        ],
        'EXTENSIONS' => [
            't3_mailer_development' => [
                'whiteListRecipients' => 'max.mustermann@domain.com',
            ],
        ],
    ];

    public function test(): void
    {
        // Arrange
        $message = GeneralUtility::makeInstance(MailMessage::class);
        $message->addTo('max.mustermann@domain.com');
        $message->addTo('some.mustermann@domain.com');
        $message->text('Test');

        $envelope = Envelope::create($message);

        // Act
        $this->mailer->send($message, $envelope);

        $sentMessage = $this->mailer->getSentMessage();

        // Assert
        self::assertInstanceOf(SentMessage::class, $sentMessage);
        self::assertEquals(
            [new Address('max.mustermann@domain.com')],
            $sentMessage->getEnvelope()
                ->getRecipients()
        );
    }
}
