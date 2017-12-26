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

namespace Kaikmedia\PagesModule\Security;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Common\Translator\TranslatorInterface;
//use Zikula\ExtensionsModule\Api\VariableApi;
use Zikula\PermissionsModule\Api\PermissionApi;

/**
 * AccessManager.
 *
 * @author Kaik
 */
class AccessManager
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var PermissionApi
     */
    private $permissionApi;

    /**
     * @var CurrentUser
    */
    private $user;

    public function __construct(
        RequestStack $requestStack,
        TranslatorInterface $translator,
        PermissionApi $permissionApi
    ) {
        $this->name = 'KaikmediaPagesModule';
        $this->requestStack = $requestStack;
        $this->request = $requestStack->getMasterRequest();
        $this->translator = $translator;
        $this->permissionApi = $permissionApi;
        $this->user = $this->request->getSession()->get('uid') > 1 ? $this->request->getSession()->get('uid') : 1;
    }

    /*
     * Do all user checks in one method:
     * Check if logged in, has correct access, and if site is disabled
     * Returns the appropriate error/return value if failed, which can be
     *          returned by calling method.
     * Returns false if use has permissions.
     * On exit, $uid has the user's UID if logged in.
     */
    public function hasPermission($level = ACCESS_READ, $throw = true, $component = null, $instance = null, $user = null)
    {
        $comp = null === $component ? '::' : $component;
        $inst = null === $instance ? '::' : $instance;

        // @todo module enabled/disabled check

        // Zikula perms check
        $zkPerms = $this->hasPermissionRaw($comp, $inst, $level, $user);

        // if needed additional conditions here
        $allowed = $this->user > 1 && $zkPerms;

        // Return status or throw exception
        if (!$allowed && $throw) {
            throw new AccessDeniedException();
        } else {
            return $allowed;
        }
    }

    private function hasPermissionRaw($component, $instance, $level, $user)
    {
        return $this->permissionApi->hasPermission($this->name.$component, $instance, $level, $user);
    }
}
