<?php
declare(strict_types=1);

namespace BEdita\Mail\Test\TestCase\Mailer;

use BEdita\Core\Model\Entity\User;
use BEdita\Mail\Mailer\BaseMailer;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \BEdita\Mail\Mailer\BaseMailer} Test Case
 */
#[CoversClass(BaseMailer::class)]
#[CoversMethod(BaseMailer::class, '__construct')]
#[CoversMethod(BaseMailer::class, 'setLocale')]
#[CoversMethod(BaseMailer::class, 'setLocaleFromUser')]
class BaseMailerTest extends TestCase
{
    /**
     * Test __construct method.
     *
     * @return void
     */
    public function testConstruct(): void
    {
        Configure::write('Project.siteUrl', 'https://example.com');
        $mailer = new class ([]) extends BaseMailer {
            public function getViewVars($key)
            {
                return $this->viewBuilder()->getVars()[$key] ?? null;
            }
        };
        $this->assertNotEmpty($mailer->getViewVars('siteUrl'));
        $this->assertSame('https://example.com', $mailer->getViewVars('siteUrl'));
    }

    /**
     * Test setLocale method.
     *
     * @return void
     */
    public function testSetLocale(): void
    {
        $mailer = new class ([]) extends BaseMailer {
            public function getLang()
            {
                return $this->lang;
            }
        };
        $mailer->setLocale('it');
        $this->assertSame('it', $mailer->getLang());

        $mailer->setLocale('es');
        $this->assertNull($mailer->getLang());
    }

    /**
     * Test setLocaleFromUser method.
     *
     * @return void
     */
    public function testSetLocaleFromUser(): void
    {
        $mailer = new class ([]) extends BaseMailer {
            public function getLang()
            {
                return $this->lang;
            }
        };
        $user = new User();
        $mailer->setLocaleFromUser($user);
        $this->assertSame('it', $mailer->getLang());

        $user = new User(['user_preferences' => ['preferred_lang' => 'en']]);
        $mailer->setLocaleFromUser($user);
        $this->assertSame('en', $mailer->getLang());
    }
}
