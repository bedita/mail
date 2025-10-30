<?php
declare(strict_types=1);

/**
 * BEdita\Mail
 */
namespace BEdita\Mail\Mailer\Preview;

use Cake\Mailer\Mailer;
use DebugKit\Mailer\MailPreview;

/**
 * Preview test emails.
 */
class PlaceholderMailerPreview extends MailPreview
{
    /**
     * Preview `testMessage` email.
     *
     * @return \Cake\Mailer\Mailer
     */
    public function testMessage(): Mailer
    {
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
            'placeholderOptions' => [
                // 'objectType' => 'documents',
                // 'contentField' => 'body',
                // 'subjectField' => 'title',
                'lang' => 'en',
            ],
        ];

        return $this->getMailer('Placeholder')
            ->placeholderMessage('test-message', $data, $config);
    }
}
