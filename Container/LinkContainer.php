<?php
/**
 * Copyright Pages Team 2015
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Pages
 * @link https://github.com/zikula-modules/Pages
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

namespace Kaikmedia\PagesModule\Container;

use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Translator\Translator;
use Zikula\Core\LinkContainer\LinkContainerInterface;

class LinkContainer implements LinkContainerInterface
{
    /**
     * @var Translator
     */
    private $translator;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct($translator, RouterInterface $router)
    {
        $this->translator = $translator;
        $this->router = $router;
    }

    public function getLinks($type = LinkContainerInterface::TYPE_ADMIN)
    {
        $links = array();
        if (\SecurityUtil::checkPermission('KaikmediaPagesModule::', '::', ACCESS_READ)) {
            $links[] = array(
                'url' => $this->router->generate('kaikmediapagesmodule_admin_manager'),
                'text' => $this->translator->__('Pages list'),
                'title' => $this->translator->__('Pages manager list'),
                'icon' => 'list');
        }
        if (\SecurityUtil::checkPermission('KaikmediaPagesModule::', '::', ACCESS_ADD)) {
            $links[] = array(
                'url' => $this->router->generate('kaikmediapagesmodule_admin_modify'),
                'text' => $this->translator->__('New Page'),
                'title' => $this->translator->__('Add media'),
                'icon' => 'plus');
        }
        if (\SecurityUtil::checkPermission('KaikmediaPagesModule::', '::', ACCESS_ADMIN)) {
            $links[] = array(
                'url' => $this->router->generate('kaikmediapagesmodule_admin_preferences'),
                'text' => $this->translator->__('Modify Config'),
                'title' => $this->translator->__('Add media'),
                'icon' => 'wrench');
        }

        return $links;
    }

    public function getBundleName()
    {
        return 'KaikmediaPagesModule';
    }
}