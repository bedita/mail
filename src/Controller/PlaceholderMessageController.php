<?php
declare(strict_types=1);

/**
 * BEdita\Mail
 */
namespace BEdita\Mail\Controller;

use BEdita\API\Controller\JsonBaseController;
use BEdita\Mail\Mailer\PlaceholderMailer;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
use Cake\Mailer\MailerAwareTrait;

/**
 * Controller to send placeholder message
 */
class PlaceholderMessageController extends JsonBaseController
{
    use MailerAwareTrait;

    /**
     * Send placeholder message.
     *
     * @return \Cake\Http\Response
     */
    public function send(): Response
    {
        $this->request->allowMethod(['post']);
        $name = $this->request->getData('name');
        if (empty($name)) {
            throw new BadRequestException('Missing message template name');
        }
        $data = (array)$this->request->getData('data');
        $config = (array)$this->request->getData('config');
        $this->getMailer(PlaceholderMailer::class, (array)Configure::read('PlaceholderMailer'))
            ->send('placeholderMessage', [$name, $data, $config]);

        return $this->getResponse()->withStatus(204);
    }
}
