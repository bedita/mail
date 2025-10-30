<?php
declare(strict_types=1);

/**
 * BEdita\Mail
 */
namespace BEdita\Mail\Mailer;

use BEdita\Core\Model\Entity\User;
use Cake\Core\Configure;
use Cake\I18n\I18n;
use Cake\Mailer\Mailer;
use Cake\Utility\Hash;

abstract class BaseMailer extends Mailer
{
    /**
     * Site url
     *
     * @var string
     */
    protected string $siteUrl = null;

    /**
     * Lang used, i.e. `it`, `en`
     *
     * @var string
     */
    protected string $lang = null;

    /**
     * {@inheritDoc}
     *
     * Set common variables.
     */
    public function __construct($config)
    {
        parent::__construct($config);

        $this->siteUrl = (string)Configure::read('Project.siteUrl');
        $this->setViewVars('siteUrl', $this->siteUrl);
    }

    /**
     * Set locale and language to use in email.
     *
     * @param string $lang The language
     * @return void
     */
    public function setLocale(string $lang): void
    {
        $supported = ['it' => 'it_IT', 'en' => 'en_US'];
        if (!array_key_exists($lang, $supported)) {
            $this->lang = null;
            $this->setViewVars('lang', null);

            return;
        }

        I18n::setLocale($supported[$lang]);
        $this->lang = $lang;
        $this->setViewVars(compact('lang'));
    }

    /**
     * Set locale from user.
     * First read `user_preferences.preferred_lang` then `nationality`.
     * Fallback `it`.
     *
     * @param \BEdita\Core\Model\Entity\User $user The user
     * @return void
     */
    public function setLocaleFromUser(User $user): void
    {
        $lang = (string)Hash::get((array)$user->user_preferences, 'preferred_lang');
        if (!empty($lang)) {
            $this->setLocale($lang);

            return;
        }

        $lang = 'it';
        $nationality = $user->get('nationality');
        if (!empty($nationality) && $nationality !== 'Italy') {
            $lang = 'en';
        }

        $this->setLocale($lang);
    }
}
