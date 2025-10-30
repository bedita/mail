<?php
declare(strict_types=1);

/**
 * BEdita\Mail
 */
namespace BEdita\Mail\Mailer;

use Cake\Mailer\Mailer;
use Cake\Utility\Hash;

/**
 * Mailer class to handle message body with placeholders.
 */
class PlaceholderMailer extends BaseMailer
{
    /**
     * @inheritDoc
     */
    public static string $name = 'placeholder';

    /**
     * Default template placeholder options.
     *
     * @var array
     */
    protected array $placeholderOptions = [
        'objectType' => 'mail_templates',
        'contentField' => 'body',
        'subjectField' => 'title',
    ];

    /**
     * @inheritDoc
     */
    public function __construct($config)
    {
        $placeholdeConfig = (array)static::getConfig(static::$name);
        if (empty($config) && !empty($placeholdeConfig)) {
            $config = $placeholdeConfig;
        } elseif (is_string($config)) {
            $config = (array)static::getConfig($config);
        }
        // override default placeholder options from config
        $options = (array)Hash::get((array)$config, 'placeholderOptions');
        $this->placeholderOptions = array_merge($this->placeholderOptions, $options);
        parent::__construct($config);
    }

    /**
     * Process text containing placeholders.
     *
     * @param string $text The text content.
     * @param array $vars The vars.
     * @return string
     */
    protected function processContent(string $text, array $vars): string
    {
        $matches = [];
        $text = $this->processConditionals($text, $vars);
        // Extract placeholders from content - format: {{placeholder}} {{object.attribute}}
        preg_match_all('/\{\{\s*[a-zA-Z1-9_\.]+\s*\}\}/', $text, $matches);
        foreach ($matches[0] as $match) {
            $key = trim(strtolower(str_replace(['{{', '}}'], '', $match)));
            $value = (string)Hash::get($vars, $key);
            $text = str_replace($match, $value, $text);
        }

        return $text;
    }

    /**
     * Process conditional logic in text content.
     *
     * @param string $text The text content.
     * @param array $vars The vars.
     * @return string
     */
    protected function processConditionals(string $text, array $vars): string
    {
        $pattern = '/\{\%\s*if\s+([a-zA-Z1-9_\.]+)\s*\%\}(.*?)\{\%\s*endif\s*\%\}/s';

        return preg_replace_callback($pattern, function ($matches) use ($vars) {
            $key = trim($matches[1]);
            $content = $matches[2];
            $value = Hash::get($vars, $key);

            return !empty($value) ? $content : '';
        }, $text);
    }

    /**
     * Load template elements in an assoc array with keys:
     *  - 'content': message content with placeholders
     *  - 'subject': message subject
     *
     * @param string $id Uname or ID of an object
     * @param array $options Template load options: 'objectType', 'contentField', 'subjectField'
     * @return array
     */
    protected function loadTemplate(string $id, array $options = []): array
    {
        $options = array_merge($this->placeholderOptions, $options);
        $lang = Hash::get($options, 'lang');
        $query = $this->fetchTable($options['objectType'])
            ->find('unameId', [$id])
            ->find('available');
        if ($lang) {
            $query->find('translations', compact('lang'));
        }
        $object = $query->firstOrFail();
        $content = (string)Hash::get($object, $options['contentField']);
        $subject = (string)Hash::get($object, $options['subjectField']);

        if (empty($lang)) {
            return compact('content', 'subject');
        }
        $contentPath = sprintf('translations.0.translated_fields.%s', $options['contentField']);
        $subjectPath = sprintf('translations.0.translated_fields.%s', $options['subjectField']);

        return [
            'content' => (string)Hash::get($object, $contentPath, $content),
            'subject' => (string)Hash::get($object, $subjectPath, $subject),
        ];
    }

    /**
     * Handle default placeholder message.
     *
     * @param string $name Template object unique name.
     * @param array $data The data.
     * @param array $config Email message config, including 'placeholderOptions'.
     * @return \Cake\Mailer\Mailer
     */
    public function placeholderMessage(string $name, array $data, array $config = []): Mailer
    {
        $nameConfig = (array)static::getConfig($name);
        $config = array_merge($nameConfig, $config);

        $options = (array)Hash::get($config, 'placeholderOptions');
        $options = array_merge($this->placeholderOptions, $options);
        $items = $this->loadTemplate($name, $options);

        $body = $this->processContent($items['content'], $data);
        $this->setViewVars(compact('body'));
        // Use the placeholder template from the BEdita/Mail plugin (templates/email/placeholder)
        $this->viewBuilder()->setPlugin('BEdita/Mail')->setTemplate('placeholder');
        $subject = $this->processContent($items['subject'], $data);
        if (!empty($subject)) {
            $config['subject'] = $subject;
        }

        if (empty($config['transport'])) {
            $config['transport'] = Hash::get(static::getConfig('default'), 'transport', 'default');
        }

        return $this->setProfile($config);
    }
}
