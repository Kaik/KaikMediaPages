<?php

/**
 * 
 */

namespace Kaikmedia\PagesModule\Security;

class AccessManager {
    /*     * *
     * Do all user checks in one method:
     * Check if logged in, has correct access, and if site is disabled
     * Returns the appropriate error/return value if failed, which can be
     *          returned by calling method.
     * Returns false if use has permissions.
     * On exit, $uid has the user's UID if logged in.
     */

    public function hasPermission($access = ACCESS_READ) {
        // Perform access check
        if (!$this->hasPermissionRaw('KaikmediaPagesModule::', '::', $access)) {
            return false;
        }
        // Get the uid of the user
        $uid = \UserUtil::getVar('uid');

        // Return user uid to signify everything is OK.
        return $uid;
    }

    public function hasPermissionRaw($component, $instance, $level) {
        return \SecurityUtil::checkPermission($component, $instance, $level);
    }

}
