<?php
declare(strict_types=1);

namespace BEdita\Mail;

use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Override;

/**
 * Plugin for BEdita\Mail
 *
 * @codeCoverageIgnore
 */
class Plugin extends BasePlugin
{
    /**
     * @inheritDoc
     */
    #[Override]
    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);
    }
}
