<?php
/**
 * Pages Module for Zikula
 *
 * @copyright  InterCom Team
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package    InterCom
 * @subpackage User
 *
 * Please see the CREDITS.txt file distributed with this source code for further
 * information regarding copyright.
 */

namespace Kaikmedia\PagesModule\Controller;

use ModUtil;
use System;
use SecurityUtil;
use ServiceUtil;
use UserUtil;
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
//use Zikula\IntercomModule\Util\Tools;
//use Zikula\IntercomModule\Util\Settings;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{ 
    public function postInitialize()
    {
        $this->view->setCaching(false);
    }

    /**
     * Route not needed here because this is a legacy-only method
     * 
     * The default entrypoint.
     *
     * @return RedirectResponse
     */
    public function mainAction()
    {
        return new RedirectResponse($this->get('router')->generate('kaikmediapagesmodule_admin_view', array(), RouterInterface::ABSOLUTE_URL));
    }
    
    /**
     * @Route("")
     *
     * the main administration function
     *
     * @return RedirectResponse
     */
    public function indexAction(Request $request)
    {
        // Security check
        if (!SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }
        

        
        
        $request->attributes->set('_legacy', true); // forces template to render inside old theme
        return $this->render('KaikmediaPagesModule:Admin:index.html.twig', array(
            'ZUserLoggedIn' => \UserUtil::isLoggedIn()));
    }
    
    /**
     * @Route("/manager")
     *
     * the main administration function
     *
     * @return RedirectResponse
     */
    public function managerAction(Request $request)
    {
        // Security check
        if (!SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }
        $a = array();
        $a['page'] = 1;
        $a['limit'] = 10;
        $a['title'] = '';
        $a['online'] = 1;        
        $form = $this->createForm('pagesfilterform',
                                   $filters = array(),
                                   array('action' => $this->get('router')->generate('kaikmediapagesmodule_admin_preferences', array(), RouterInterface::ABSOLUTE_URL),
                                         'limit' => $a['limit'],
                                         'title' => $a['title'],
                                         'online' => $a['online']
                                        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
        $data = $form->getData();    
      
        }

        // Get parameters from whatever input we need.
        $this->entityManager = ServiceUtil::getService('doctrine.entitymanager');
        $pages = $this->entityManager
                    ->getRepository('Kaikmedia\PagesModule\Entity\PagesEntity')
                    ->getAll($a);     
        
        $request->attributes->set('_legacy', true); // forces template to render inside old theme
        return $this->render('KaikmediaPagesModule:Admin:manager.html.twig', array(
            'ZUserLoggedIn' => \UserUtil::isLoggedIn(),
            'pages' => $pages,          
            'form' => $form->createView(),            
            'thisPage' => $a['page'],
            'maxPages' => ceil($pages->count() / $a['limit'])));        
    }
    
    /**
     * @Route("manager/display/{id}", requirements={"id" = "\d+"})
     *
     * Modify site information.
     *
     * @param Request $request
     * @param integer $id
     *
     * Parameters passed via GET:
     * --------------------------------------------------
     * string   uname The user name of the account for which profile information should be modified; defaults to the uname of the current user.
     * dynadata array The modified profile information passed into this function in case of an error in the update function.
     *
     * @return RedirectResponse|string The rendered template output.
     *
     * @throws AccessDeniedException on failed permission check
     */
    public function displayAction(Request $request, $id = null)
    {
        // Security check
        if (!UserUtil::isLoggedIn() || !SecurityUtil::checkPermission($this->name.'::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }
        // Get parameters from whatever input we need.
        $this->entityManager = ServiceUtil::getService('doctrine.entitymanager');
        $page = $this->entityManager
                    ->getRepository('Kaikmedia\PagesModule\Entity\PagesEntity')
                    ->getOneBy(array('id' => $id));
        
        $request->attributes->set('_legacy', true); // forces template to render inside old theme
        return $this->render('KaikmediaPagesModule:Admin:display.html.twig', array(
            'ZUserLoggedIn' => \UserUtil::isLoggedIn(),
            'page' => $page));
    }
    
    /**
     * @Route("manager/modify/{id}", requirements={"id" = "\d+"})
     *
     * Modify site information.
     *
     * @param Request $request
     * @param integer $id
     *
     * Parameters passed via GET:
     * --------------------------------------------------
     * string   uname The user name of the account for which profile information should be modified; defaults to the uname of the current user.
     * dynadata array The modified profile information passed into this function in case of an error in the update function.
     *
     * @return RedirectResponse|string The rendered template output.
     *
     * @throws AccessDeniedException on failed permission check
     */
    public function modifyAction(Request $request, $id = null)
    {
        // Security check
        if (!UserUtil::isLoggedIn() || !SecurityUtil::checkPermission($this->name.'::', '::', ACCESS_READ)) {
            throw new AccessDeniedException();
        }
        
        if($id == null){
        // create a new customer
        $page = new Page();
                    
        }else{
        $this->entityManager = ServiceUtil::getService('doctrine.entitymanager');
        $page = $this->entityManager
                    ->getRepository('Kaikmedia\PagesModule\Entity\PagesEntity')
                    ->getOneBy(array('id' => $id));    
        }
        
        $form = $this->createForm('pageform', $page);

        $form->handleRequest($request);

        /** @var \Doctrine\ORM\EntityManager $em
         */ 
        $em = $this->getDoctrine()->getManager();
        if ($form->isValid()) {
            $em->persist($page);
            $em->flush();
            $request->getSession()->getFlashBag()->add('status', "Page saved!");

            return $this->redirect($this->generateUrl('kaikmediapagesmodule_admin_manager'));
        }

        $request->attributes->set('_legacy', true); // forces template to render inside old theme
        return $this->render('KaikmediaPagesModule:Admin:modify.html.twig', array(
            'form' => $form->createView(),
            'page' => $page,
        ));        
    }     

    /**
     * @Route("/preferences")
     *
     * @return Response symfony response object
     *
     * @throws AccessDeniedException Thrown if the user doesn't have admin access to the module
     */
    public function preferencesAction(Request $request)
    {
        // Security check
        if (!SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }   

        $mod_vars = ModUtil::getVar($this->name);
        
        $form = $this->createForm('settingsform',
                                   $settings = array(),
                                   array('action' => $this->get('router')->generate('kaikmediapagesmodule_admin_preferences', array(), RouterInterface::ABSOLUTE_URL),
                                         'itemsperpage' => isset($mod_vars['itemsperpage']) ? $mod_vars['itemsperpage'] : 25,
                                         'images_max_count' => isset($mod_vars['images_max_count']) ? $mod_vars['images_max_count'] : 5,
                                         'images_max_size' => isset($mod_vars['images_max_size']) ? $mod_vars['images_max_size'] :555,
                                         'images_ext_allowed' => isset($mod_vars['images_ext_allowed']) ? $mod_vars['images_ext_allowed'] :'png'
                                        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
        $data = $form->getData();    
        foreach ($data as $key => $value) {
            ModUtil::setVar($this->name,$key, $value);            
            }       
        }        
   
        $request->attributes->set('_legacy', true); // forces template to render inside old them        
        return $this->render('KaikmediaPagesModule:Admin:preferences.html.twig', array(
            'ZUserLoggedIn' => \UserUtil::isLoggedIn(),
            'form' => $form->createView()          
        ));
    }
}