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

namespace Kaikmedia\PagesModule\Container;

use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Core\LinkContainer\LinkContainerInterface;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;

class LinkContainer implements LinkContainerInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var PermissionApiInterface
     */
    private $permissionApi;

    /**
     * @var bool
     */
    private $enableCategorization;

    /**
     * LinkContainer constructor.
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     * @param PermissionApiInterface $permissionApi
     */
    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        PermissionApiInterface $permissionApi,
        $enableCategorization
    ) {
        $this->translator = $translator;
        $this->router = $router;
        $this->permissionApi = $permissionApi;
        $this->enableCategorization = $enableCategorization;
    }


    /**
     * get Links of any type for this extension
     * required by the interface.
     *
     * @param string $type
     *
     * @return array
     */
    public function getLinks($type = LinkContainerInterface::TYPE_ADMIN)
    {
        $method = 'get'.ucfirst(strtolower($type));
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return [];
    }

    /**
     * get the Admin links for this extension.
     *
     * @return array
     */
    private function getAdmin()
    {
        $links = [];

        if ($this->permissionApi->hasPermission($this->getBundleName().'::', '::', ACCESS_EDIT)) {
            $links[] = [
                    'url' => $this->router->generate('kaikmediapagesmodule_manager_list'),
                    'text' => $this->translator->__('List'),
                    'title' => $this->translator->__('Pages manager list'),
                    'icon' => 'list'];
            }
            if ($this->permissionApi->hasPermission('KaikmediaPagesModule::', '::', ACCESS_ADD)) {
                $links[] = [
                    'url' => $this->router->generate('kaikmediapagesmodule_manager_modify'),
                    'text' => $this->translator->__('New'),
                    'title' => $this->translator->__('Add a new Page'),
                    'icon' => 'plus'];
            }
            if ($this->permissionApi->hasPermission('KaikmediaPagesModule::', '::', ACCESS_ADMIN)) {
                $links[] = [
                    'url' => $this->router->generate('kaikmediapagesmodule_admin_preferences'),
                    'text' => $this->translator->__('Modify Config'),
                    'title' => $this->translator->__('Modify Config'),
                    'icon' => 'wrench'];
            }

        return $links;
    }

    /**
     * get the User links for this extension.
     *
     * @return array
     */
    private function getUser()
    {
        $links = [];

//        if ($this->currentUserApi->isLoggedIn()) {
//            if ($this->permissionApi->hasPermission('ZikulaUsersModule::', '::', ACCESS_READ)) {
//                $links[] = [
//                    'url'  => $this->router->generate('zikulausersmodule_account_menu'),
//                    'icon' => 'user-circle-o',
//                    'text' => $this->translator->__('Account menu'),
//                ];
//            }
//
//            if ($this->permissionApi->hasPermission($this->getBundleName().'::', '::', ACCESS_READ)) {
//                $links[] = [
//                    'url'   => $this->router->generate('zikulaprofilemodule_profile_display'),
//                    'text'  => $this->translator->__('Profile'),
//                    'icon'  => 'user',
//                    'links' => [
//                        [
//                            'url'  => $this->router->generate('zikulaprofilemodule_profile_display'),
//                            'text' => $this->translator->__('Display profile'),
//                        ],
//                        [
//                            'url'  => $this->router->generate('zikulaprofilemodule_profile_edit'),
//                            'text' => $this->translator->__('Edit profile'),
//                        ],
//                        [
//                            'url'  => $this->router->generate('zikulazauthmodule_account_changeemail'),
//                            'text' => $this->translator->__('Change email address'),
//                        ],
//                        [
//                            'url'  => $this->router->generate('zikulazauthmodule_account_changepassword'),
//                            'text' => $this->translator->__('Change password'),
//                        ],
//                    ],
//                ];
//            }
//
//            $messageModule = $this->variableApi->getSystemVar(SettingsConstant::SYSTEM_VAR_MESSAGE_MODULE, '');
//            if ($messageModule != '' && ModUtil::available($messageModule) && $this->permissionApi->hasPermission($messageModule.'::', '::', ACCESS_READ)) {
//                $links[] = [
//                    'url'  => $this->messageModuleCollector->getSelected()->getInboxUrl(),
//                    'text' => $this->translator->__('Messages'),
//                    'icon' => 'envelope',
//                ];
//            }
//        }

        if ($this->permissionApi->hasPermission($this->getBundleName().':Members:', '::', ACCESS_READ)) {
            $membersLinks = [];
            if ($this->permissionApi->hasPermission($this->getBundleName().':Members:', '::', ACCESS_READ)) {
                $membersLinks[] = [
                    'url'  => $this->router->generate('zikulaprofilemodule_members_list'),
                    'text' => $this->translator->__('Registered users'),
                    'icon' => 'users',
                ];
            }
            if ($this->permissionApi->hasPermission($this->getBundleName().':Members:recent', '::', ACCESS_READ)) {
                $membersLinks[] = [
                    'url'  => $this->router->generate('zikulaprofilemodule_members_recent'),
                    'text' => $this->translator->__f('Last %s registered users', ['%s' => $this->variableApi->get($this->getBundleName(), 'recentmembersitemsperpage', 10)]),
                ];
            }
            if ($this->permissionApi->hasPermission($this->getBundleName().':Members:online', '::', ACCESS_READ)) {
                $membersLinks[] = [
                    'url'  => $this->router->generate('zikulaprofilemodule_members_online'),
                    'text' => $this->translator->__('Users online'),
                ];
            }
            $links[] = [
                'url'   => $this->router->generate('zikulaprofilemodule_members_list'),
                'text'  => $this->translator->__('Registered users'),
                'icon'  => 'list',
                'links' => $membersLinks,
            ];
        }

        return $links;
    }

    /**
     * get the Account links for this extension.
     *
     * @return array
     */
    private function getAccount()
    {
        $links = [];

//        if (!$this->currentUserApi->isLoggedIn()) {
//            return $links;
//        }

//        $links[] = [
//            'url'  => $this->router->generate('zikulaprofilemodule_profile_display', ['uid' => $this->currentUserApi->get('uid')]),
//            'text' => $this->translator->__('Profile'),
//            'icon' => 'user',
//        ];
//
//        if ($this->permissionApi->hasPermission($this->getBundleName().':Members:', '::', ACCESS_READ)) {
//            $links[] = [
//                'url'  => $this->router->generate('zikulaprofilemodule_members_list'),
//                'text' => $this->translator->__('Registered users'),
//                'icon' => 'users',
//            ];
//        }
//
//        // check if the users block exists
//        $block = $this->blocksRepository->findOneBy(['bkey' => 'ZikulaProfileModule:Zikula\ProfileModule\Block\UserBlock']);
//        if (isset($block)) {
//            $links[] = [
//                'url'   => $this->router->generate('zikulaprofilemodule_userblock_edit'),
//                'text'  => $this->translator->__('Personal custom block'),
//                'icon'  => 'cube',
//            ];
//        }

        return $links;
    }

//
//    public function getLinks($type = LinkContainerInterface::TYPE_ADMIN)
//    {
//        $links = [];
//        if (LinkContainerInterface::TYPE_ADMIN == $type) {
//            if ($this->permissionApi->hasPermission('KaikmediaPagesModule::', '::', ACCESS_READ)) {
//                $links[] = [
//                    'url' => $this->router->generate('kaikmediapagesmodule_manager_list'),
//                    'text' => $this->translator->__('Pages list'),
//                    'title' => $this->translator->__('Pages manager list'),
//                    'icon' => 'list'];
//            }
////            if ($this->permissionApi->hasPermission('KaikmediaPagesModule::', '::', ACCESS_ADD)) {
//                $links[] = [
//                    'url' => $this->router->generate('kaikmediapagesmodule_manager_modify'),
//                    'text' => $this->translator->__('New Page'),
//                    'title' => $this->translator->__('New Page'),
//                    'icon' => 'plus'];
////            }
//            if ($this->permissionApi->hasPermission('KaikmediaPagesModule::', '::', ACCESS_ADMIN)) {
//                $links[] = [
//                    'url' => $this->router->generate('kaikmediapagesmodule_admin_preferences'),
//                    'text' => $this->translator->__('Modify Config'),
//                    'title' => $this->translator->__('Modify Config'),
//                    'icon' => 'wrench'];
//            }
//        } elseif (LinkContainerInterface::TYPE_USER == $type) {
//            if ($this->permissionApi->hasPermission('KaikmediaPagesModule::', '::', ACCESS_OVERVIEW)) {
//                $links[] = [
//                    'url' => $this->router->generate('zikulapagesmodule_page_view'),
//                    'text' => $this->translator->__('Pages list'),
//                    'icon' => 'list'];
//                if ($this->enableCategorization) {
////                    $links[] = [
////                        'url' => $this->router->generate('zikulapagesmodule_user_categories'),
////                        'text' => $this->translator->__('Categories'),
////                        'icon' => 'tag'];
//                }
//            }
//        }
//    }

    public function getBundleName()
    {
        return 'KaikmediaPagesModule';
    }
}
