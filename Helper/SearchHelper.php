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

use Doctrine\ORM\EntityManagerInterface;
use Kaikmedia\PagesModule\Security\AccessManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Zikula\CategoriesModule\Api\ApiInterface\CategoryPermissionApiInterface;
use Zikula\Core\RouteUrl;
use Zikula\SearchModule\Entity\SearchResultEntity;
use Zikula\SearchModule\SearchableInterface;

class SearchHelper implements SearchableInterface
{
    /**
     * @var bool
     */
    private $enableCategorization;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CategoryPermissionApiInterface
     */
    private $categoryPermissionApi;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var AccessManager
     */
    private $accessManager;

    /**
     * SearchHelper constructor.
     * @param EntityManagerInterface $entityManager
     * @param CategoryPermissionApiInterface $categoryPermissionApi
     * @param SessionInterface $session
     * @param bool $enableCategorization
     * @param AccessManager $accessManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CategoryPermissionApiInterface $categoryPermissionApi,
        SessionInterface $session,
        $enableCategorization,
        AccessManager $accessManager
    ) {
        $this->entityManager = $entityManager;
        $this->categoryPermissionApi = $categoryPermissionApi;
        $this->session = $session;
        $this->enableCategorization = $enableCategorization;
        $this->accessManager = $accessManager;
    }

    /**
     * {@inheritdoc}
     */
    public function amendForm(FormBuilderInterface $form)
    {
        // not needed because `active` child object is already added and that is all that is needed.
    }

    /**
     * {@inheritdoc}
     */
    public function getResults(array $words, $searchType = 'AND', $modVars = null)
    {
        if (!$this->accessManager->hasPermission(ACCESS_READ, false)) {
            return [];
        }
        $method = ('OR' == $searchType) ? 'orX' : 'andX';
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('p')
            ->from('Kaikmedia\PagesModule\Entity\PageEntity', 'p');
        /** @var $where \Doctrine\ORM\Query\Expr\Composite */
        $where = $qb->expr()->$method();
        $i = 1;
        foreach ($words as $word) {
            $subWhere = $qb->expr()->orX();
            foreach (['p.title', 'p.content'] as $field) {
                $expr = $qb->expr()->like($field, "?$i");
                $subWhere->add($expr);
                $qb->setParameter($i, '%' . $word . '%');
                $i++;
            }
            $where->add($subWhere);
        }
        $qb->andWhere($where);
        $pages = $qb->getQuery()->getResult();
        $results = [];
        /** @var $pages \Zikula\PagesModule\Entity\PageEntity[] */
        foreach ($pages as $page) {
            $pagePermissionCheck = $this->accessManager->hasPermission(ACCESS_OVERVIEW, false, '::', $page->getTitle() . '::' . $page->getId());
            if ($this->enableCategorization) {
                $pagePermissionCheck = $pagePermissionCheck && $this->categoryPermissionApi->hasCategoryAccess($page->getCategoryAssignments()->toArray());
            }
            if (!$pagePermissionCheck) {
                continue;
            }
            $result = new SearchResultEntity();
            $result->setTitle($page->getTitle())
                ->setText($page->getContent())
                ->setModule('KaikmediaPagesModule')
                ->setCreated($page->getCreatedAt())
                ->setUrl(RouteUrl::createFromRoute('kaikmediapagesmodule_page_display', ['urltitle' => $page->getUrltitle()]))
                ->setSesid($this->session->getId());
            $results[] = $result;
        }
        return $results;
    }
    public function getErrors()
    {
        return [];
    }
}