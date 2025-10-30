<?php
declare(strict_types=1);

namespace BEdita\Mail\Test\TestCase\Controller;

use BEdita\Mail\Controller\PlaceholderMessageController;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \BEdita\Mail\Controller\PlaceholderMessageController} Test Case
 */
#[CoversClass(PlaceholderMessageController::class)]
#[CoversMethod(PlaceholderMessageController::class, 'send')]
class PlaceholderMessageControllerTest extends TestCase
{
    /**
     * Test controller
     *
     * @var \BEdita\Mail\Controller\PlaceholderMessageController
     */
    public PlaceholderMessageController $controller;

    /**
     * Test `send` method on missing `name` data.
     *
     * @return void
     */
    public function testSendBadRequestException(): void
    {
        $exception = new BadRequestException('Missing message template name');
        $this->expectException(get_class($exception));
        $this->expectExceptionMessage($exception->getMessage());
        $serverRequest = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [],
        ]);
        $controller = new class ($serverRequest) extends PlaceholderMessageController {
            public function initialize(): void
            {
            }
        };
        $controller->send();
    }

    /**
     * Test `send` method.
     *
     * @return void
     */
    public function testSend(): void
    {
        $serverRequest = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'name' => 'test_template',
            ],
        ]);
        $controller = new class ($serverRequest) extends PlaceholderMessageController {
            public function initialize(): void
            {
            }

            protected function sendMail(string $name, array $data = [], array $config = []): void
            {
                // Do nothing
            }
        };
        $response = $controller->send();
        $this->assertSame(204, $response->getStatusCode());
    }
}
