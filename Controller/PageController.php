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

namespace Kaikmedia\PagesModule\Controller;

use Kaikmedia\PagesModule\Entity\PageEntity;
use Kaikmedia\PagesModule\Form\Type\PageType;
use Kaikmedia\PagesModule\Form\Type\PageFilterType;
use Kaikmedia\PagesModule\Helper\LayoutHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route; // used in annotations - do not remove
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method; // used in annotations - do not remove
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Zikula\Core\Controller\AbstractController;

/**
 *
 */
class PageController extends AbstractController
{
    /**
     * @Route("")
     *
     * Display pages list.
     *
     * @throws AccessDeniedException on failed permission check
     */
    public function homeAction(Request $request)
    {
        // access throw component instance user
        $this->get('kaikmedia_pages_module.access_manager')->hasPermission(ACCESS_OVERVIEW, true, '::view');

        $categorisationHelper = $this->get('kaikmedia_pages_module.helper.categorisation_helper');
        $topicMainCategory = $categorisationHelper->getTopicRegistryCategory();
        $catsAndPages = [];
        foreach ($topicMainCategory->getChildren() as $category) {
            $catsAndPages[$category->getId()]['category'] = $category;
            $pagesCollection = $this->get('kaikmedia_pages_module.pages_collection_manager');
            $pagesCollection
                ->buildCollection()
                ->setSortBy('publishedAt')
                ->setSortOrder('DESC')
                ->setLimit(false)
                ->setTopic($category->getId())
                ->setEnablePager(false)
            ;
            $catsAndPages[$category->getId()]['pages'] = $pagesCollection->load();
        }

        return $this->render('KaikmediaPagesModule:User:home.html.twig', [
            'catsAndPages'          => $catsAndPages,
            'categorisationHelper'  => $categorisationHelper
        ]);
    }
//        // get latest news - like 4-5 news @todo move to block
//        $latestNews = $this->get('kaikmedia_news_module.news_collection_manager');
//        $latestNews
//            ->buildCollection()
//            ->setSortBy('publishedAt')
//            ->setSortOrder('DESC')
//            ->setLimit(3)
//            ->setTopic(null)
//            ->setEnablePager(false)
//        ;
//
//        //get pager for all news
//        $otherNews = $this->get('kaikmedia_news_module.news_collection_manager');
//        $otherNews
//            ->buildCollection()
//            ->setOffset(3)
//            ->setLimit(21)
//            ->setTopic(null)
//            ->setEnablePager(true)
//        ;

    /**
     * @Route("/{topic_name}/{slug}/{id}")
     *
     */
    public function displayAction($topic_name = null, $id = null, $slug = null)
    {
        // access throw component instance user
        $this->get('kaikmedia_pages_module.access_manager')->hasPermission(ACCESS_OVERVIEW, true, '::display');

        $categorisationHelper = $this->get('kaikmedia_pages_module.helper.categorisation_helper');
        //this is due to old urls where we want to make sure there is no duplicate
        // there was no id in bettween and I had to add it so this is why it looks like it
        $article = $this->get('kaikmedia_pages_module.page_manager')->getManager($id, null, false,  is_numeric($id) || $id === null ? $slug : $id);

        if ($article->exists()) {
            // 301 redirect to right one in case bad
            $parameters = ['topic_name'     => $categorisationHelper->getTopicNameByArticle($article->get()),
                            'slug'          => $article->getSlug()
            ];

            $thisUrl = $this->get('router')->generate('kaikmediapagesmodule_page_display', $parameters, RouterInterface::ABSOLUTE_URL);

            if ($article->getId() != $id) {
//                return new RedirectResponse($thisUrl, 301);
            } elseif ($article->getSlug() != $slug) {
                return new RedirectResponse($thisUrl, 301);
            } elseif ($categorisationHelper->getTopicNameByArticle($article->get()) != $topic_name) {
                return new RedirectResponse($thisUrl, 301);
            }

        } else {
            throw new NotFoundHttpException($this->__f('Page %n not found.', ['%n' => $slug]));
        }

//        $article->get()->incrementViews();
//        $article->store();

        return $this->render('KaikmediaPagesModule:User:display.html.twig', [
            'page'                  => $article->get(),
            'categorisationHelper'  => $this->get('kaikmedia_pages_module.helper.categorisation_helper'),
            'layoutHelper'          => new LayoutHelper()
        ]);
    }
//    /**
//     * @Route("/{urltitle}")
//     *
//     * Display item.
//     *
//     * @throws AccessDeniedException on failed permission check
//     */
//    public function displayAction(Request $request, $urltitle)
//    {
//        // access throw component instance user
//        $this->get('kaikmedia_pages_module.access_manager')->hasPermission(ACCESS_OVERVIEW, true, '::display');
//
//        $a = [];
//        $a['online'] = 1;
//        $a['urltitle'] = $urltitle;
////        $a['language'] = $this->container->get('translator')->getLocale();
//
//        $page = $this->getDoctrine()
//            ->getManager()
//            ->getRepository('Kaikmedia\PagesModule\Entity\PageEntity')
//            ->getOneBy($a);
//
//        if (!$page) {
//            throw new NotFoundHttpException();
//        }
//
//        return $this->render('KaikmediaPagesModule:User:display.html.twig', [
//            'page' => $page
//        ]);
//    }

}
