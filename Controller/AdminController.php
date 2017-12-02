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

        return new RedirectResponse($this->get('router')->generate('kaikmediapagesmodule_manager_list', [], RouterInterface::ABSOLUTE_URL));
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
