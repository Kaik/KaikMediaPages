<?php
/**
 * Copyright (c) KaikMedia.com 2014
 */
namespace Kaikmedia\PagesModule\Entity;

use UserUtil;
use Doctrine\ORM\QueryBuilder;

/**
 * Description of PagesQueryBuilder
 * 
 * @author Kaik
 */
class PagesQueryBuilder extends QueryBuilder
{

    public function filterId($id)
    {
        if (is_array($id)) {
            return $this->andWhere('p.id IN (:id)')->setParameter('id', $id);
        } elseif ($id !== false) {
            return $this->andWhere('p.id = :id')->setParameter('id', $id);
        }
    }

    public function filterUrltitle($urltitle)
    {
        if (is_array($urltitle)) {
            return $this->andWhere('p.urltitle IN (:urltitle)')->setParameter('urltitle', $urltitle);
        } elseif ($urltitle !== false) {
            return $this->andWhere('p.urltitle = :urltitle')->setParameter('urltitle', $urltitle);
        }
    }

    public function filterDepot($depot)
    {
        if ($depot !== false) {
            return $this->andWhere('p.depot = :depot')->setParameter('depot', $depot);
        }
    }

    public function filterOnline($online)
    {
        if ($online !== false) {
            switch ($online) {
                case 'all':
                    return $this;
                case 'online':
                    return $this->andWhere('p.online = :online')->setParameter('online', 1);
                case 'offline':
                    return $this->andWhere('p.online = :online')->setParameter('online', 0);
            }
        }
    }

    public function filterPublished($published)
    {
        switch ($published) {
            case 'published':
                return $this->andWhere($this->expr()
                    ->orx($this->expr()
                    ->lte('p.published', ':date_now'), $this->expr()
                    ->isNull('p.published')))
                    ->setParameter('date_now', new \DateTime('now'));
            case 'awaiting':
                return $this->andWhere($this->expr()
                    ->gte('p.published', ':date_now'))
                    ->setParameter('date_now', new \DateTime('now'));
            case 'unset':
                return $this->andWhere($this->expr()
                    ->isNull('p.published'));
        }
    }

    public function filterExpired($expired)
    {
        switch ($expired) {
            case 'published':
                return $this->andWhere($this->expr()
                    ->orx($this->expr()
                    ->gte('p.expired', ':date_now'), $this->expr()
                    ->isNull('p.expired')))
                    ->setParameter('date_now', new \DateTime('now'));
            case 'expired':
                return $this->andWhere($this->expr()
                    ->lte('p.expired', ':date_now'))
                    ->setParameter('date_now', new \DateTime('now'));
            case 'awaiting':
                return $this->andWhere($this->expr()
                    ->gte('p.expired', ':date_now'))
                    ->setParameter('date_now', new \DateTime('now'));
            case 'unset':
                return $this->andWhere($this->expr()
                    ->isNull('p.expired'));
        }
    }

    public function filterShowinlist($inlist)
    {
        if ($inlist !== false) {
            return $this->andWhere('p.inlist = :inlist')->setParameter('inlist', $inlist);
        }
    }

    public function filterShowinmenu($inmenu)
    {
        if ($inmenu !== false) {
            return $this->andWhere('p.inmenu = :inmenu')->setParameter('inmenu', $inmenu);
        }
    }

    public function filterLanguage($language)
    {
        if ($language !== false) {
            return $this->andWhere('p.language = :language')->setParameter('language', $language);
        }
    }

    public function filterViews($views)
    {
        if ($views !== false) {
            return $this->andWhere('p.views = :views')->setParameter('views', $views);
        }
    }

    public function filterAuthor($author)
    {
        if ($author !== false) {
            return $this->andWhere('p.author = :author')->setParameter('author', $author);
        }
    }

    public function filterTitle($title)
    {
        if ($title !== false) {
            return $this->andWhere('p.title = :title')->setParameter('title', $title);
        }
    }

    public function filterDeleted($deletedAt)
    {
        if ($deletedAt === false) {
            return $this->andWhere($this->expr()
                ->isNull('p.deletedAt'));
        }
    }

    public function addFilter($field, $filter)
    {
        $fn = 'filter' . ucfirst($field);
        if (method_exists($this, $fn)) {
            $this->$fn($filter);
        }
    }

    public function addFilters($f)
    {
        foreach ($f as $field => $filter) {
            $this->addFilter($field, $filter);
        }
    }

    public function addSearch($s)
    {
        $search = $s['search'];
        $search_field = $s['search_field'];
        
        if ($search === false || $search_field === false) {
            return;
        }
        
        switch ($search_field) {
            case 'author':
                if (is_numeric($search)) {
                    return $this->filterAuthor($search);
                } elseif (is_string($search)) {
                    $uid = UserUtil::getIdFromName($search);
                    $uid = $uid !== false ? $uid : 0;
                    return $this->filterAuthor($uid);
                }
                break;
            case 'title':
                return $this->andWhere('p.title LIKE :search')->setParameter('search', '%' . $search . '%');
            case 'urltitle':
                return $this->andWhere('p.urltitle LIKE :search')->setParameter('search', '%' . $search . '%');
        }
    }

    public function sort($sortBy, $sortOrder)
    {
        return $this->orderBy('p.' . $sortBy, $sortOrder);
    }
}
