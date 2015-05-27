<?php
/**
 * Copyright Pages Team 2015
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Pages
 * @link https://github.com/zikula-modules/Pages
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */
namespace Kaikmedia\PagesModule\Block;
use BlockUtil;
use ModUtil;
use SecurityUtil;
use Zikula\Core\Controller\AbstractBlockController;
/**
 * Class PagesListBlock
 * @package Kaikmedia\PagesModule\Block
 */
class PagesListBlock extends AbstractBlockController
{
    /**
     * Initialise block.
     *
     * @return void
     */
    public function init()
    {
        SecurityUtil::registerPermissionSchema('KaikmediaPagesModule:pageslistblock:', 'Block title::');
    }
    /**
     * get information on block
     *
     * @return array The block information
     */
    public function info()
    {
        return array(
            'module' => $this->name,
            'text_type' => __('Display List'),
            'text_type_long' => __('Display a list of pages'),
            'allow_multiple' => true,
            'form_content' => false,
            'form_refresh' => false,
            'show_preview' => true,
            'admin_tableless' => true
        );
    }
    /**
     * Display block.
     *
     * @param array $blockInfo A blockInfo structure.
     *
     * @return string|void The rendered block.
     */
    public function display($blockInfo)
    {
        // Security check
        if (!SecurityUtil::checkPermission('KaikmediaPagesModule:pageslistblock:', "{$blockInfo['title']}::", ACCESS_READ)) {
            return false;
        }
        
        $locale = $this->get('request')->query->get('lang', $this->translator->getLocale());
        $this->translator->setLocale($locale);
        
        // Get variables from content block
        $vars = BlockUtil::varsFromContent($blockInfo['content']);
        // Defaults
        if (empty($vars['numitems'])) {
            $vars['numitems'] = 5;
        }
        if (empty($vars['template'])) {
            $vars['template'] = 'carousel';
        }        
        
        // Check if the module is available.
        if (!ModUtil::available($this->name)) {
            return false;
        }
        //var_dump($locale);
        //exit(0);
        $a = array();
        
        $a['page'] = 1;
        $a['limit'] = $vars['numitems'];
        $a['title'] = '';
        $a['online'] = 1;
        $a['language'] = $locale;
        // Get parameters from whatever input we need.
        $pages = $this->get('doctrine.entitymanager')->getRepository('Kaikmedia\PagesModule\Entity\PagesEntity')->getAll($a); 
               
        $blockInfo['content'] = $this->render('KaikmediaPagesModule:Block:display.'. $vars['template'] .'.html.twig',
             array('pages' => $pages,
                   'thisPage' => $a['page'],
                   'translator' => $this->translator,
                   'maxPages' => ceil($pages->count() / $a['limit'])))->getContent();
             
        return BlockUtil::themeBlock($blockInfo);
    }
    /**
     * modify block settings
     *
     * @param array $blockInfo a blockInfo structure
     *
     * @return string the block form
     */
    public function modify($blockInfo)
    {
        // Get current content
        $vars = BlockUtil::varsFromContent($blockInfo['content']);
        // Defaults
        $vars['template'] = !empty($vars['template']) ? $vars['template'] : 'list';
        $vars['numitems'] = !empty($vars['numitems']) ? $vars['numitems'] : 5;
        return $this->render('KaikmediaPagesModule:Block:modify.list.html.twig', $vars)->getContent();
    }
    /**
     * update block settings
     *
     * @param array $blockInfo A blockInfo structure.
     *
     * @return array The modified blockInfo structure.
     */
    public function update($blockInfo)
    {
        // Get current content
        $vars = BlockUtil::varsFromContent($blockInfo['content']);
        // alter the corresponding variable
        $vars['template'] = $this->get('request')->request->get('template', null);
        $vars['numitems'] = $this->get('request')->request->get('numitems', null);
        // write back the new contents
        $blockInfo['content'] = BlockUtil::varsToContent($vars);
        return $blockInfo;
    }
}