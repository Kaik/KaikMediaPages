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

namespace Kaikmedia\PagesModule\Helper;

use Doctrine\ORM\EntityManager;
use Kaikmedia\PagesModule\Entity\PageEntity;
use Kaikmedia\PagesModule\Entity\CategoryAssignmentEntity;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\ExtensionsModule\Api\VariableApi;
use Zikula\UsersModule\Entity\UserEntity;

/**
 * ImportHelper.
 *
 * @author Kaik
 */
class ImportHelper
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var VariableApi
     */
    private $variableApi;

    /**
     * Tables to remove from import table selector
     */
    private $excludeTables = ['admin_category',
        'admin_module',
        'block_placements',
        'block_positions',
        'blocks',
        'bundles',
        'categories_attributes',
        'categories_category',
        'categories_mapmeta',
        'categories_mapobj',
        'categories_registry',
        'group_applications',
        'group_membership',
        'group_perms',
        'groups',
        'hook_binding',
        'hook_runtime',
        'hooks',
        'menu_items',
        'module_deps',
        'module_vars',
        'modules',
        'oauth_mapping',
        'objectdata_attributes',
        'objectdata_log',
        'objectdata_meta',
        'pagelock',
        'sc_intrusion',
        'search_result',
        'search_stat',
        'session_info',
        'themes',
        'users',
        'users_attributes',
        'users_verifychg',
        'user_property',
        'workflows',
        'zauth_authentication_mapping',
        'zikula_routes_route',
// this module tables
        'km_pages',
        'km_pages_category'
    ];

    public function __construct(
            RequestStack $requestStack,
            EntityManager $entityManager,
            VariableApi $variableApi
        ) {
        $this->name = 'KaikmediaPagesModule';
        $this->requestStack = $requestStack;
        $this->request = $requestStack->getMasterRequest();
        $this->entityManager = $entityManager;
        $this->variableApi = $variableApi;
    }

    /*
     * DB tables check
     *
     */
    public function getTables()
    {
        $connection = $this->entityManager->getConnection();
        $schemaManager = $connection->getSchemaManager();
        $importTables = [];
        foreach ($schemaManager->listTables() as $table) {
            if(in_array($table->getName(), $this->excludeTables)) {
                continue;
            }
            $importTables[] = $table->getName();
        }

        return $importTables;
    }

    /*
     * Remove current data.
     *
     * @todo create remove all data functionality maybe in repository?
     */
    public function removeCurrentData()
    {
        return true;
    }

    /*
     * Get total messages count to import.
     */
    public function getTableCheck($data)
    {
        if ($data['table']) {
            $connection = $this->entityManager->getConnection();
            $sql = 'SELECT count(*) AS total FROM '.$data['table'];
            $statement = $connection->prepare($sql);
            $statement->execute();

            $data['total'] = $statement->fetchColumn();
        }

        return $data;
    }

    public function importData($data)
    {
        $connection = $this->entityManager->getConnection();
        $limit = $data['pageSize'];
        $offset = $data['page'] == 0 ? $data['page'] : $data['page'] * $limit;
        $sql = 'SELECT * FROM '.$data['table'].' ORDER BY id ASC LIMIT :offset,:limit';
        $statement = $connection->prepare($sql);
        $statement->bindValue('limit', $limit, \PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, \PDO::PARAM_INT);
        $statement->execute();
        $currentPageItems = $statement->fetchAll();
        $data['rejected_items'] = []; // each time new

        $this->defaultUser = $this->entityManager->find(UserEntity::class, 2);

        foreach ($currentPageItems as $item) {
            // only one reasons for item to be rejected
            $itemExists = $this->entityManager->find(PageEntity::class, (int) $item['id']);
            if ($itemExists) {
                $data['rejected_items'][(int) $item['id']] = $item;
                $data['rejected_items'][(int) $item['id']]['reason'] = 1;
                $data['rejected']++;
                continue;
            }

            $page = new PageEntity();

            // basics
            $page->setId((int) $item['id']);

            $page->setTitle($item['title']);

            $page->setUrltitle($item['urltitle']);

            $page->setContent($this->getContent($item));

            $page->setDescription($this->getDescription($item));

            $page->setOnline($item['online']);

            $page->setDepot($item['indepot']);

            $page->setInmenu($item['showinmenu']);

            $page->setInlist($item['showinlist']);

            $page->setLanguage($item['language']);

//            $item->setLayout(''); default

            $page->setViews($item['hitcount']);

            $page->setStatus($item['obj_status']);
            // users
            $page->setAuthor($this->getAuthor($item));
            $page->setCreatedBy($this->getCreatedBy($item));
            $page->setUpdatedBy($this->getUpdatedBy($item));
            $page->setDeletedBy($this->getDeletedBy($item));
            // dates
            $page->setCreatedAt($this->getCreatedAt($item));
            $page->setUpdatedAt($this->getUpdatedAt($item));
            $page->setExpiredAt($this->getExpiredAt($item));
            $page->setPublishedAt($this->getPublishedAt($item));
            $page->setDeletedAt($this->getDeletedAt($item));

            //store object
            $metadata = $this->entityManager->getClassMetadata(get_class($page));
            $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $this->entityManager->persist($page);
            $data['imported']++;
        }
        $this->entityManager->flush();
        $this->entityManager->clear();

        return $data;
    }

    private function getDescription($item)
    {
        if (array_key_exists('link_desc', $item)) {
            $description = empty($item['link_desc']) ? ' imported empty ' : $item['link_desc'];
        } else {
            $description = ' imported empty ';
        }

        return $description;
    }

    private function getContent($item)
    {
        if (array_key_exists('content', $item)) {
            $content = empty($item['content']) ? ' imported empty ' : $item['content'];
        } else {
            $content = ' imported empty ';
        }

        return $content;
    }

    /*
     * Users
     */
    private function getAuthor($item)
    {
        if (array_key_exists('author', $item)) {
            $user = $this->entityManager->find(UserEntity::class, $item['author']);
            if ($user) {
                return $user;
            } else {
                // default user
                return $this->defaultUser;
            }
        } else {
            // default user
            return $this->defaultUser;
        }
    }

    private function getCreatedBy($item)
    {
        if (array_key_exists('cr_uid', $item)) {
            $user = $this->entityManager->find(UserEntity::class, $item['cr_uid']);
            if ($user) {
                return $user;
            } else {
                // default user
                return $this->defaultUser;
            }
        } else {
            // default user
            return $this->defaultUser;
        }
    }

    private function getUpdatedBy($item)
    {
        if (array_key_exists('lu_uid', $item)) {
            $user = $this->entityManager->find(UserEntity::class, $item['lu_uid']);
            if ($user) {
                return $user;
            } else {
                // default user
                return $this->defaultUser;
            }
        } else {
            // default user
            return $this->defaultUser;
        }
    }

    private function getDeletedBy($item)
    {
        // default deleted
        return null;
    }

    /*
     * Dates
     */
    private function getCreatedAt($item)
    {
        if (array_key_exists('cr_date', $item) && !empty($item['cr_date'])) {
            return new \DateTime($item['cr_date']);
        } else {
            return new \DateTime('now'); // forced date
        }
    }

    private function getUpdatedAt($item)
    {
        if (array_key_exists('lu_date', $item) && !empty($item['lu_date'])) {
            return new \DateTime($item['lu_date']);
        } else {
            return new \DateTime('now'); // forced date
        }
    }

    private function getPublishedAt($item)
    {
        if (array_key_exists('publishdate', $item) && !empty($item['publishdate'])) {
            return new \DateTime($item['publishdate']);
        } else {
            return null; // null date
        }
    }

    private function getExpiredAt($item)
    {
        if (array_key_exists('expiredate', $item) && !empty($item['expiredate'])) {
            return new \DateTime($item['expiredate']);
        } else {
            return null; // null date
        }
    }

    private function getDeletedAt($item)
    {
        // default deleted
        return null;
    }
}
