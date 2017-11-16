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

use Zikula\Core\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route; // used in annotations - do not remove
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method; // used in annotations - do not remove
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Kaikmedia\PagesModule\Entity\PageEntity;
use Kaikmedia\PagesModule\Form\Type\PageType;
use Kaikmedia\PagesModule\Form\Type\PageFilterType;
//use Kaikmedia\GalleryModule\Manager\Plugin as GalleryPlugin;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/index")
     * the main administration function
     *
     * @return RedirectResponse
     */
    public function indexAction(Request $request)
    {
//        // Security check
//        if (!\SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
//            throw new AccessDeniedException();
//        }

        return new RedirectResponse($this->get('router')->generate('kaikmediapagesmodule_admin_manager', [], RouterInterface::ABSOLUTE_URL));
    }

    /**
     * @Route("/manager/{page}", requirements={"page" = "\d*"}, defaults={"page" = 1})
     * the main administration function
     *
     * @return RedirectResponse
     */
    public function managerAction(Request $request, $page)
    {
//        // Security check
//        if (!\SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
//            throw new AccessDeniedException();
//        }
        $a = [];
        // Get startnum and perpage parameter for pager
        $a['page'] = $page;
        $a['limit'] = $request->query->get('limit', 15);
        $a['title'] = $request->query->get('title', '');
        $a['online'] = $request->query->get('online', '');
        $a['depot'] = $request->query->get('depot', '');
        $a['inlist'] = $request->query->get('inlist', '');
        $a['inmenu'] = $request->query->get('inmenu', '');
        $a['language'] = $request->query->get('language', '');
        $a['layout'] = $request->query->get('layout', '');
        $a['author'] = $request->query->get('author', '');

        $formBuilder = $this->get('form.factory')->createBuilder(PageFilterType::class, $a)
                ->setAction($this->get('router')->generate('kaikmediapagesmodule_admin_manager', [], RouterInterface::ABSOLUTE_URL))
                ->setMethod('GET');

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $a['limit'] = $data['limit'] ? $data['limit'] : $a['limit'];
            $a['title'] = $data['title'] ? $data['title'] : $a['title'];
            $a['online'] = $data['online'] ? $data['online'] : $a['online'];
            $a['depot'] = $data['depot'] ? $data['depot'] : $a['depot'];
            $a['inlist'] = $data['inlist'] ? $data['inlist'] : $a['inlist'];
            $a['inmenu'] = $data['inmenu'] ? $data['inmenu'] : $a['inmenu'];
            $a['language'] = $data['language'] ? $data['language'] : $a['language'];
            $a['layout'] = $data['layout'] ? $data['layout'] : $a['layout'];
            $a['author'] = $data['author'] ? $data['author'] : $a['author'];
        }

        // Get parameters from whatever input we need.
        $pages = $this->getDoctrine()->getManager()->getRepository('Kaikmedia\PagesModule\Entity\PageEntity')->findAll($a);

        return $this->render('KaikmediaPagesModule:Admin:manager.html.twig', [
            'pages' => $pages,
            'form' => $form->createView(),
            'thisPage' => $a['page'],
            'maxPages' => 5 //ceil($pages / $a['limit'])
        ]);
    }

    /**
     * @Route("/manager/display/{id}", requirements={"id" = "\d+"})
     * Modify site information.
     *
     * @param Request $request
     * @param integer $id
     *            Parameters passed via GET:
     *            --------------------------------------------------
     *            string uname The user name of the account for which profile information should be modified; defaults to the uname of the current user.
     *            dynadata array The modified profile information passed into this function in case of an error in the update function.
     * @return RedirectResponse|string The rendered template output.
     * @throws AccessDeniedException on failed permission check
     */
    public function displayAction(Request $request, $id = null)
    {
//        // Security check
//        if (!\UserUtil::isLoggedIn() || !\SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
//            throw new AccessDeniedException();
//        }
        // Get parameters from whatever input we need.
        $page = $this->get('doctrine.entitymanager')->getRepository('Kaikmedia\PagesModule\Entity\PageEntity')->getOneBy([
            'id' => $id
        ]);

        return $this->render('KaikmediaPagesModule:Admin:display.html.twig', [
            'page' => $page
        ]);
    }

    /**
     * @Route("/manager/modify/{id}", requirements={"id" = "\d+"})
     * Modify site information.
     *
     * @param Request $request
     * @param integer $id
     *            Parameters passed via GET:
     *            --------------------------------------------------
     *            string uname The user name of the account for which profile information should be modified; defaults to the uname of the current user.
     *            dynadata array The modified profile information passed into this function in case of an error in the update function.
     * @return RedirectResponse|string The rendered template output.
     * @throws AccessDeniedException on failed permission check
     */
    public function modifyAction(Request $request, $id = null)
    {
//        // Security check
//        if (!\UserUtil::isLoggedIn() || !\SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
//            throw new AccessDeniedException();
//        }

        if ($id == null) {
            // create a new customer
            $page = new PageEntity();
        } else {
            $page = $this->get('doctrine.entitymanager')->getRepository('Kaikmedia\PagesModule\Entity\PageEntity')->getOneBy([
                'id' => $id
            ]);
        }

        $formBuilder = $this->get('form.factory')->createBuilder(PageType::class, $page)
                ->setAction($this->get('router')->generate('kaikmediapagesmodule_admin_manager', [], RouterInterface::ABSOLUTE_URL))
                ->setMethod('GET');

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isValid()) {

            //$images = $form->get('images')->getData();

            $em->persist($page);
            $em->flush();
            $request->getSession()
            ->getFlashBag()
            ->add('status', "Page saved!");

            //$gallery = new GalleryPlugin($this->container->get('doctrine.entitymanager'));
            //$result = $gallery->assignMedia('KaikmediaPagesModule', $page->getId(), $images);
            //var_dump($result);
            //exit(0);

            return $this->redirect($this->generateUrl('kaikmediapagesmodule_admin_display', [
                'id' => $page->getId()
            ]));
        }

        return $this->render('KaikmediaPagesModule:Admin:modify.html.twig', [
            'form' => $form->createView(),
            'page' => $page
        ]);
    }

    /**
     * @Route("/preferences")
     *
     * @return Response symfony response object
     * @throws AccessDeniedException Thrown if the user doesn't have admin access to the module
     */
    public function preferencesAction(Request $request)
    {
//        // Security check
//        if (!SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
//            throw new AccessDeniedException();
//        }

        $mod_vars = [];

        $form = $this->createForm('settingsform', $settings = [], [
            'action' => $this->get('router')
            ->generate('kaikmediapagesmodule_admin_preferences', [], RouterInterface::ABSOLUTE_URL),
            'itemsperpage' => isset($mod_vars['itemsperpage']) ? $mod_vars['itemsperpage'] : 25
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            foreach ($data as $key => $value) {

            }
        }

        return $this->render('KaikmediaPagesModule:Admin:preferences.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
