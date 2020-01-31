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

class MainMediaPreviewUiHooksSubscriber implements HookSubscriberInterface
{
    const DISPLAY_VIEW = 'pages.ui_hooks.mainmedia_preview.display_view';

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
        return $this->translator->__('Pages Main Media preview area');
    }

    public function getEvents()
    {
        return [
            UiHooksCategory::TYPE_DISPLAY_VIEW => self::DISPLAY_VIEW,
        ];
    }
}
