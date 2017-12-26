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

namespace Kaikmedia\PagesModule\Entity;

use Doctrine\ORM\QueryBuilder;

/**
 * PagesQueryBuilder
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

    public function filterTitle($title)
    {
        if ($title !== false) {
            return $this->andWhere('p.title LIKE :title')->setParameter('title', '%' . $title . '%');
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

    public function filterAuthor($author)
    {
        if ($author !== false) {
            return $this->andWhere('p.author = :author')->setParameter('author', $author);
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
            return $this->andWhere('p.online = :online')->setParameter('online', $online);
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

    public function filterPublished($published)
    {
        switch ($published) {
            case 'published':
                return $this->andWhere($this->expr()
                    ->orx($this->expr()
                    ->lte('p.publishedAt', ':date_now'), $this->expr()
                    ->isNull('p.publishedAt')))
                    ->setParameter('date_now', new \DateTime('now'));
            case 'awaiting':
                return $this->andWhere($this->expr()
                    ->gte('p.publishedAt', ':date_now'))
                    ->setParameter('date_now', new \DateTime('now'));
            case 'unset':
                return $this->andWhere($this->expr()
                    ->isNull('p.publishedAt'));
        }
    }

    public function filterExpired($expired)
    {
        switch ($expired) {
            case 'published':
                return $this->andWhere($this->expr()
                    ->orx($this->expr()
                    ->gte('p.expiredAt', ':date_now'), $this->expr()
                    ->isNull('p.expiredAt')))
                    ->setParameter('date_now', new \DateTime('now'));
            case 'expired':
                return $this->andWhere($this->expr()
                    ->lte('p.expiredAt', ':date_now'))
                    ->setParameter('date_now', new \DateTime('now'));
            case 'awaiting':
                return $this->andWhere($this->expr()
                    ->gte('p.expiredAt', ':date_now'))
                    ->setParameter('date_now', new \DateTime('now'));
            case 'unset':
                return $this->andWhere($this->expr()
                    ->isNull('p.expiredAt'));
        }
    }

    public function filterDeleted($deletedAt)
    {
        if ($deletedAt === false) {
            switch ($deletedAt) {
                case 'deleted':
                    return $this->andWhere($this->expr()
                        ->isNotNull('p.deletedAt'));
                case 'notdeleted':
                    return $this->andWhere($this->expr()
                        ->isNull('p.deletedAt'));
            }
        }
    }

    public function filterCategory($category)
    {
//        dump($category);
//        if ($category !== false) {
//            return $this->andWhere('c.category = :category')->setParameter('category', $category);
//        }
    }

    public function filterLanguage($language)
    {
        if ($language !== false) {
            return $this->andWhere('p.language = :language')->setParameter('language', $language);
        }
    }

    public function filterLayout($layout)
    {
        if ($layout !== false) {
            return $this->andWhere('p.layout = :layout')->setParameter('layout', $layout);
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
