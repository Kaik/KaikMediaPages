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
use Kaikmedia\PagesModule\Security\AccessManager;

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
     * @var AccessManager
     */
    private $accessManager;

    /**
     * @var bool
     */
    private $enableCategorization;

    /**
     * LinkContainer constructor.
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     * @param AccessManager $accessManager
     */
    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        AccessManager $accessManager,
        $enableCategorization
    ) {
        $this->translator = $translator;
        $this->router = $router;
        $this->accessManager = $accessManager;
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

        if ($this->accessManager->hasPermission(ACCESS_EDIT, false, ':manager:list')) {
            $links[] = [
                    'url' => $this->router->generate('kaikmediapagesmodule_manager_list'),
                    'text' => $this->translator->__('List'),
                    'title' => $this->translator->__('Pages manager list'),
                    'icon' => 'list'];
            }
            if ($this->accessManager->hasPermission(ACCESS_ADD, false, ':manager:modify')) {
                $links[] = [
                    'url' => $this->router->generate('kaikmediapagesmodule_manager_modify'),
                    'text' => $this->translator->__('New'),
                    'title' => $this->translator->__('Add a new Page'),
                    'icon' => 'plus'];
            }
            if ($this->accessManager->hasPermission(ACCESS_ADMIN, false)) {
                $links[] = [
                    'url' => $this->router->generate('kaikmediapagesmodule_admin_preferences'),
                    'text' => $this->translator->__('Modify Config'),
                    'title' => $this->translator->__('Modify Config'),
                    'icon' => 'wrench'];
            }
            if ($this->accessManager->hasPermission(ACCESS_ADMIN, false)) {
                $links[] = [
                    'url' => $this->router->generate('kaikmediapagesmodule_import_index'),
                    'text' => $this->translator->__('Import'),
                    'title' => $this->translator->__('Import'),
                    'icon' => 'download'];
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
