<?php
/**
 * Copyright (c) KaikMedia.com 2014
 */
namespace Kaikmedia\PagesModule\Block;

use BlockUtil;
use ModUtil;
use SecurityUtil;
use Zikula\Core\Controller\AbstractBlockController;

/**
 * Class PagesListBlock
 * 
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
     * @param array $blockInfo
     *            A blockInfo structure.
     * @return string|void The rendered block.
     */
    public function display($blockInfo)
    {
        // Security check
        if (! SecurityUtil::checkPermission('KaikmediaPagesModule:pageslistblock:', "{$blockInfo['title']}::", ACCESS_READ)) {
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
        
        if (empty($vars['show_ids'])) {
            $vars['show_ids'] = false;
        }
        
        if (empty($vars['show_titles'])) {
            $vars['show_titles'] = false;
        }
        
        if (! ModUtil::available($this->name)) {
            return false;
        }
        
        $a = array();
        $pages = array();
        $a['page'] = 1;
        $a['limit'] = $vars['numitems'];
        $a['title'] = '';
        $a['online'] = 1;
        $a['language'] = $locale;
        $a['sortby'] = 'id';
        $a['sortorder'] = 'ASC';
        
        $repo = $this->get('doctrine.entitymanager')->getRepository('Kaikmedia\PagesModule\Entity\PagesEntity');
        
        if ($vars['show_titles']) {
            $titles_arr = explode(',', $vars['show_titles']);
            foreach ($titles_arr as $title) {
                $a['urltitle'] = $title;
                $pages[] = $repo->getOneBy($a);
            }
            
            $pages = array_filter($pages);
            $maxPages = 5;
        } else {
            // Get parameters from whatever input we need.
            $pages = $repo->getAll($a);
            $maxPages = ceil($pages->count() / $a['limit']);
        }
        
        $blockInfo['content'] = $this->render('KaikmediaPagesModule:Block:display.' . $vars['template'] . '.html.twig', array(
            'pages' => $pages,
            'thisPage' => $a['page'],
            'translator' => $this->translator,
            'maxPages' => $maxPages
        ))->getContent();
        
        return BlockUtil::themeBlock($blockInfo);
    }

    /**
     * modify block settings
     * 
     * @param array $blockInfo
     *            a blockInfo structure
     * @return string the block form
     */
    public function modify($blockInfo)
    {
        // Get current content
        $vars = BlockUtil::varsFromContent($blockInfo['content']);
        // Defaults
        $vars['template'] = ! empty($vars['template']) ? $vars['template'] : 'list';
        $vars['numitems'] = ! empty($vars['numitems']) ? $vars['numitems'] : 5;
        $vars['show_ids'] = ! empty($vars['show_ids']) ? $vars['show_ids'] : '';
        $vars['show_titles'] = ! empty($vars['show_titles']) ? $vars['show_titles'] : '';
        return $this->render('KaikmediaPagesModule:Block:modify.list.html.twig', $vars)->getContent();
    }

    /**
     * update block settings
     * 
     * @param array $blockInfo
     *            A blockInfo structure.
     * @return array The modified blockInfo structure.
     */
    public function update($blockInfo)
    {
        // Get current content
        $vars = BlockUtil::varsFromContent($blockInfo['content']);
        // alter the corresponding variable
        $vars['template'] = $this->get('request')->request->get('template', null);
        $vars['numitems'] = $this->get('request')->request->get('numitems', null);
        $vars['show_ids'] = $this->get('request')->request->get('show_ids', null);
        $vars['show_titles'] = $this->get('request')->request->get('show_titles', null);
        // write back the new contents
        $blockInfo['content'] = BlockUtil::varsToContent($vars);
        return $blockInfo;
    }
}