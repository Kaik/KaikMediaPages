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

namespace Kaikmedia\PagesModule;

use Zikula\CategoriesModule\Entity\CategoryAttributeEntity;
use Zikula\CategoriesModule\Entity\CategoryEntity;
use Zikula\CategoriesModule\Entity\CategoryRegistryEntity;
use Zikula\Core\AbstractExtensionInstaller;
use Kaikmedia\PagesModule\Entity\CategoryAssignmentEntity;
use Kaikmedia\PagesModule\Entity\PageEntity;

class PagesModuleInstaller extends AbstractExtensionInstaller
{
    /**
     * @var \
     */
    private $entities = [
        PageEntity::class,
        CategoryAssignmentEntity::class
    ];

    public function install()
    {
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
            $this->addFlash('error', $this->__f('Did not create default categories (%s).', ['%s' => $e->getMessage()]));
        }
        // set up config variables
        $modvars = [
            'itemsperpage' => 25,
            'enablecategorization' => true
        ];
        $this->setVars($modvars);

        // initialisation successful
        return true;
    }

    public function upgrade($oldversion)
    {
        // this module does not have released previous versions this upgrade is just installing module in place of old Pages
        // use import to collect data from other modules
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
            $this->addFlash('error', $this->__f('Did not create default categories (%s).', ['%s' => $e->getMessage()]));
        }
        // set up config variables
        $modvars = [
            'itemsperpage' => 25,
            'enablecategorization' => true
        ];
        $this->setVars($modvars);

        // initialisation successful
        return true;
    }

    public function uninstall()
    {
        // drop table
        try {
            $this->schemaTool->drop($this->entities);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return false;
        }
        // remove module vars
        $this->delVars();
        // Delete entries from category registry
        $registries = $this->container->get('zikula_categories_module.category_registry_repository')->findBy(['modname' => $this->bundle->getName()]);
        foreach ($registries as $registry) {
            $this->entityManager->remove($registry);
        }
        $this->entityManager->flush();
        // Deletion successful

        return true;
    }

    /**
     * create the category tree
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If Root category not found
     * @throws \Exception
     *
     * @return boolean
     */
    private function createCategoryTree()
    {
        $locale = $this->container->get('request_stack')->getCurrentRequest()->getLocale();
        $repo = $this->container->get('zikula_categories_module.category_repository');
        // create pages root category
        $parent = $repo->findOneBy(['name' => 'Modules']);
        $pagesRoot = new CategoryEntity();
        $pagesRoot->setParent($parent);
        $pagesRoot->setName($this->bundle->getName());
        $pagesRoot->setDisplay_name([
            $locale => $this->__('Pages', 'kaikmediapagesmodule', $locale)
        ]);
        $pagesRoot->setDisplay_desc([
            $locale => $this->__('Static Pages', 'kaikmediapagesmodule', $locale)
        ]);
        $this->entityManager->persist($pagesRoot);
        // create children
        $category1 = new CategoryEntity();
        $category1->setParent($pagesRoot);
        $category1->setName('Default');
        $category1->setDisplay_name([
            $locale => $this->__('Default', 'kaikmediapagesmodule', $locale)
        ]);
        $category1->setDisplay_desc([
            $locale => $this->__('Initial sub-category created on install', 'kaikmediapagesmodule', $locale)
        ]);
        $attribute = new CategoryAttributeEntity();
        $attribute->setAttribute('color', '#99ccff');
        $category1->addAttribute($attribute);
        $this->entityManager->persist($category1);
        $category2 = new CategoryEntity();
        $category2->setParent($pagesRoot);
        $category2->setName('Internal');
        $category2->setDisplay_name([
            $locale => $this->__('Internal', 'kaikmediapagesmodule', $locale)
        ]);
        $category2->setDisplay_desc([
            $locale => $this->__('Initial sub-category created on install', 'kaikmediapagesmodule', $locale)
        ]);
        $attribute = new CategoryAttributeEntity();
        $attribute->setAttribute('color', '#cceecc');
        $category2->addAttribute($attribute);
        $this->entityManager->persist($category2);
        // create Registry
        $registry = new CategoryRegistryEntity();
        $registry->setCategory($pagesRoot);
        $registry->setEntityname('PageEntity');
        $registry->setModname($this->bundle->getName());
        $registry->setProperty('Main');
        $this->entityManager->persist($registry);
        $this->entityManager->flush();
        return true;
    }
}

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

//    /**
//     * create the category tree
//     *
//     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If Root category not found.
//     * @throws \Exception
//     *
//     * @return boolean
//     */
//    private function createCategoryTree()
//    {
//        // create category
//        \CategoryUtil::createCategory('/__SYSTEM__/Modules', $this->bundle->getName(), null, $this->__('Pages'), $this->__('Static pages'));
//        // create subcategory
//        \CategoryUtil::createCategory('/__SYSTEM__/Modules/KaikmediaPagesModule', 'Category1', null, $this->__('Category 1'), $this->__('Initial sub-category created on install'), ['color' => '#99ccff']);
//        \CategoryUtil::createCategory('/__SYSTEM__/Modules/KaikmediaPagesModule', 'Category2', null, $this->__('Category 2'), $this->__('Initial sub-category created on install'), ['color' => '#cceecc']);
//        // get the category path to insert Pages categories
//        $rootcat = \CategoryUtil::getCategoryByPath('/__SYSTEM__/Modules/KaikmediaPagesModule');
//        if ($rootcat) {
//            // create an entry in the categories registry to the Main property
//            if (!\CategoryRegistryUtil::insertEntry($this->bundle->getName(), 'PageEntity', 'Main', $rootcat['id'])) {
//                throw new \Exception('Cannot insert Category Registry entry.');
//            }
//        } else {
//            throw new NotFoundHttpException('Root category not found.');
//        }
//        return true;
//    }