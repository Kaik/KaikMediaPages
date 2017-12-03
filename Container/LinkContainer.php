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


    /**
     * get Links of any type for this extension
     * required by the interface.
     *
     * @param string $type
     *
     * @return array
     */
    public function getLinks($type = LinkContainerInterface::TYPE_ADMIN)
    {
        $method = 'get'.ucfirst(strtolower($type));
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return [];
    }

    /**
     * get the Admin links for this extension.
     *
     * @return array
     */
    private function getAdmin()
    {
        $links = [];

        if ($this->permissionApi->hasPermission($this->getBundleName().'::', '::', ACCESS_EDIT)) {
            $links[] = [
                    'url' => $this->router->generate('kaikmediapagesmodule_manager_list'),
                    'text' => $this->translator->__('List'),
                    'title' => $this->translator->__('Pages manager list'),
                    'icon' => 'list'];
            }
            if ($this->permissionApi->hasPermission('KaikmediaPagesModule::', '::', ACCESS_ADD)) {
                $links[] = [
                    'url' => $this->router->generate('kaikmediapagesmodule_manager_modify'),
                    'text' => $this->translator->__('New'),
                    'title' => $this->translator->__('Add a new Page'),
                    'icon' => 'plus'];
            }
            if ($this->permissionApi->hasPermission('KaikmediaPagesModule::', '::', ACCESS_ADMIN)) {
                $links[] = [
                    'url' => $this->router->generate('kaikmediapagesmodule_admin_preferences'),
                    'text' => $this->translator->__('Modify Config'),
                    'title' => $this->translator->__('Modify Config'),
                    'icon' => 'wrench'];
            }

        return $links;
    }

    /**
     * get the User links for this extension.
     *
     * @return array
     */
    private function getUser()
    {
        $links = [];

        return $links;
    }

    /**
     * get the Account links for this extension.
     *
     * @return array
     */
    private function getAccount()
    {
        $links = [];

        return $links;
    }

    public function getBundleName()
    {
        return 'KaikmediaPagesModule';
    }
}
