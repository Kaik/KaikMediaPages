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

namespace Kaikmedia\PagesModule\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Kaikmedia\PagesModule\Entity\PagesQueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PagesRepository extends EntityRepository
{
    /**
     * Query builder helper
     *
     * @return \Kaikmedia\KmdModule\Entity\CustomersQueryBuilder
     */
    public function build()
    {
        $qb = new PagesQueryBuilder($this->_em);
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
    public function getOneOrAll($onlyone = false, $f, $s, $sortby, $sortorder, $page, $limit)
    {
        $qb = $this->build();
        $qb->select('p');
        $qb->from('Kaikmedia\PagesModule\Entity\PageEntity', 'p');
        $qb->leftJoin('p.categoryAssignments', 'c');
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
    public function paginate($dql, $page = 1, $limit = 5)
    {
        $paginator = new Paginator($dql);
        $paginator->getQuery()
            ->setFirstResult($page - 1)
            ->setMaxResults($limit);

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
    public function getAll($args = [])
    {
        // internall
        dump($args);
        $onlyone = isset($args['onlyone']) ? $args['onlyone'] : false;
        // pager
        $page = (isset($args['page']) && $args['page'] > 1) ? $args['page'] : 1;
        $limit = isset($args['limit']) ? $args['limit'] : 25;
        // sort
        $sortby = isset($args['sortby']) ? $args['sortby'] : 'id';
        $sortorder = isset($args['sortorder']) ? $args['sortorder'] : 'DESC';
        // filter's - sql = exact search apart from title field
        // basics
        $f['id']        = isset($args['id']) && $args['id'] !== '' ? $args['id'] : false;
        $f['title']     = isset($args['title']) && $args['title'] !== '' ? $args['title'] : false;
        $f['urltitle']  = isset($args['urltitle']) && $args['urltitle'] !== '' ? $args['urltitle'] : false;
        $f['author']    = isset($args['author']) && $args['author'] !== '' && $args['author'] != 0 ? $args['author'] : false;
        // statuses
        $f['online']    = isset($args['online']) && $args['online'] !== '' ? $args['online'] : false;
        $f['depot']     = isset($args['depot']) && $args['depot'] !== '' ? $args['depot'] : false;
        $f['showinmenu'] = isset($args['inmenu']) && $args['inmenu'] !== '' ? $args['inmenu'] : false;
        $f['showinlist'] = isset($args['inlist']) && $args['inlist'] !== '' ? $args['inlist'] : false;
        // dates
        $f['expired']   = isset($args['expired']) && $args['expired'] !== '' ? $args['expired'] : false;
        $f['published'] = isset($args['published']) && $args['published'] !== '' ? $args['published'] : false;
        $f['deleted']   = isset($args['deleted']) && $args['deleted'] !== '' ? $args['deleted'] : false;
        // other
        $f['category']  = isset($args['categoryAssignments']) && $args['categoryAssignments'] !== '' ? $args['categoryAssignments'] : false;
        $f['language']  = isset($args['language']) && $args['language'] !== '' ? $args['language'] : false;
        $f['layout']  = isset($args['layout']) && $args['layout'] !== '' ? $args['layout'] : false;

        // search sql like%
        $s['search']        = isset($args['search']) && $args['search'] !== '' ? $args['search'] : false;
        $s['search_field']  = isset($args['search_field']) && $args['search_field'] !== '' ? $args['search_field'] : false;



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
