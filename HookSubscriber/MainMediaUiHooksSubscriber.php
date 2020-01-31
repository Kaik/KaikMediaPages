<?php

/**
 * KaikMedia PagesModule
 *
 * @package    KaikmediaPagesModule
 * @author     Kaik <contact@kaikmedia.com>
 * @copyright  KaikMedia
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @link       https://github.com/Kaik/KaikMediaPages.git
 */

namespace Kaikmedia\PagesModule\HookSubscriber;

use Zikula\Bundle\HookBundle\Category\UiHooksCategory;
use Zikula\Bundle\HookBundle\HookSubscriberInterface;
use Zikula\Common\Translator\TranslatorInterface;

class MainMediaUiHooksSubscriber implements HookSubscriberInterface
{
    const DISPLAY_VIEW = 'pages.ui_hooks.mainmedia.display_view';
    const FORM_EDIT = 'pages.ui_hooks.mainmedia.form_edit';
    const VALIDATE_EDIT = 'pages.ui_hooks.mainmedia.validate_edit';
    const PROCESS_EDIT = 'pages.ui_hooks.mainmedia.process_edit';
    const FORM_DELETE = 'pages.ui_hooks.mainmedia.form_delete';
    const VALIDATE_DELETE = 'pages.ui_hooks.mainmedia.validate_delete';
    const PROCESS_DELETE = 'pages.ui_hooks.mainmedia.process_delete';

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getOwner()
    {
        return 'KaikmediaPagesModule';
    }

    public function getCategory()
    {
        return UiHooksCategory::NAME;
    }

    public function getTitle()
    {
        return $this->translator->__('Pages Main Media area');
    }

    public function getEvents()
    {
        return [
            UiHooksCategory::TYPE_DISPLAY_VIEW => self::DISPLAY_VIEW,
            UiHooksCategory::TYPE_FORM_EDIT => self::FORM_EDIT,
            UiHooksCategory::TYPE_VALIDATE_EDIT => self::VALIDATE_EDIT,
            UiHooksCategory::TYPE_PROCESS_EDIT => self::PROCESS_EDIT,
            UiHooksCategory::TYPE_FORM_DELETE => self::FORM_DELETE,
            UiHooksCategory::TYPE_VALIDATE_DELETE => self::VALIDATE_DELETE,
            UiHooksCategory::TYPE_PROCESS_DELETE => self::PROCESS_DELETE,
        ];
    }
}
