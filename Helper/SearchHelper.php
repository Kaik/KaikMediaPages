<?php
/**
 * 
 */

namespace Kaikmedia\PagesModule\Helper;

use Zikula\Core\RouteUrl;
use Zikula\SearchModule\AbstractSearchable;

class SearchHelper extends AbstractSearchable
{
    /**
     * get the UI options for search form
     *
     * @param boolean $active if the module should be checked as active
     * @param array|null $modVars module form vars as previously set
     * @return string
     */
    public function getOptions($active, $modVars = null)
    {
        if (\SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_READ)) {

            return $this->getContainer()->get('templating')->renderResponse('KaikmediaPagesModule:Search:options.html.twig', array('active' => $active))->getContent();
        }

        return '';
    }

    /**
     * Get the search results
     *
     * @param array $words array of words to search for
     * @param string $searchType AND|OR|EXACT
     * @param array|null $modVars module form vars passed though
     * @return array
     */
    function getResults(array $words, $searchType = 'AND', $modVars = null)
    {
        if (!\SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_READ)) {
            return array();
        }

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('p')->from('Kaikmedia\PagesModule\Entity\PageEntity', 'p');
        $whereExpr = $this->formatWhere($qb, $words, array('p.title', 'p.content'), $searchType);
        $qb->andWhere($whereExpr);
        $pages = $qb->getQuery()->getResult();

        $sessionId = session_id();
        $enableCategorization = \ModUtil::getVar($this->name, 'enablecategorization');

        $records = array();
        foreach ($pages as $page) {
            /** @var $page \Zikula\PagesModule\Entity\PageEntity */

            $pagePermissionCheck = \SecurityUtil::checkPermission($this->name . '::', $page->getTitle() . '::' . $page->getPageid(), ACCESS_OVERVIEW);
            if ($enableCategorization) {
                $pagePermissionCheck = $pagePermissionCheck && \CategoryUtil::hasCategoryAccess($page->getCategories(), $this->name);
            }
            if (!$pagePermissionCheck) {
                continue;
            }

            $records[] = array(
                'title' => $page->getTitle(),
                'text' => $page->getContent(),
                'created' => $page->getCr_date(),
                'module' => $this->name,
                'sesid' => $sessionId,
                'url' => RouteUrl::createFromRoute('zikulapagesmodule_user_display', array('urltitle' => $page->getUrltitle()))
            );
        }

        return $records;
    }

}