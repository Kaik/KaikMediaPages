<?php
/**
 * Copyright (c) KaikMedia.com 2014
 *
 */

namespace Kaikmedia\PagesModule\Controller;

use DataUtil;
use ModUtil;
use SecurityUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use System;
use UserUtil;
use ServiceUtil;
use ZLanguage;
use Zikula\Core\Controller\AbstractController;
use Zikula\Core\Event\GenericEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route; // used in annotations - do not remove
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method; // used in annotations - do not remove
use Symfony\Component\Routing\RouterInterface;
//use Kaikmedia\PagesModule\Entity\PagesEntity as Page; 


class UserController extends AbstractController
{
    /**
     * @Route("")
     *
     * The default entry point.
     *
     * This redirects back to the default entry point for the Users module.
     *
     * @return RedirectResponse
     */
    public function indexAction()
    {
        return new RedirectResponse($this->get('router')->generate('kaikmediapagesmodule_user_view', array(), RouterInterface::ABSOLUTE_URL));
    }
         
    /**
     * @Route("/abouts")
     * 
     * Display item.
     *
     * @param Request $request
     *
     * Parameters passed via GET:
     * --------------------------------------------------
     * numeric uid   The user account id (uid) of the user for whom to display profile information; optional, ignored if uname is supplied, if not provided
     *                  and if uname is not supplied then defaults to the current user.
     * string  uname The user name of the user for whom to display profile information; optional, if not supplied, then uid is used to determine the user.
     * string  page  The name of the Profile "page" (view template) to display; optional, if not provided then the standard view template is used.
     *
     * @return RedirectResponse|string The rendered template output.
     *
     * @throws AccessDeniedException on failed permission check
     */
    public function aboutsAction(Request $request)
    {
        // Security check
        if (!SecurityUtil::checkPermission($this->name.'::view', '::', ACCESS_READ)) {
            throw new AccessDeniedException();
        }
        
        $a['page'] = 1;
        $a['limit'] = 10;
        $a['title'] = '';
        $a['online'] = 1;         
        
        // Get parameters from whatever input we need.
        $this->entityManager = ServiceUtil::getService('doctrine.entitymanager');
        $pages = $this->entityManager
                    ->getRepository('Kaikmedia\PagesModule\Entity\PagesEntity')
                    ->getAll($a);
        
        
        $request->attributes->set('_legacy', true); // forces template to render inside old theme
        return $this->render('KaikmediaPagesModule:User:list.html.twig',
                array('ZUserLoggedIn' => \UserUtil::isLoggedIn(),
                      'pages' => $pages,            
                        'thisPage' => $a['page'],
                        'maxPages' => ceil($pages->count() / $a['limit'])));                     
    }
    
    /**
     * @Route("/about/{urltitle}")
     * 
     * Display item.
     *
     * @param Request $request
     *
     * Parameters passed via GET:
     * --------------------------------------------------
     * numeric uid   The user account id (uid) of the user for whom to display profile information; optional, ignored if uname is supplied, if not provided
     *                  and if uname is not supplied then defaults to the current user.
     * string  uname The user name of the user for whom to display profile information; optional, if not supplied, then uid is used to determine the user.
     * string  page  The name of the Profile "page" (view template) to display; optional, if not provided then the standard view template is used.
     *
     * @return RedirectResponse|string The rendered template output.
     *
     * @throws AccessDeniedException on failed permission check
     */
    public function aboutAction(Request $request , $urltitle)
    {
        // Security check
        if (!SecurityUtil::checkPermission($this->name.'::view', '::', ACCESS_READ)) {
            throw new AccessDeniedException();
        }

        // Get parameters from whatever input we need.
        $this->entityManager = ServiceUtil::getService('doctrine.entitymanager');
        $page = $this->entityManager
                    ->getRepository('Kaikmedia\PagesModule\Entity\PagesEntity')
                    ->getOneBy(array('urltitle' => $urltitle, 'language' => $this->container->get('translator')->getLocale()));
        
        //"Done! Deleted %1$d user account." => "Gotowe! Usunięto %1$d konto użytkownika."
        //"Done! Deleted %1$d user accounts." => "Gotowe! Usunięto %1$d konto użytkownika.|Gotowe! Usunięto %1$d konta użytkowników.|Gotowe! Usunięto %1$d kont użytkowników."
       // $translated = $this->translator->__('Page');
        /*
        $translated = $this->translator->transChoice(
        		'Done! Deleted %1$d user accounts.',
        		2,
        		array('%1$d' => 2),
        		'zikula'
        );
        */
        

        $translated = array();
        //m1 m2 n params domain locale
        //default locale
        $translated[] = $this->translator->_fn('Done! Deleted %1$d user account.', 'Done! Deleted %1$d user accounts.', 1, array('%1$d' => 1), 'zikula');        
        $translated[] = $this->translator->_fn('Done! Deleted %1$d user account.', 'Done! Deleted %1$d user accounts.', 2, array('%1$d' => 2), 'zikula'); 
        $translated[] = $this->translator->_fn('Done! Deleted %1$d user account.', 'Done! Deleted %1$d user accounts.', 5, array('%1$d' => 5), 'zikula'); 
        //forced locale
        $translated[] = $this->translator->_fn('Done! Deleted %1$d user account.', 'Done! Deleted %1$d user accounts.', 1, array('%1$d' => 1), 'zikula', 'pl');         
        $translated[] = $this->translator->_fn('Done! Deleted %1$d user account.', 'Done! Deleted %1$d user accounts.', 2, array('%1$d' => 2), 'zikula', 'pl'); 
        $translated[] = $this->translator->_fn('Done! Deleted %1$d user account.', 'Done! Deleted %1$d user accounts.', 5, array('%1$d' => 5), 'zikula', 'pl'); 
        // default domain and locale should be not translated        
        $translated[] = $this->translator->_fn('Done! Deleted %1$d user account.', 'Done! Deleted %1$d user accounts.', 1, array('%1$d' => 1));        
        $translated[] = $this->translator->_fn('Done! Deleted %1$d user account.', 'Done! Deleted %1$d user accounts.', 2, array('%1$d' => 2));
        $translated[] = $this->translator->_fn('Done! Deleted %1$d user account.', 'Done! Deleted %1$d user accounts.', 5, array('%1$d' => 5));  
		//zikula notation
        $translated[] = $this->_fn('Done! Deleted %1$d user account.', 'Done! Deleted %1$d user accounts.', 1, array('%1$d' => 1));
        $translated[] = $this->_fn('Done! Deleted %1$d user account.', 'Done! Deleted %1$d user accounts.', 2, array('%1$d' => 2));
        $translated[] = $this->_fn('Done! Deleted %1$d user account.', 'Done! Deleted %1$d user accounts.', 5, array('%1$d' => 5));
        $translated[] = $this->__('Page');
        
        $request->attributes->set('_legacy', true); // forces template to render inside old theme
        return $this->render('KaikmediaPagesModule:User:display.html.twig', array(
            'ZUserLoggedIn' => \UserUtil::isLoggedIn(),
            'page' => $page ,'translated' => $translated, 'translator' =>$this->get('translator')));
    }    
    
}
