<?php
declare(strict_types=1);

namespace BEdita\Mail\Test\TestCase\Mailer;

use BEdita\Mail\Mailer\PlaceholderMailer;
use Cake\Mailer\Transport\DebugTransport;
use Cake\Mailer\TransportFactory;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \BEdita\Mail\Mailer\PlaceholderMailer} Test Case
 */
#[CoversClass(PlaceholderMailer::class)]
#[CoversMethod(PlaceholderMailer::class, '__construct')]
#[CoversMethod(PlaceholderMailer::class, 'loadTemplate')]
#[CoversMethod(PlaceholderMailer::class, 'placeholderMessage')]
#[CoversMethod(PlaceholderMailer::class, 'processContent')]
#[CoversMethod(PlaceholderMailer::class, 'processConditionals')]
class PlaceholderMailerTest extends TestCase
{
    /**
     * Test placeholder message sending.
     *
     * @return void
     */
    public function testPlaceholderMessage()
    {
        $name = 'welcome_email';
        $data = [
            'user' => [
                'name' => 'John',
                'surname' => 'Doe',
                'email' => 'john.doe@example.com',
            ],
        ];
        $config = [
            'from' => 'sender@example.com',
            'to' => 'recipient@example.com',
            'transport' => 'default',
            'placeholderOptions' => [
                'lang' => 'en',
            ],
        ];
        $templateData = [
            'content' => 'Hello {{user.name}} {{user.surname}}, this is a test message sent to {{user.email}}.',
            'subject' => 'Test Message for {{user.name}}',
        ];

        TransportFactory::setConfig('default', ['className' => DebugTransport::class]);
        $mailer = new PlaceholderMailer([]);
        $result = $mailer->send('placeholderMessage', [$name, $data, $config, $templateData]);
        $headers = Hash::get($result, 'headers');
        $message = trim((string)Hash::get($result, 'message'));
        $this->assertNotEmpty($headers);
        $this->assertNotEmpty($message);
        $this->assertStringContainsString('Hello ' . $data['user']['name'] . ' ' . $data['user']['surname'], $message);
        $this->assertStringContainsString('this is a test message sent to ' . $data['user']['email'], $message);
    }
}
