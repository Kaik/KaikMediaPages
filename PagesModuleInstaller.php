<?php

namespace Kaikmedia\PagesModule;

use HookUtil;
use Exception;
use DoctrineHelper;

class PagesModuleInstaller extends \Zikula_AbstractInstaller
{
    
    private $_entities = array('Kaikmedia\PagesModule\Entity\PagesEntity');    
    
    
    public function install()
    {
        
        try {
            DoctrineHelper::createSchema($this->entityManager,$this->_entities);
        } catch (\Exception $e) {
            $this->request->getSession()->getFlashBag()->add('error', $e->getMessage());
            return false;
        }        
        
        
        $this->setVar('itemsperpage', 0);
        $this->setVar('images_max_count', 0);
        $this->setVar('images_max_size', 0);
        $this->setVar('images_ext_allowed', 0);
        
        return true;
    }

    public function upgrade($oldversion)
    {
        return true;
    }

    public function uninstall()
    {
        try {
            DoctrineHelper::dropSchema($this->entityManager, $this->_entities);
        } catch (Exception $e) {
            $this->request->getSession()->getFlashBag()->add('error', $e->getMessage());
            return false;
        }
        // remove module vars
        $this->delVars();
        // unregister hooks
        HookUtil::unregisterSubscriberBundles($this->version->getHookSubscriberBundles());
        HookUtil::unregisterProviderBundles($this->version->getHookProviderBundles());
        // Deletion successful
        return true;
    }
}