<?php
/**
 * Copyright (c) KaikMedia.com 2014
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
use Kaikmedia\PagesModule\Entity\PagesEntity as Page;
use Kaikmedia\GalleryModule\Manager\Plugin as GalleryPlugin;
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
        // Security check
        if (!\SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }
        
        return new RedirectResponse($this->get('router')->generate('kaikmediapagesmodule_admin_manager', array(), RouterInterface::ABSOLUTE_URL));
    }

    /**
     * @Route("/manager/{page}", requirements={"page" = "\d*"}, defaults={"page" = 1})
     * the main administration function
     * 
     * @return RedirectResponse
     */
    public function managerAction(Request $request, $page)
    {
        // Security check
        if (!\SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }
        $a = array();
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
        
        $form = $this->createFormBuilder($a)
        ->setAction($this->get('router')->generate('kaikmediapagesmodule_admin_manager', array(), RouterInterface::ABSOLUTE_URL))
        ->setMethod('GET')      
        ->add('limit', 'choice', array('choices' => array('10' => '10','15' => '15','25' => '25','50' => '50','100'=> '100'), 'required' => false))
        ->add('title', 'text', array('required' => false))
        ->add('online', 'choice', array('choices' => array('1' => 'Online','0' => 'Offline'),'required' => false))
        ->add('depot', 'choice', array('choices' => array('1' => 'Allowed','0' => 'Depot'),'required' => false))
        ->add('inlist', 'choice', array('choices' => array('1' => 'In List','0' => 'Not in list'),'required' => false))
        ->add('inmenu', 'choice', array('choices' => array('1' => 'In Menu','0' => 'Not in menu'),'required' => false))
        //todo add language detection
        ->add('language', 'choice', array('choices' => array('any' => 'Any','en' => 'English' ,'pl' => 'Polish'),'required' => false))
        //todo add layout detection
        ->add('layout', 'choice', array('choices' => array('default' => 'Default','slider' => 'Slider'),'required' => false))
        ->add('author', 'text', array('required' => false))
        ->add('filter', 'submit', array('label' => 'Filter'))
        ->getForm();        
        
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
        $pages = $this->get('doctrine.entitymanager')->getRepository('Kaikmedia\PagesModule\Entity\PageEntity')->getAll($a);
        
        $request->attributes->set('_legacy', true); // forces template to render inside old theme
        return $this->render('KaikmediaPagesModule:Admin:manager.html.twig', array(
            'pages' => $pages,
            'form' => $form->createView(),
            'thisPage' => $a['page'],
            'maxPages' => ceil($pages->count() / $a['limit'])
        ));
    }

    /**
     * @Route("manager/display/{id}", requirements={"id" = "\d+"})
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
        // Security check
        if (!\UserUtil::isLoggedIn() || !\SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }
        // Get parameters from whatever input we need.
        $page = $this->get('doctrine.entitymanager')->getRepository('Kaikmedia\PagesModule\Entity\PageEntity')->getOneBy(array(
            'id' => $id
        ));
        
        $request->attributes->set('_legacy', true); // forces template to render inside old theme
        return $this->render('KaikmediaPagesModule:Admin:display.html.twig', array(
            'ZUserLoggedIn' => \UserUtil::isLoggedIn(),
            'page' => $page
        ));
    }

    /**
     * @Route("manager/modify/{id}", requirements={"id" = "\d+"})
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
        // Security check
        if (!\UserUtil::isLoggedIn() || !\SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }
        
        if ($id == null) {
            // create a new customer
            $page = new Page();
        } else {
            $page = $this->get('doctrine.entitymanager')->getRepository('Kaikmedia\PagesModule\Entity\PageEntity')->getOneBy(array(
                'id' => $id
            ));
        }
        
        $form = $this->createForm('pageform', $page);
        
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
            
            return $this->redirect($this->generateUrl('kaikmediapagesmodule_admin_display', array(
                'id' => $page->getId()
            )));
        }
        
        $request->attributes->set('_legacy', true); // forces template to render inside old theme
        return $this->render('KaikmediaPagesModule:Admin:modify.html.twig', array(
            'form' => $form->createView(),
            'page' => $page
        ));
    }

    /**
     * @Route("/preferences")
     * 
     * @return Response symfony response object
     * @throws AccessDeniedException Thrown if the user doesn't have admin access to the module
     */
    public function preferencesAction(Request $request)
    {
        // Security check
        if (! SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }
        
        $mod_vars = ModUtil::getVar($this->name);
        
        $form = $this->createForm('settingsform', $settings = array(), array(
            'action' => $this->get('router')
                ->generate('kaikmediapagesmodule_admin_preferences', array(), RouterInterface::ABSOLUTE_URL),
            'itemsperpage' => isset($mod_vars['itemsperpage']) ? $mod_vars['itemsperpage'] : 25
        ));
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $data = $form->getData();
            foreach ($data as $key => $value) {
                ModUtil::setVar($this->name, $key, $value);
            }
        }
        
        $request->attributes->set('_legacy', true); // forces template to render inside old them
        return $this->render('KaikmediaPagesModule:Admin:preferences.html.twig', array(
            'ZUserLoggedIn' => \UserUtil::isLoggedIn(),
            'form' => $form->createView()
        ));
    }
}