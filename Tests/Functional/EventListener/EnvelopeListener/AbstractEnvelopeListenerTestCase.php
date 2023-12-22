<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_mailer_development" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Ssch\T3MailerDevelopment\Tests\Functional\EventListener\EnvelopeListener;

use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

abstract class AbstractEnvelopeListenerTestCase extends FunctionalTestCase
{
    protected bool $initializeDatabase = false;

    protected array $testExtensionsToLoad = ['typo3conf/ext/t3_mailer_development'];

    protected Mailer $mailer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mailer = $this->get(Mailer::class);
    }

    abstract public function test(): void;
}
