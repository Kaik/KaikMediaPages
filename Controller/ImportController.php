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

use Kaikmedia\PagesModule\Form\Type\SettingsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Zikula\Core\Controller\AbstractController;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * @Route("/import")
 */
class ImportController extends AbstractController
{
    /**
     * @Route("/index")
     *
     * @Theme("admin")
     *
     * the main administration function
     *
     * @return RedirectResponse
     */
    public function indexAction()
    {
        // access throw component instance user
        $this->get('kaikmedia_pages_module.access_manager')->hasPermission(ACCESS_ADMIN, true);

        return $this->render('@KaikmediaPagesModule/Import/index.html.twig', [
            'importHelper' => $this->get('kaikmedia_pages_module.import_helper')
        ]);
    }

    /**
     * @Route("/status", options={"expose"=true})
     *
     * @Theme("admin")
     *
     * the main administration function
     *
     * @return RedirectResponse
     */
    public function statusAction(Request $request)
    {
        // access throw component instance user
        $this->get('kaikmedia_pages_module.access_manager')->hasPermission(ACCESS_ADMIN, true);

        $content = $request->getContent();
        if (!empty($content)) {
            $data = json_decode($content, true); // 2nd param to get as array
        }

        $importHelper = $this->get('kaikmedia_pages_module.import_helper');
        $data = $importHelper->getTableCheck($data);

        return new Response(json_encode($data));
    }

    /**
     * @Route("/import", options={"expose"=true})
     *
     * @Theme("admin")
     *
     * the main administration function
     *
     * @return RedirectResponse
     */
    public function importAction(Request $request)
    {
        // access throw component instance user
        $this->get('kaikmedia_pages_module.access_manager')->hasPermission(ACCESS_ADMIN, true);

        $content = $request->getContent();
        if (!empty($content)) {
            $data = json_decode($content, true); // 2nd param to get as array
        }

        $importHelper = $this->get('kaikmedia_pages_module.import_helper');
        $data = $importHelper->importData($data);

        return new Response(json_encode($data));
    }
}
