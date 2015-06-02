<?php
/**
 * Copyright (c) KaikMedia.com 2014
 */
namespace Kaikmedia\PagesModule\Entity\Repository;

use ServiceUtil;
use Doctrine\ORM\EntityRepository;
use Kaikmedia\PagesModule\Entity\PagesQueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ImagesRepository extends EntityRepository
{

    /**
     * Query builder helper
     * 
     * @return \Kaikmedia\KmdModule\Entity\CustomersQueryBuilder
     */
    public function build()
    {
        $em = ServiceUtil::getService('doctrine.entitymanager');
        $qb = new PagesQueryBuilder($em);
        return $qb;
    }

    /**
     * Repository
     * 
     * @param integer $page
     *            Current page (defaults to 1)
     * @param integer $limit
     *            The total number per page
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getOneOrAll($onlyone = false, $f, $s, $sortby, $sortorder, $page = 1, $limit)
    {
        $qb = $this->build();
        $qb->select('i');
        $qb->from('Kaikmedia\PagesModule\Entity\ImageEntity', 'i');
        // filters
        $qb->addFilters($f);
        // search
        $qb->addSearch($s);
        // sort
        $qb->sort($sortby, $sortorder);
        
        $query = $qb->getQuery();
        
        if ($onlyone) {
            $item = $query->getOneOrNullResult();
            return $item;
        }
        $paginator = $this->paginate($query, $page, $limit);
        
        return $paginator;
    }

    /**
     * Paginator Helper
     * Pass through a query object, current page & limit
     * the offset is calculated from the page and limit
     * returns an `Paginator` instance, which you can call the following on:
     * $paginator->getIterator()->count() # Total fetched (ie: `5` posts)
     * $paginator->count() # Count of ALL posts (ie: `20` posts)
     * $paginator->getIterator() # ArrayIterator
     * 
     * @param Doctrine\ORM\Query $dql
     *            DQL Query Object
     * @param integer $page
     *            Current page (defaults to 1)
     * @param integer $limit
     *            The total number per page (defaults to 5)
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function paginate($dql, $page = 1, $limit = 15)
    {
        $paginator = new Paginator($dql);
        
        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit); // Limit
        
        return $paginator;
    }

    /**
     * Get all in one function
     * 
     * @param array $args            
     * @param integer $onlyone
     *            Internal switch
     * @param integer $page
     *            Current page
     * @param integer $limit
     *            The total number per page
     * @return \Doctrine\ORM\Tools\Pagination\Paginator or
     *         object
     */
    public function getAll($args = array())
    {
        // internall
        $onlyone = isset($args['onlyone']) ? $args['onlyone'] : false;
        // pager
        $page = isset($args['page']) ? $args['page'] : 1;
        $page = $page < 1 ? 1 : $page;
        $limit = isset($args['limit']) ? $args['limit'] : 25;
        // sort
        $sortby = isset($args['sortby']) ? $args['sortby'] : 'createdAt';
        $sortorder = isset($args['sortorder']) ? $args['sortorder'] : 'DESC';
        // filter's
        $f['id'] = isset($args['id']) && $args['id'] !== '' ? $args['id'] : false;
        $f['urltitle'] = isset($args['urltitle']) && $args['urltitle'] !== '' ? $args['urltitle'] : false;
        $f['title'] = isset($args['title']) && $args['title'] !== '' ? $args['title'] : false;
        
        // search
        $s['search'] = isset($args['search']) && $args['search'] !== '' ? $args['search'] : false;
        $s['search_field'] = isset($args['search_field']) && $args['search_field'] !== '' ? $args['search_field'] : false;
        
        return $this->getOneOrAll($onlyone, $f, $s, $sortby, $sortorder, $page, $limit);
    }

    /**
     * Shortcut to get one item
     * 
     * @param array $args            
     * @param integer $onlyone
     *            Internal switch
     * @param integer $page
     *            Current page
     * @param integer $limit
     *            The total number per page
     * @return \Doctrine\ORM\Tools\Pagination\Paginator or
     *         object
     */
    public function getOneBy($a)
    {
        // set internall
        $a['onlyone'] = true;
        return $this->getAll($a);
    }
}