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

use Doctrine\ORM\EntityManager;
//use Doctrine\Common\Collections\AbstractLazyCollection;
use Kaikmedia\PagesModule\Entity\PageEntity;
use Kaikmedia\PagesModule\Helper\CategorisationHelper;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\ExtensionsModule\Api\VariableApi;

/**
 * Page manager
 */
class PageManager
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

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
     * Managed item
     *
     * @var PageEntity
     */
    private $_item;

    public function __construct(
        TranslatorInterface $translator,
        EntityManager $entityManager,
        VariableApi $variableApi,
        CategorisationHelper $categorisationHelper
    ) {
        $this->name = 'KaikmediaPagesModule';
        $this->translator = $translator;
        $this->entityManager = $entityManager;
        $this->variableApi = $variableApi;
        $this->categorisationHelper = $categorisationHelper;
    }

    /**
     * Start managing
     *
     * @return PostManager
     */
    public function getManager($id = null, PageEntity $item = null, $create = true, $slug = null)
    {
        if (isset($item)) {
            // post has been injected
            $this->_item = $item;
        } elseif (is_numeric($id) && $id > 0) {
            // find existing post
            $this->_item = $this->entityManager->find('Kaikmedia\PagesModule\Entity\PageEntity', $id);
        } elseif ($create) {
            // create new post
            $this->_item = new PageEntity();
        } elseif ($slug) {
            // find by slug
            $this->_item = $this->entityManager->getRepository('Kaikmedia\PagesModule\Entity\PageEntity')->findOneBy(['urltitle' => $slug]);
        }

        return $this;
    }

    /**
     * Check if topic exists
     *
     * @return bool
     */
    public function exists()
    {
        return $this->_item ? true : false;
    }

    /**
     * Get the Post entity
     *
     * @return PostEntity
     */
    public function get()
    {
        return $this->_item;
    }

    /**
     * Get post id
     *
     * @return integer
     */
    public function getSlug()
    {
        return $this->_item->getUrltitle();
    }

    /**
     * Get post id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->_item->getId();
    }

    /**
     * Get post as array
     *
     * @return mixed array or false
     */
    public function toArray()
    {
        if (!$this->_item) {
            return [];
        }

        $post = $this->_item->toArray();

        return $post;
    }

    /**
     * Create a post from provided data but do not yet persist
     *
     * @todo Add create validation
     * @todo event
     *
     * @return bool
     */
    public function create($data = null)
    {
//        if (!is_null($data)) {
//            $this->_topic = $this->topicManagerService->getManager($data['topic_id']);
//            $this->_item->setTopic($this->_topic->get());
//            unset($data['topic_id']);
//            $this->_item->merge($data);
//        } else {
//            throw new \InvalidArgumentException($this->translator->__('Cannot create Post, no data provided.'));
//        }
//        $managedForumUser = $this->forumUserManagerService->getManager();
//        $this->_item->setPoster($managedForumUser->get());

        return $this;
    }

    /**
     * Update post
     *
     * @param array/object $data Post data or post object to save
     *
     * @return bool
     */
    public function update($data = null)
    {
        if (is_null($data)) {
            throw new \InvalidArgumentException($this->translator->__('Cannot create page, no data provided.'));
        } elseif ($data instanceof NewsEntity) {
            $this->_item = $data;
        } elseif (is_array($data)) {
            $this->_item->merge($data);
        }

        return $this;
    }

    /**
     * Persist the post and update related entities to reflect new post
     *
     * @todo Add validation ?
     * @todo event
     *
     * @return $this
     */
    public function store()
    {
        $this->entityManager->persist($this->_item);
        $this->entityManager->flush();

        return $this;
    }

    /**
     * Delete post
     *
     * @return $this
     */
    public function delete()
    {
        // preserve post_id
        $itemArray = $this->toArray();
        // remove the post
        $this->entityManager->remove($this->_item);
        $this->entityManager->flush();

        return $itemArray;
    }
}
