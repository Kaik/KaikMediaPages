<?php
/**
 * Copyright (c) KaikMedia.com 2014
 */
namespace Kaikmedia\PagesModule\Api;

use System;
use SecurityUtil;
use ModUtil;
use Kaikmedia\PagesModule\Entity\PagesEntity;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * API functions used by administrative controllers
 */
class AdminApi extends \Zikula_AbstractApi
{

    /**
     * get available admin panel links
     * 
     * @return array array of admin links
     */
    public function getLinks()
    {
        $links = array();
        if (SecurityUtil::checkPermission('KaikmediaPagesModule::', '::', ACCESS_ADMIN)) {
            $links[] = array(
                'url' => $this->get('router')->generate('kaikmediapagesmodule_admin_manager'),'text' => $this->__('Manager'),'title' => $this->__('Here you can manage your messages database'),'icon' => 'list'
            );
            $links[] = array(
                'url' => $this->get('router')->generate('kaikmediapagesmodule_admin_modify'),'text' => $this->__('Add new page'),'title' => $this->__('Add new page'),'icon' => 'plus'
            );
            $links[] = array(
                'url' => $this->get('router')->generate('kaikmediapagesmodule_admin_preferences'),'text' => $this->__('Settings'),'title' => $this->__('Adjust module settings'),'icon' => 'wrench'
            );
        }
        return $links;
    }
}