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

namespace Kaikmedia\PagesModule\Container;

use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Core\LinkContainer\LinkContainerInterface;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;

class LinkContainer implements LinkContainerInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var PermissionApiInterface
     */
    private $permissionApi;

    /**
     * @var bool
     */
    private $enableCategorization;

    /**
     * LinkContainer constructor.
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     * @param PermissionApiInterface $permissionApi
     */
    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        PermissionApiInterface $permissionApi,
        $enableCategorization
    ) {
        $this->translator = $translator;
        $this->router = $router;
        $this->permissionApi = $permissionApi;
        $this->enableCategorization = $enableCategorization;
    }

    public function getLinks($type = LinkContainerInterface::TYPE_ADMIN)
    {
        $links = [];
        if (LinkContainerInterface::TYPE_ADMIN == $type) {
            if ($this->permissionApi->hasPermission('KaikmediaPagesModule::', '::', ACCESS_READ)) {
                $links[] = [
                    'url' => $this->router->generate('kaikmediapagesmodule_admin_manager'),
                    'text' => $this->translator->__('Pages list'),
                    'title' => $this->translator->__('Pages manager list'),
                    'icon' => 'list'];
            }
            if ($this->permissionApi->hasPermission('KaikmediaPagesModule::', '::', ACCESS_ADD)) {
                $links[] = [
                    'url' => $this->router->generate('kaikmediapagesmodule_admin_modify'),
                    'text' => $this->translator->__('New Page'),
                    'title' => $this->translator->__('New Page'),
                    'icon' => 'plus'];
            }
            if ($this->permissionApi->hasPermission('KaikmediaPagesModule::', '::', ACCESS_ADMIN)) {
                $links[] = [
                    'url' => $this->router->generate('kaikmediapagesmodule_admin_preferences'),
                    'text' => $this->translator->__('Modify Config'),
                    'title' => $this->translator->__('Modify Config'),
                    'icon' => 'wrench'];
            }
        } elseif (LinkContainerInterface::TYPE_USER == $type) {
            if ($this->permissionApi->hasPermission('KaikmediaPagesModule::', '::', ACCESS_OVERVIEW)) {
//                $links[] = [
//                    'url' => $this->router->generate('zikulapagesmodule_user_listpages'),
//                    'text' => $this->translator->__('Pages list'),
//                    'icon' => 'list'];
                if ($this->enableCategorization) {
//                    $links[] = [
//                        'url' => $this->router->generate('zikulapagesmodule_user_categories'),
//                        'text' => $this->translator->__('Categories'),
//                        'icon' => 'tag'];
                }
            }
        }
    }

    public function getBundleName()
    {
        return 'KaikmediaPagesModule';
    }
}
