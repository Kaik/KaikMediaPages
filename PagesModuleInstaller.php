<?php

/**
 * Copyright (c) KaikMedia.com 2014
 */

namespace Kaikmedia\PagesModule;

use Zikula\Core\AbstractBundle;
use Zikula\Core\ExtensionInstallerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zikula\Component\HookDispatcher\AbstractContainer;
use Zikula\Component\HookDispatcher\SubscriberBundle;

class PagesModuleInstaller implements ExtensionInstallerInterface, ContainerAwareInterface {
    
    use TranslatorTrait;
    //use ExtensionVariablesTrait;

    /**
     * @var string the bundle name.
     */
    protected $name;
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /**
     * @var AbstractBundle
     */
    protected $bundle;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;
    /**
     * @var \Zikula\Core\Doctrine\Helper\SchemaHelper
     */
    protected $schemaTool;
    /**
     * @var \Zikula\ExtensionsModule\Api\HookApi
     */
    protected $hookApi;
  
    /**
     * @var \
     */
    private $entities = array(
        'Kaikmedia\PagesModule\Entity\PageEntity',
        'Kaikmedia\PagesModule\Entity\CategoryAssignmentEntity'
    );    

    public function setBundle(AbstractBundle $bundle)
    {
        $this->bundle = $bundle;
        $this->name = $bundle->getName();
        if ($this->container) {
            // both here and in `setContainer` so either method can be called first.
            $this->container->get('translator')->setDomain($this->bundle->getTranslationDomain());
        }
    }

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->setTranslator($container->get('translator'));
        $this->entityManager = $container->get('doctrine.entitymanager');
        $this->schemaTool = $container->get('zikula.doctrine.schema_tool');
        $this->extensionName = $this->name; // for ExtensionVariablesTrait
        $this->variableApi = $container->get('zikula_extensions_module.api.variable'); // for ExtensionVariablesTrait
        $this->hookApi = $container->get('zikula_extensions_module.api.hook');
        if ($this->bundle) {
            $container->get('translator')->setDomain($this->bundle->getTranslationDomain());
        }
    }

    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     * Convenience shortcut to add a session flash message.
     * @param $type
     * @param $message
     */
    public function addFlash($type, $message)
    {
        if (!$this->container->has('session')) {
            throw new \LogicException('You can not use the addFlash method if sessions are disabled.');
        }

        $this->container->get('session')->getFlashBag()->add($type, $message);
    }

    public function install() {

       // create table
        try {
            $this->schemaTool->create($this->entities);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return false;
        }
        // insert default category
        try {
            $this->createCategoryTree();
        } catch (\Exception $e) {
            $this->addFlash('error', $this->__f('Did not create default categories (%s).', $e->getMessage()));
        }
        // set up config variables
        $modvars = array(
            'itemsperpage' => 25,
            'enablecategorization' => true
        );
        $this->variableApi->setAll($this->name, $modvars);
        $hookContainer = $this->hookApi->getHookContainerInstance($this->bundle->getMetaData());
        \HookUtil::registerSubscriberBundles($hookContainer->getHookSubscriberBundles());

        
        // initialisation successful
        return true;       
    }

    public function upgrade($oldversion) {
        /*
        // return false;
        $connection = $this->entityManager->getConnection();
        
        $sqla = 'SELECT * FROM pages';
        $stmt = $connection->prepare($sqla);
        try {
            $stmt->execute();
        } catch (\Exception $e) {
            $this->request->getSession()
                    ->getFlashBag()
                    ->add('error', $e->getMessage());
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
        $sql[] = 'ALTER TABLE pages DROP COLUMN link_desc';
        $sql[] = 'ALTER TABLE pages CHANGE title title VARCHAR(250) AFTER author';
        $sql[] = 'ALTER TABLE pages CHANGE obj_status status CHAR(1) AFTER author';
        $sql[] = 'ALTER TABLE pages CHANGE language language VARCHAR(5) DEFAULT NULL AFTER expiredAt';
        $sql[] = 'ALTER TABLE pages CHANGE cr_date createdAt DATETIME DEFAULT NULL AFTER author';
        $sql[] = 'ALTER TABLE pages CHANGE cr_uid createdBy CHAR(1) AFTER createdAt';
        $sql[] = 'ALTER TABLE pages CHANGE lu_date updatedAt DATETIME DEFAULT NULL AFTER createdBy';
        $sql[] = 'ALTER TABLE pages CHANGE lu_uid updatedBy CHAR(1) AFTER updatedAt';

        $sql[] = 'ALTER TABLE pages ADD deletedAt DATETIME DEFAULT NULL AFTER updatedAt';
        $sql[] = 'ALTER TABLE pages ADD deletedBy VARCHAR(11) AFTER deletedAt';

        $sql[] = 'RENAME TABLE pages TO km_pages';

        foreach ($sql as $sq) {
            $stmt = $connection->prepare($sq);
            try {
                $stmt->execute();
            } catch (\Exception $e) {
                $this->request->getSession()
                        ->getFlashBag()
                        ->add('error', $e);
                return false;
            }
        }
        
        // update all the tables to 3.0.0
        try {
            \DoctrineHelper::createSchema($this->entityManager, $this->_entities);
        } catch (\Exception $e) {
            $this->request->getSession()
                    ->getFlashBag()
                    ->add('error', $e->getMessage());
            return false;
        }
       
        // insert default category
        try {
            $this->createCategoryTree();
        } catch (\Exception $e) {
            $this->request->getSession()
                    ->getFlashBag()
                    ->add('error', $e->getMessage());
        }         
         */
        //$hookContainer = $this->hookApi->getHookContainerInstance($this->bundle->getMetaData());
        //HookUtil::registerSubscriberBundles($hookContainer->getHookSubscriberBundles());        

        return true;
    }

    public function uninstall() {
        // drop table
        $this->schemaTool->drop($this->entities);
        // Delete any module variables
        $this->variableApi->delAll($this->name);
        // Delete entries from category registry
        \CategoryRegistryUtil::deleteEntry($this->bundle->getName());
        $hookContainer = $this->hookApi->getHookContainerInstance($this->bundle->getMetaData());
        \HookUtil::unregisterSubscriberBundles($hookContainer->getHookSubscriberBundles());
        // Deletion successful
        return true;
    }
    
    /**
     * create the category tree
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If Root category not found.
     * @throws \Exception
     *
     * @return boolean
     */
    private function createCategoryTree()
    {
        // create category
        \CategoryUtil::createCategory('/__SYSTEM__/Modules', $this->bundle->getName(), null, $this->__('Pages'), $this->__('Static pages'));
        // create subcategory
        \CategoryUtil::createCategory('/__SYSTEM__/Modules/KaikmediaPagesModule', 'Category1', null, $this->__('Category 1'), $this->__('Initial sub-category created on install'), array('color' => '#99ccff'));
        \CategoryUtil::createCategory('/__SYSTEM__/Modules/KaikmediaPagesModule', 'Category2', null, $this->__('Category 2'), $this->__('Initial sub-category created on install'), array('color' => '#cceecc'));
        // get the category path to insert Pages categories
        $rootcat = \CategoryUtil::getCategoryByPath('/__SYSTEM__/Modules/KaikmediaPagesModule');
        if ($rootcat) {
            // create an entry in the categories registry to the Main property
            if (!\CategoryRegistryUtil::insertEntry($this->bundle->getName(), 'PageEntity', 'Main', $rootcat['id'])) {
                throw new \Exception('Cannot insert Category Registry entry.');
            }
        } else {
            throw new NotFoundHttpException('Root category not found.');
        }
        return true;
    }    

}
