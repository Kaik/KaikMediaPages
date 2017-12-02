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
//use Kaikmedia\GalleryModule\Manager\Plugin as GalleryPlugin;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\RouterInterface;
use Zikula\Core\Controller\AbstractController;
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
        if (!$this->hasPermission($this->name.'::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

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
            'page' => $page
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
        if (!$this->hasPermission($this->name.'::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        $a = [];
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

        $formBuilder = $this->get('form.factory')
            ->createBuilder(PageFilterType::class, $a)
                ->setAction($this->get('router')->generate('kaikmediapagesmodule_manager_list', [], RouterInterface::ABSOLUTE_URL))
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

        $pages = $this->getDoctrine()
            ->getManager()
            ->getRepository('Kaikmedia\PagesModule\Entity\PageEntity')
            ->findAll($a);

        return $this->render('KaikmediaPagesModule:Manager:manager.html.twig', [
            'pages' => $pages,
            'form' => $form->createView(),
            'thisPage' => $a['page'],
            'maxPages' => ceil(count($pages) / $a['limit'])
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
        if (!$this->hasPermission($this->name.'::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

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
//                ->setAction($this->get('router')->generate('kaikmediapagesmodule_manager_list', [], RouterInterface::ABSOLUTE_URL))
            ->setMethod('POST');

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            //$images = $form->get('images')->getData();
            $em->persist($page);
            $em->flush();

            $request->getSession()
            ->getFlashBag()
            ->add('status', $this->__('Page saved!'));

            //$gallery = new GalleryPlugin($this->container->get('doctrine.entitymanager'));
            //$result = $gallery->assignMedia('KaikmediaPagesModule', $page->getId(), $images);

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
