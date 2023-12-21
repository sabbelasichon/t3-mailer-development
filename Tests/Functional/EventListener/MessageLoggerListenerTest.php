<?php
declare(strict_types=1);

namespace Ssch\T3MailerDevelopment\Tests\Functional\EventListener;

use Spatie\Snapshots\MatchesSnapshots;
use Ssch\T3MailerDevelopment\EventListener\MessageLoggerListener;
use Symfony\Component\Mime\RawMessage;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class MessageLoggerListenerTest extends FunctionalTestCase
{
    use MatchesSnapshots;

    protected bool $initializeDatabase = false;
    private MessageLoggerListener $subject;
    private MailerInterface $mailer;

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/t3_mailer_development'
    ];

    protected array $configurationToUseInTestInstance = [
        'MAIL' => [
            'transport' => 'null',
        ]
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->get(MessageLoggerListener::class);
        $this->mailer = $this->get(MailerInterface::class);
    }

    public function test(): void
    {
        // Arrange
        /** @var MailMessage $message */
        $message = GeneralUtility::makeInstance(MailMessage::class);
        $message->addTo('max.mustermann@domain.com');
        $message->text('Test');

        // Act
        $this->mailer->send($message);

        // Assert
        $events = $this->subject->getEvents();

        self::assertNotEmpty($events);
        self::assertNotEmpty($events->getMessages());

        $firstMessage = $events->getMessages()[0];
        self::assertInstanceOf(RawMessage::class, $firstMessage);
        $this->assertMatchesTextSnapshot($firstMessage->toString());
    }
}
