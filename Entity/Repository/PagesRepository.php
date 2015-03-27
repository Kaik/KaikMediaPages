<?php


namespace Kaikmedia\PagesModule\Entity\Repository;

use ServiceUtil;
use Doctrine\ORM\EntityRepository;
use Kaikmedia\PagesModule\Entity\PagesQueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PagesRepository extends EntityRepository
{

    public function build()
    {
       $em = ServiceUtil::getService('doctrine.entitymanager'); 
       $qb = new PagesQueryBuilder($em); 
       return $qb;
    }
    
    /**
     * Get all or count all
     *
     * @param integer $id
     */
    public function getOrCountAll($onlyone = false, $countonly = false, $r, $f, $s, $sortby, $sortorder, $startnum = 1, $itemsperpage = 15)
    {           
    
        $qb = $this->build();    
        $qb->select('p');  
        $qb->from('Kaikmedia\Zikula\Module\PagesModule\Entity\PagesEntity', 'p'); 
        //restrictions
        $qb->addFilters($r);
        //filters
        $qb->addFilters($f);
        //search
        $qb->addSearch($s);
        //sort
        $qb->sort($sortby,$sortorder);   
        
        $query = $qb->getQuery();
        
        if ($onlyone){
            $item = $query->getOneOrNullResult();  
            return $item;          
        }

        $query->setFirstResult($startnum -1)->setMaxResults($itemsperpage);        
        
        if ($countonly){
            $paginator = new Paginator($query, false);
            $items = $paginator->count(); 
        }else{
            $items = $query->getResult();  
        }
        
        return $items;
    }
    
    public function getAll($args) {
        
        //internal switch
        $countonly = isset($args['countonly']) ? $args['countonly'] : false;
        $onlyone = isset($args['onlyone']) ? $args['onlyone'] : false;
        //pager
        $startnum = isset($args['startnum']) ? $args['startnum'] : 1;
        $startnum      = $startnum < 1 ? 1 : $startnum;
        $itemsperpage = isset($args['itemsperpage']) ? $args['itemsperpage'] : 25;    
        //sort
        $sortby = isset($args['sortby']) ? $args['sortby'] : 'publishdate';    
        $sortorder = isset($args['sortorder']) ? $args['sortorder'] : 'DESC';     
        //publishing specyfic restrictions
        $r['depot'] = isset($args['depot']) && $args['depot'] !== '' ? $args['depot'] : false;
        $r['online'] = isset($args['online']) && $args['online'] !== '' ? $args['online'] : false;
        $r['publishdate'] = isset($args['publishdate']) && $args['publishdate'] !== '' ? $args['publishdate'] : false;
        $r['expiredate'] = isset($args['expiredate']) && $args['expiredate'] !== '' ? $args['expiredate'] : false;
        $r['showinmenu'] = isset($args['showinmenu']) && $args['showinmenu'] !== '' ? $args['showinmenu'] : false;
        $r['showinlist'] = isset($args['showinlist']) && $args['showinlist'] !== '' ? $args['showinlist'] : false;
        //soft delete
        $r['deleted'] = isset($args['deleted']) && $args['deleted'] !== '' ? $args['deleted'] : false;
        //filter's
        $f['author'] = isset($args['author']) && $args['author'] !== '' ? $args['author'] : false;
        $f['revision'] = isset($args['revision']) && $args['revision'] !== '' ? $args['revision'] : false;
        $f['id'] = isset($args['id']) && $args['id'] !== '' ? $args['id'] : false;
        $f['pid'] = isset($args['pid']) && $args['pid'] !== '' ? $args['pid'] : false;
        $f['title'] = isset($args['title']) && $args['title'] !== '' ? $args['title'] : false;
        $f['urltitle'] = isset($args['urltitle']) && $args['urltitle'] !== '' ? $args['urltitle'] : false;
        $f['hitcount'] = isset($args['hitcount']) && $args['hitcount'] !== '' ? $args['hitcount'] : false;
        $f['language'] = isset($args['language']) && $args['language'] !== '' ? $args['language'] : false;
        //search
        $s['search'] = isset($args['search']) && $args['search'] !== '' ? $args['search'] : false;
        $s['search_field'] = isset($args['search_field']) && $args['search_field'] !== '' ? $args['search_field'] : false;
        
        
       return $this
          ->getOrCountAll($onlyone, $countonly, $r, $f, $s, $sortby, $sortorder, $startnum, $itemsperpage); 
  
    }
    
    public function getCount($a) {
       
       $a['countonly'] = true;
       return $this
          ->getAll($a); 
    }
    
    public function getOneBy($a){ 
        
        $a['onlyone'] = true;
        return $this
          ->getAll($a);        
    }
    
    
    public function getMaxPid(){     
        
        $qb = $this->build();    
        $qb->select($qb->expr()->max('p.pid'));  
        $qb->from('Kaikmedia\PagesModule\Entity\PagesEntity', 'p'); 
        $query = $qb->getQuery();
        $item = $query->getOneOrNullResult();
        return $item;   
    }
    
}
