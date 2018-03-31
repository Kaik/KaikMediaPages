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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @Route("/display/{id}", requirements={"id" = "\d+"})
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
    public function displayAction(Request $request, $id = null)
    {
        // access throw component instance user
        $this->get('kaikmedia_pages_module.access_manager')->hasPermission(ACCESS_ADD, true, ':manager:display');

        $page = $this->getDoctrine()
            ->getManager()
            ->getRepository('Kaikmedia\PagesModule\Entity\PageEntity')
            ->getOneBy([
            'id' => $id
        ]);

        if (!$page) {
            throw new NotFoundHttpException();
        }

        return $this->render('KaikmediaPagesModule:Manager:display.html.twig', [
            'page'      => $page,
            'languages' => $this->get('zikula_settings_module.locale_api')->getSupportedLocaleNames(null, $request->getLocale())
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

        $filters = $request->get('page_filter', []);
        //need to unset because form expects it to be an object
        $category = '';
        if (array_key_exists('categoryAssignments', $filters)) {
            $category = $filters['categoryAssignments'];
            unset($filters['categoryAssignments']);
        }
        $limit = array_key_exists('limit', $filters) ? $filters['limit'] : 5;
        $sortBy = array_key_exists('sortby', $filters) ? $filters['sortby'] : $request->query->get('sortby', 'id');
        $sortOrder = array_key_exists('sortorder', $filters) ? $filters['sortorder'] : $request->query->get('sortorder', Column::DIRECTION_DESCENDING);
        $filtered_user_name = $request->get('page_filter_authorSelector', '');

        $languages = $this->get('zikula_settings_module.locale_api')->getSupportedLocaleNames(null, $request->getLocale());
        //todo add layout detection array_merge($filters, ['page' => $page, 'sortby' => $sortBy, 'sortorder' => $sortOrder, 'limit' => $limit])
        $formBuilder = $this->get('form.factory')
            ->createBuilder(PageFilterType::class, array_merge($filters, ['page' => $page, 'sortby' => $sortBy, 'sortorder' => $sortOrder, 'limit' => $limit]), ['locales' => $languages])
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

        $sortableColumns->setOrderBy($sortableColumns->getColumn($sortBy), $sortOrder);
        $sortableColumns->setAdditionalUrlParameters($request->query->all());

        $pages = $this->getDoctrine()
            ->getManager()
            ->getRepository('Kaikmedia\PagesModule\Entity\PageEntity')
            ->getAll(array_merge($filters, ['limit' => $limit, 'page' => $page, 'sortby' => $sortBy, 'sortorder' => $sortOrder, 'categoryAssignments' => $category]));

        return $this->render('KaikmediaPagesModule:Manager:manager.html.twig', [
            'pages'              => $pages,
            'form'               => $form->createView(),
            'limit'              => $limit,
            'filtered_user_name' => $filtered_user_name,
            'sort'               => $sortableColumns->generateSortableColumns(),
            'languages'          => $languages
        ]);
    }


    /**
     * @Route("/modify/{id}", requirements={"id" = "\d+"})
     *
     * @Theme("admin")
     *
     * Modify page.
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
            $page = new PageEntity();
        } else {
            $page = $em->getRepository('Kaikmedia\PagesModule\Entity\PageEntity')->getOneBy([
                'id' => $id
            ]);
        }

        $formBuilder = $this->get('form.factory')
            ->createBuilder(PageType::class, $page)
            ->setMethod('POST');

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em->persist($page);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                    ->add('status', $this->__('Page saved!'));

            $routeUrl = new RouteUrl('kaikmediamodule_page_display', ['urltitle' => $page->getUrltitle()]);
            $this->get('hook_dispatcher')->dispatch('pages.ui_hooks.page.process_edit', new ProcessHook($page->getId(), $routeUrl));

            return $this->redirect($this->generateUrl('kaikmediapagesmodule_manager_display', [
                'id' => $page->getId()
            ]));
        }

        return $this->render('KaikmediaPagesModule:Manager:modify.html.twig', [
            'form' => $form->createView(),
            'page' => $page
        ]);
    }
}
