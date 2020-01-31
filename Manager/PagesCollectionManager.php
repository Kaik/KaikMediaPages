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

namespace Kaikmedia\PagesModule\Manager;

//use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\EntityManager;

//use Kaikmedia\GalleryModule\Entity\Media\ImageEntity as Media;
//use Kaikmedia\GalleryModule\Entity\Relations\HooksRelationsEntity;
//use Kaikmedia\NewsModule\Entity\NewsEntity;
//use Kaikmedia\NewsModule\Entity\CategoryAssignmentEntity;
use Kaikmedia\PagesModule\Entity\PagesQueryBuilder;
use Kaikmedia\PagesModule\Helper\CategorisationHelper;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\Bundle\CoreBundle\HttpKernel\ZikulaHttpKernelInterface;
use Zikula\ExtensionsModule\Api\VariableApi;
//use Zikula\UsersModule\Entity\UserEntity;


/**
 * PagesCollection
 *
 * @author Kaik
 */
class PagesCollectionManager
{
    /*
     * News Items
    */
    private $items = [];

    /*
     * Paging
     */
    private $pager = false;

    private $page = 1;

    private $limit = 25;

    private $count = 0;

    private $offset = 0;
    /*
     * Filters
     */
    private $filters = [];

    /*
     * Sorting
     */
    private $sortBy = 'publishedAt';

    private $sortOrder = 'DESC';

    /**
     * @var ZikulaHttpKernelInterface
     */
    private $kernel;

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
     * @var CategorisationHelper
     */
    private $categorisationHelper;

    /**
     * Constructor
     *
     * @todo add
     */
    public function __construct(
        ZikulaHttpKernelInterface $kernel,
        RequestStack $requestStack,
        EntityManager $entityManager,
        VariableApi $variableApi,
        CategorisationHelper $categorisationHelper
    ) {
        $this->name = 'KaikmediaPagesModule';
        $this->kernel = $kernel;
        $this->requestStack = $requestStack;
        $this->request = $requestStack->getMasterRequest();
        $this->entityManager = $entityManager;
        $this->variableApi = $variableApi;
        $this->categorisationHelper = $categorisationHelper;
        $this->setDefaultFilters();
    }

    /*
     * Default filter settings for online
     *
     */
    protected function setDefaultFilters()
    {
        $this->filters = [
            'title'                 => false,
            'online'                => 1,
            'depot'                 => 0,
            'inmenu'                => 1,
            'inlist'                => 1,
            'author'                => false,
            'layout'                => 'any',
            'expired'               => 'published',
            'published'             => 'published',
            'language'              => 'any',
            'topic'                 => [],
        ];
    }

    public function buildCollection()
    {
        $this->qb = new PagesQueryBuilder($this->entityManager);
        $this->qb->select('p');
        $this->qb->from('Kaikmedia\PagesModule\Entity\PageEntity', 'p');

        return $this;
    }

    /*
     *  Setters
     */

    /*
     * Items
     * Should not be used externally
     *
     */
    private function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /*
     * Paging and pager
     *
     */
    public function setEnablePager($pager)
    {
        $this->pager = $pager;

        return $this;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /*
     * Sorting
     */
    public function setSortBy($sortBy)
    {
        $this->sortBy = !empty($sortBy) ? $sortBy : $this->sortBy;

        return $this;
    }

    public function setSortOrder($sortOrder)
    {
        $allowedOptions = ['asc', 'ASC', 'desc', 'DESC'];
        $this->sortOrder = in_array($sortOrder, $allowedOptions) ? strtoupper($sortOrder) : $this->sortOrder ;

        return $this;
    }

    /*
     * Filters
     *
     * Set array of filters
     *
     */
    public function setFilters($filters)
    {
        if (!is_array($filters)) {
            return $this;
        }

        foreach ($filters as $key => $value) {
            // key check
            if (!array_key_exists($key, $this->filters)) {
                continue;
            }
            // value check
            if ('' === $value) {
                continue;
            }
            // method check
            $fn = 'set' . ucfirst($key);
            if (!method_exists($this, $fn)) {
                continue;
            }

            $this->$fn($value);
        }

        return $this;
    }

    public function setId($id)
    {
        $this->filters['id'] = $id;

        return $this;
    }

    public function setExcludeIds($excludeIds)
    {
        $this->filters['excludeIds'] = array_key_exists('excludeIds', $this->filters) && is_array($this->filters['excludeIds'])
            ? is_array($excludeIds) && !empty($excludeIds)
                ? array_merge($this->filters['excludeIds'], $excludeIds)
                : $this->filters['excludeIds']
            : is_array($excludeIds) && !empty($excludeIds)
                ? $excludeIds
                : false
            ;

        return $this;
    }

    public function setTitle($title)
    {
        $this->filters['title'] = $title;

        return $this;
    }

    public function setUrltitle($urltitle)
    {
        $this->filters['urltitle'] = $urltitle;

        return $this;
    }

    public function setInlist($inlist)
    {
        $this->filters['inlist'] = $inlist;

        return $this;
    }

    public function setInmenu($inmenu)
    {
        $this->filters['inmenu'] = $inmenu;

        return $this;
    }

    public function setDeleted($deleted)
    {
        $this->filters['deleted'] = $deleted;

        return $this;
    }

    public function setAuthor($author)
    {
        $this->filters['author'] = $author !== '0' ? $author : false;

        return $this;
    }

    public function setDepot($depot)
    {
        $this->filters['depot'] = $depot;

        return $this;
    }

    public function setLayout($layout)
    {
        $this->filters['layout'] = $layout;

        return $this;
    }

    public function setOnline($online)
    {
        $this->filters['online'] = $online;

        return $this;
    }

    public function setExpired($expired)
    {
        $allowedOptions = ['published', 'expired', 'awaiting', 'unset', 'any'];
        $this->filters['expired'] = in_array($expired, $allowedOptions) ? $expired : 'published';

        return $this;
    }

    public function setPublished($published)
    {
        $allowedOptions = ['published', 'awaiting', 'unset', 'any'];
        $this->filters['published'] = in_array($published, $allowedOptions) ? $published : 'published';

        return $this;
    }

    public function setPublishedBetween($from, $to)
    {
        $this->filters['publishedBetween'] = ['from' => $from, 'to' => $to];

        return $this;
    }

    public function setTopic($topic)
    {
        $this->filters['topic'] = $topic;

        return $this;
    }

    /*
     *  Getters
     */
    public function getItems()
    {
        return $this->items;
    }

    // @todo duplicated
    public function getItemsArray()
    {
        return $this->items;
    }

    /*
     * Paging
     */
    public function getEnablePager()
    {
        return $this->pager;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function getCount()
    {
        return $this->count;
    }

    /*
     * Sorting
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /*
     * Filters
     */
    public function getOnline()
    {
        return $this->filters['online'];
    }

    public function getTopic()
    {
        return $this->filters['topic'];
    }

    public function getFilters()
    {
        return $this->filters;
    }
    /*
     * main action
     *
     */
    public function load()
    {
        if (!empty($this->filters['topic'])) {
            $this->qb->leftJoin('p.categoryAssignments', 'c');
        }

        // apply filters
        $this->qb->addFilters($this->getFilters());
        // search
//        $this->qb->addSearch($s);
        // sort
        $this->qb->sort($this->sortBy, $this->sortOrder);

        $query = $this->qb->getQuery();

        if ($this->limit > 0) {
            $query->setMaxResults($this->limit);
        }

        if ($this->pager) {

            $query->setFirstResult($this->limit * ($this->page - 1) + $this->offset);
            $paginator = new Paginator($query);
            $this->count = count($paginator);
            $this->items = $paginator;

        } else {
            $this->items = $query->getResult();
        }

        return $this;
    }
}
