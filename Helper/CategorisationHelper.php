<?php

/**
 * KaikMedia PagesModule
 *
 * @package    KaikmediaPagesModule
 * @author     Kaik <contact@kaikmedia.com>
 * @copyright  KaikMedia
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @link       https://github.com/Kaik/KaikMediaNews.git
 */

namespace Kaikmedia\PagesModule\Helper;

use Doctrine\ORM\EntityManagerInterface;

use Kaikmedia\PagesModule\Entity\PageEntity;
use Kaikmedia\PagesModule\Security\AccessManager;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Zikula\CategoriesModule\Api\ApiInterface\CategoryPermissionApiInterface;
use Zikula\CategoriesModule\Entity\CategoryRegistryEntity;
use Zikula\CategoriesModule\Entity\RepositoryInterface\CategoryRegistryRepositoryInterface;
use Zikula\CategoriesModule\Entity\RepositoryInterface\CategoryRepositoryInterface;


class CategorisationHelper
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
     * @var CategoryRegistryRepositoryInterface
     */
    private $categoryRegistryRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

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
        AccessManager $accessManager,
        CategoryRegistryRepositoryInterface $categoryRegistryRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->entityManager = $entityManager;
        $this->categoryPermissionApi = $categoryPermissionApi;
        $this->session = $session;
        $this->enableCategorization = $enableCategorization;
        $this->accessManager = $accessManager;
        $this->categoryRegistryRepository = $categoryRegistryRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /*
     * Topic
     */
    public function getTopicRegistry()
    {
        return $this->categoryRegistryRepository->findOneBy([
            'modname'    => 'KaikmediaPagesModule',
            'entityname' => 'PageEntity',
            'property'   => 'Topic'
        ]);
    }

    public function getTopicRegistryCategory()
    {
        return $this->getTopicRegistry() instanceof CategoryRegistryEntity
            ? $this->getTopicRegistry()->getCategory()
            : null;
    }

    public function getTopics()
    {
        $topicsParent = $this->getTopicRegistryCategory();
        if (empty($topicsParent)) {
            return [];
        }

        return $this->categoryRepository->getChildren($topicsParent);
    }

    public function getTopicByName($topic_name)
    {
        foreach ($this->getTopics() as $topic) {
            if ($topic->getName() == $topic_name) {
                return $topic;
            }
        }

        return false;
    }

    public function getTopicByArticle(PageEntity $article)
    {
        $topicRegistry = $this->getTopicRegistry();

        return $article->getCategoryForRegistryId($topicRegistry->getId());
    }

    public function getTopicNameByArticle(PageEntity $article)
    {
        $topicRegistry = $this->getTopicRegistry();

        return $article->getCategoryNameForRegistryId($topicRegistry->getId());
    }

    public function getTopicTitleByArticle(PageEntity $article, $locale = 'en')
    {
        $topic = $this->getTopicByArticle($article);
        if (!$topic) {
            return null;
        }
        // attribute(topic.display_name, app.request.locale) is defined ? attribute(topic.display_name, app.request.locale) : 'global' 
        
        if (array_key_exists($locale, $topic->getDisplayName())) {
            return $topic->getDisplayName()[$locale];
        }
        
        return $topic->getName();
    }

    public function getTopicAttributeByArticle(PageEntity $article, $attribute = false)
    {
        if (!$attribute) {
            return null;
        }    
        
        $topic = $this->getTopicByArticle($article);
        if (!$topic) {
            return null;
        }    
        
        $topicAttributes = $topic->getAttributes();
        if ($topicAttributes->offsetExists($attribute)) {
            return $topicAttributes->offsetGet($attribute);
        }
        
        return null;
    }
}
