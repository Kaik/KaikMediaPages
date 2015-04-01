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
     * @Route("/view")
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
    public function viewAction(Request $request)
    {
        // Security check
        if (!SecurityUtil::checkPermission($this->name.'::view', '::', ACCESS_READ)) {
            throw new AccessDeniedException();
        }
        

        
        
        $request->attributes->set('_legacy', true); // forces template to render inside old theme
        return $this->render('KaikmediaPagesModule:User:list.html.twig',
                array('ZUserLoggedIn' => \UserUtil::isLoggedIn(),
                    ));
    }    
}
