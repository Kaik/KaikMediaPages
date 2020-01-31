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
use Kaikmedia\PagesModule\Form\Type\Manager\PageManagerModifyType;
use Kaikmedia\PagesModule\Form\Type\PagesFilterType;
use Kaikmedia\PagesModule\Helper\LayoutHelper;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\RouterInterface;

use Zikula\Bundle\HookBundle\Hook\ProcessHook;
use Zikula\Core\Controller\AbstractController;
use Zikula\Core\RouteUrl;
use Zikula\Component\SortableColumns\SortableColumns;
use Zikula\Component\SortableColumns\Column;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * @Route("/manager")
 */
class ManagerController extends AbstractController
{
    /**
     * @Route("")
     *
     * The default entry point.
     * This redirects back to the default entry point for the Users module.
     *
     * @return RedirectResponse
     */
    public function indexAction()
    {
        return new RedirectResponse($this->get('router')->generate('kaikmediapagesmodule_manager_list', [], RouterInterface::ABSOLUTE_URL));
    }

    /**
     * @Route("/display/{id}/{preview_type}/{preview_name}", 
     *      requirements={"id" = "\d+",
     *                    "preview" = "card|layout"
     *      },
     *      defaults={"preview_type"="layout", "preview_name"= "default"}
     *  )
     * 
     * @Theme("admin")
     *
     * Display page.
     *
     * @param Request $request
     * @param integer $id
     * @return RedirectResponse|string The rendered template output.
     * @throws AccessDeniedException on failed permission check
     */
    public function displayAction(Request $request, $id = null, $preview_type, $preview_name)
    {
        // access throw component instance user
        $this->get('kaikmedia_pages_module.access_manager')->hasPermission(ACCESS_ADD, true, ':manager:display');
        
        $managedPage = $this->get('kaikmedia_pages_module.page_manager')->getManager($id);

        if (!$managedPage->exists()) {
            throw new NotFoundHttpException();
        }

        $layoutHelper = new LayoutHelper();
        //display in article layout
        if ($managedPage->get()->getLayout() && $preview_type == 'layout' && $preview_name == 'default') {
            $preview_name = $managedPage->get()->getLayout();
        }

        return $this->render('KaikmediaPagesModule:Manager:display.html.twig', [
            'article'               => $managedPage->get(),
            'preview_type'          => $preview_type,
            'preview_name'          => $preview_name,
            'languages'             => $this->get('zikula_settings_module.locale_api')->getSupportedLocaleNames(null, $request->getLocale()),
            'categorisationHelper'  => $this->get('kaikmedia_pages_module.helper.categorisation_helper'),
            'layoutHelper'          => $layoutHelper
        ]);
    }
    
    /**
     * @Route("/list/{page}", requirements={"page" = "\d*"}, defaults={"page" = 1})
     *
     * @Theme("admin")
     *
     * The main administration list.
     *
     * @return RedirectResponse
     * @throws AccessDeniedException on failed permission check
     */
    public function listAction(Request $request, $page)
    {
        // access throw component instance user
        $this->get('kaikmedia_pages_module.access_manager')->hasPermission(ACCESS_ADD, true, ':manager:list');

        $default_filters = [
//            'title'   => false,
            'online'    => 'any',
            'depot'     => 'any',
            'inmenu'    => 'any',
            'inlist'    => 'any',
//            'author'  => false,
            'layout'    => 'any',
            'expired'   => 'any',
            'published' => 'any',
            'language'  => 'any',
//            'topic'   => [],
        ];

        $filters_form_data = $request->get('pages_filter', $default_filters);
        $filtered_user_name = $request->get('article_filter_authorSelector', '');

        $pages = $this->get('kaikmedia_pages_module.pages_collection_manager');
        $pages->buildCollection()
            ->setPage($page)
            ->setLimit(array_key_exists('limit', $filters_form_data) ? $filters_form_data['limit'] : 25)
            ->setSortBy(array_key_exists('sortby', $filters_form_data) ? $filters_form_data['sortby'] : $request->query->get('sortby', 'createdAt'))
            ->setSortOrder(array_key_exists('sortorder', $filters_form_data) ? $filters_form_data['sortorder'] : $request->query->get('sortorder', Column::DIRECTION_DESCENDING))
            ->setFilters($filters_form_data)
            ->setEnablePager(true)
        ;

        $formData = array_merge(
            $pages->getFilters(),
            ['page' => $page,
             'sortby' => $pages->getSortBy(),
             'sortorder' => $pages->getSortOrder(),
             'limit' => $pages->getLimit()
            ]
        );

        $languages = $this->get('zikula_settings_module.locale_api')->getSupportedLocaleNames(null, $request->getLocale());

        $formBuilder = $this->get('form.factory')
            ->createBuilder(PagesFilterType::class, $formData, ['locales' => $languages])
            ->setAction($this->get('router')->generate('kaikmediapagesmodule_manager_list', ['page' => $page], RouterInterface::ABSOLUTE_URL))
            ->setMethod('GET');

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        $sortableColumns = new SortableColumns(
            $this->get('router'),
            'kaikmediapagesmodule_manager_list',
            'sortby',
            'sortorder'
        );

        $sortableColumns->addColumns([
            new Column('id'),
            new Column('title'),
            new Column('author'),
            new Column('createdAt')
        ]);

        $sortableColumns->setOrderBy($sortableColumns->getColumn($pages->getSortBy()), $pages->getSortOrder());
        $sortableColumns->setAdditionalUrlParameters($request->query->all());
        
        return $this->render('KaikmediaPagesModule:Manager:manager.html.twig', [
            'pages'                 => $pages->load(),
            'form'                  => $form->createView(),
            'filtered_user_name'    => $filtered_user_name,
            'sort'                  => $sortableColumns->generateSortableColumns(),
            'languages'             => $languages,
            'categorisationHelper'  => $this->get('kaikmedia_pages_module.helper.categorisation_helper'),
        ]);  
        
//        // access throw component instance user
//        $this->get('kaikmedia_pages_module.access_manager')->hasPermission(ACCESS_ADD, true, ':manager:list');
//
//        $filters = $request->get('page_filter', []);
//        //need to unset because form expects it to be an object
//        $category = '';
//        if (array_key_exists('categoryAssignments', $filters)) {
//            $category = $filters['categoryAssignments'];
//            unset($filters['categoryAssignments']);
//        }
//        $limit = array_key_exists('limit', $filters) ? $filters['limit'] : 5;
//        $sortBy = array_key_exists('sortby', $filters) ? $filters['sortby'] : $request->query->get('sortby', 'id');
//        $sortOrder = array_key_exists('sortorder', $filters) ? $filters['sortorder'] : $request->query->get('sortorder', Column::DIRECTION_DESCENDING);
//        $filtered_user_name = $request->get('page_filter_authorSelector', '');
//
//        $languages = $this->get('zikula_settings_module.locale_api')->getSupportedLocaleNames(null, $request->getLocale());
//        //todo add layout detection array_merge($filters, ['page' => $page, 'sortby' => $sortBy, 'sortorder' => $sortOrder, 'limit' => $limit])
//        $formBuilder = $this->get('form.factory')
//            ->createBuilder(PageFilterType::class, array_merge($filters, ['page' => $page, 'sortby' => $sortBy, 'sortorder' => $sortOrder, 'limit' => $limit]), ['locales' => $languages])
//                ->setAction($this->get('router')->generate('kaikmediapagesmodule_manager_list', ['page' => $page], RouterInterface::ABSOLUTE_URL))
//                ->setMethod('GET');
//
//        $form = $formBuilder->getForm();
//        $form->handleRequest($request);
//        $sortableColumns = new SortableColumns(
//            $this->get('router'),
//            'kaikmediapagesmodule_manager_list',
//            'sortby',
//            'sortorder'
//        );
//
//        $sortableColumns->addColumns([
//            new Column('id'),
//            new Column('title'),
//            new Column('author'),
//            new Column('createdAt')
//        ]);
//
//        $sortableColumns->setOrderBy($sortableColumns->getColumn($sortBy), $sortOrder);
//        $sortableColumns->setAdditionalUrlParameters($request->query->all());
//
//        $pages = $this->getDoctrine()
//            ->getManager()
//            ->getRepository('Kaikmedia\PagesModule\Entity\PageEntity')
//            ->getAll(array_merge($filters, ['limit' => $limit, 'page' => $page, 'sortby' => $sortBy, 'sortorder' => $sortOrder, 'categoryAssignments' => $category]));
//
//        return $this->render('KaikmediaPagesModule:Manager:manager.html.twig', [
//            'pages'              => $pages,
//            'form'               => $form->createView(),
//            'limit'              => $limit,
//            'filtered_user_name' => $filtered_user_name,
//            'sort'               => $sortableColumns->generateSortableColumns(),
//            'languages'          => $languages
//        ]);
    }

    /**
     * @Route("/modify/{id}", requirements={"id" = "\d+"})
     *
     * @Theme("admin")
     *
     * Modify article.
     *
     * @param Request $request
     * @param integer $id
     * @return RedirectResponse|string The rendered template output.
     * @throws AccessDeniedException on failed permission check
     */
    public function modifyAction(Request $request, $id = null)
    {
        // access throw component instance user
        $this->get('kaikmedia_pages_module.access_manager')->hasPermission(ACCESS_ADD, true, ':manager:modify');

        $em = $this->getDoctrine()->getManager();
        if ($id == null) {
            $article = new PageEntity();
        } else {
            $article = $em->getRepository('Kaikmedia\PagesModule\Entity\PageEntity')->findOneBy(['id' => $id]);
        }
        
        $installedLanguageNames = $this->get('zikula_settings_module.locale_api')->getSupportedLocaleNames(null, $request->getLocale());
        $layoutHelper = new LayoutHelper();
        $formBuilder = $this->get('form.factory')
            ->createBuilder(PageManagerModifyType::class, 
                $article,
                ['layouts' => $layoutHelper->getLayoutChoices(),
                'locales' => $installedLanguageNames
                ]
            )
            ->setMethod('POST');
        
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em->persist($article);
            $em->flush();

            $request->getSession()
            ->getFlashBag()
            ->add('status', $this->__('Article saved!'));

            $routeUrl = new RouteUrl('kaikmediapagesmodule_manager_display', ['id' => $article->getId()]);
            $this->get('hook_dispatcher')->dispatch('pages.ui_hooks.page.process_edit', new ProcessHook($article->getId(), $routeUrl));
            $this->get('hook_dispatcher')->dispatch('pages.ui_hooks.mainmedia.process_edit', new ProcessHook($article->getId(), $routeUrl));
            $this->get('hook_dispatcher')->dispatch('pages.ui_hooks.gallery.process_edit', new ProcessHook($article->getId(), $routeUrl));

//            return $this->redirect($this->generateUrl('kaikmediapagesmodule_manager_display', [
//                'id' => $article->getId()
//            ]));
        }

        return $this->render('KaikmediaPagesModule:Manager:modify.html.twig', [
            'form'                  => $form->createView(),
            'article'               => $article,
            'categorisationHelper'  => $this->get('kaikmedia_pages_module.helper.categorisation_helper'),
            'layoutHelper'          => $layoutHelper,
        ]);
    }
    
    /**
     * @Route("/toggle/{id}/{property}", 
     *      requirements={"id" = "\d+",
     *                    "property" = "online|depot|inmenu|inlist"
     *      },
     *      options={"expose"=true}
     *  )
     *
     * @Theme("admin")
     *
     * Modify article.
     *
     * @param Request $request
     * @param integer $id
     * @return RedirectResponse|string The rendered template output.
     * @throws AccessDeniedException on failed permission check
     */
    public function toggleAction(Request $request, PageEntity $item, $property)
    {
        // access throw component instance user
        $this->get('kaikmedia_pages_module.access_manager')->hasPermission(ACCESS_ADD, true, ':manager:toggle');
        
        $data = [];
        $data['id'] = $item->getId();
        $data['property'] = $property;
        $data['status'] = true;
        
        switch ($property) {
            case 'online':
                $data['value'] = !$item->getOnline();
                $item->setOnline($data['value']);
                break;
            case 'depot':
                $data['value'] = !$item->getDepot();
                $item->setDepot($data['value']);
                break;
            case 'inmenu':
                $data['value'] = !$item->getDepot();
                $item->setInmenu($data['value']);
                break;
            case 'inlist':
                $data['value'] = !$item->getInlist();
                $item->setInlist($data['value']);
                break;
            default :
                $data['status'] = false;
                break;
        }
        
        if ($data['status']) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }
        
        return new Response(json_encode($data));
    }
    
    /**
     * @Route("/update/{id}", 
     *      requirements={"id" = "\d+"},
     *      options={"expose"=true}
     *  )
     *
     * @Theme("admin")
     *
     * Modify article.
     *
     * @param Request $request
     * @param integer $id
     * @return RedirectResponse|string The rendered template output.
     * @throws AccessDeniedException on failed permission check
     */
    public function updateAction(Request $request, NewsEntity $item)
    {
        // access throw component instance user
        $this->get('kaikmedia_pages_module.access_manager')->hasPermission(ACCESS_ADD, true, ':manager:update');
        
        $data = [];
        $content = $request->getContent();
        if (empty($content)) {
            $data['status'] = false;
            
            goto display;
        }
        
        $data = json_decode($content, true); // 2nd param to get as array
        $data['status'] = true;
        if (!array_key_exists('name', $data)) {
            $data['status'] = false;
            
            goto display;
        }
        
        switch ($data['name']) {
            case 'language':
                $item->setLanguage($data['value']);
                
                break;
            case 'layout':
                $item->setLayout($data['value']);

            case 'author':
                
//                $item->setLayout($data['value']);
                
                break;

            case 'topic':
                
//                $item->setLayout($data['value']);
                
                break;
            
            default :
                $data['status'] = false;
                break;
        }
        
        if ($data['status']) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }
        
        display:
        
        return new Response(json_encode($data));
    }
}
