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
        //return false;
        $connection = $this->entityManager->getConnection();
        $sqla = 'SELECT * FROM pages';
        $stmt = $connection->prepare($sqla);
        try {
            $stmt->execute();
        } catch (Exception $e) {
            $this->request->getSession()->getFlashBag()->add('error', $e->getMessage() . $this->__('Pages module table not found'));
            return false;
        }
        
        $sql[] = 'ALTER TABLE pages CHANGE urltitle urltitle VARCHAR(250) AFTER id';
        $sql[] = 'ALTER TABLE pages DROP COLUMN pid';
        $sql[] = 'ALTER TABLE pages CHANGE indepot depot TINYINT(1) AFTER urltitle';
        $sql[] = 'ALTER TABLE pages DROP COLUMN revision';
        $sql[] = 'ALTER TABLE pages CHANGE showinmenu inmenu TINYINT(1) AFTER online';        
        $sql[] = 'ALTER TABLE pages CHANGE showinlist inlist TINYINT(1) AFTER inmenu';
        $sql[] = 'ALTER TABLE pages CHANGE publishdate publishedAt DATETIME DEFAULT NULL AFTER inlist';
        $sql[] = 'ALTER TABLE pages CHANGE expiredate expiredAt DATETIME DEFAULT NULL AFTER publishedAt';
        $sql[] = 'ALTER TABLE pages CHANGE hitcount views INT(9) AFTER language';        
        $sql[] = 'ALTER TABLE pages CHANGE link layout VARCHAR(100) AFTER views';         
        $sql[] = 'UPDATE pages SET layout = "default"';
        $sql[] = 'ALTER TABLE pages CHANGE link_desc images LONGTEXT AFTER content';         
        $sql[] = 'UPDATE pages SET images = "a:0:{};"';
        $sql[] = 'ALTER TABLE pages CHANGE title title VARCHAR(250) AFTER author';        
        $sql[] = 'ALTER TABLE pages CHANGE obj_status status CHAR(1) AFTER images';
        
        $sql[] = 'ALTER TABLE pages CHANGE cr_date createdAt DATETIME DEFAULT NULL AFTER images';        
        $sql[] = 'ALTER TABLE pages CHANGE cr_uid createdBy CHAR(1) AFTER createdAt';
        $sql[] = 'ALTER TABLE pages CHANGE lu_date updatedAt DATETIME DEFAULT NULL AFTER createdBy';
        $sql[] = 'ALTER TABLE pages CHANGE lu_uid updatedBy CHAR(1) AFTER updatedAt';
        
        $sql[] = 'ALTER TABLE pages ADD deletedAt DATETIME DEFAULT NULL AFTER updatedAt';
        $sql[] = 'ALTER TABLE pages ADD deletedBy VARCHAR(11) AFTER deletedAt';
        
        $sql[] = 'RENAME TABLE pages TO kmpages';
        
        foreach ($sql as $sq) {
            $stmt = $connection->prepare($sq);
            try {
                $stmt->execute();
            } catch (Exception $e) {
                $this->request->getSession()->getFlashBag()->add('error', $e);
                return false;
            }
        }        
        
        // update all the tables to 3.0.0
        try {
            DoctrineHelper::updateSchema($this->entityManager, array('Kaikmedia\PagesModule\Entity\PagesEntity'));
        } catch (Exception $e) {
            $this->request->getSession()->getFlashBag()->add('error', $e);
            return false;
        }          

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