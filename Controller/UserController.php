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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Core\Controller\AbstractController;
use Zikula\Core\Event\GenericEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route; // used in annotations - do not remove
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method; // used in annotations - do not remove
use Symfony\Component\Routing\RouterInterface;

class UserController extends AbstractController
{
    /**
     * @Route("")
     * The default entry point.
     * This redirects back to the default entry point for the Users module.
     *
     * @return RedirectResponse
     */
    public function indexAction()
    {
        return new RedirectResponse($this->get('router')->generate('kaikmediapagesmodule_user_view', [], RouterInterface::ABSOLUTE_URL));
    }

    /**
     * @Route("/view/{page}")
     *
     * @todo online defailt in repository
     * Display pages list.
     * @throws AccessDeniedException on failed permission check
     */
    public function viewAction(Request $request, $page)
    {
//        // Security check
//        if (!SecurityUtil::checkPermission($this->name . '::view', '::', ACCESS_READ)) {
//            throw new AccessDeniedException();
//        }

        $a = [];
        $a['page'] = $page;
        $a['limit'] = $request->query->get('limit', 25);
        $a['online'] = 1; // this should be 1 by default

        $pages = $this->get('doctrine.entitymanager')->getRepository('Kaikmedia\PagesModule\Entity\PagesEntity')->getAll($a);

        return $this->render('KaikmediaPagesModule:User:view.html.twig', [
            'pages' => $pages, 'thisPage' => $a['page'], 'maxPages' => ceil($pages->count() / $a['limit'])
        ]);
    }

    /**
     * @Route("/display/{urltitle}", options={"zkNoBundlePrefix"=1})
     * Display item.
     *
     * @throws AccessDeniedException on failed permission check
     */
    public function displayAction(Request $request, $urltitle)
    {
//        // Security check
//        if (!SecurityUtil::checkPermission($this->name . '::view', '::', ACCESS_READ)) {
//            throw new AccessDeniedException();
//        }

        $a = [];
        $a['online'] = 1;
        $a['urltitle'] = $urltitle;
//        $a['language'] = $this->container->get('translator')->getLocale();

        $page = $this->get('doctrine.entitymanager')->getRepository('Kaikmedia\PagesModule\Entity\PagesEntity')->getOneBy($a);

        return $this->render('KaikmediaPagesModule:User:display.html.twig', [
            'page' => $page
        ]);
    }
}
