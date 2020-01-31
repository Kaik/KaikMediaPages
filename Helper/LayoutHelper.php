<?php

/**
 * KaikMedia PagesModule
 *
 * @package    KaikmediaPagesModule
 * @author     Kaik <contact@kaikmedia.com>
 * @copyright  KaikMedia
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @link       https://github.com/Kaik/KaikMediaNews.git
 */

namespace Kaikmedia\PagesModule\Helper;

use Symfony\Component\Finder\Finder;

class LayoutHelper
{
    private $finder;
    private $layouts = [];
    private $cards = [];

    public function __construct() 
    {
        $this->finder = new Finder();
    }
    /*
     * Used in lists blocks
     */
    public function getCardsList()
    {
        $this->finder->files()->name('*.twig');
        $this->finder->files()->in(__DIR__.'/../Resources/views/Cards/');
        foreach ($this->finder as $key => $file) {
            $this->cards[] = $file->getRelativePathname();
        }

        return $this->cards;
    }
    
    /*
     * Used in full view
     */
    public function getLayoutsList()
    {
        $this->finder->files()->name('*.twig');
        $this->finder->files()->in(__DIR__.'/../Resources/views/Layouts/');
        foreach ($this->finder as $key => $file) {
            $this->layouts[] = $file->getRelativePathname();
        }

        return $this->layouts;
    }
    
    /*
     * Used in full view
     */
    public function getLayoutChoices()
    {
        $layouts = $this->getLayoutsList();
        $choices = [];
        foreach ($layouts as $layoutFileName) {
            $choices[ucfirst(str_replace('.html.twig', '', $layoutFileName))] =  str_replace('.html.twig', '', $layoutFileName);
        }
        
        return $choices;
    }
    
    /*
     * Used in full view
     */
    public function getLayout($layout)
    {
//        $this->finder->files()->name('*.twig');
//        $this->finder->files()->in(__DIR__.'/../Resources/views/Layouts/');
//        $layouts = [];
//        foreach ($this->finder as $key => $file) {
//            $layouts[] = $file->getRelativePathname();
//        }
//                        {% include '@KaikmediaNewsModule/Cards/full.html.twig' %}#}
// @KaikmediaNewsModule/Layouts/'. (!empty($layout) ? $layout . '.html.twig' : 'default.html.twig
        
        
        $fullPath = '@KaikmediaPagesModule/Layouts/default.html.twig' ;
    
        return $fullPath;
    }
    
    /*
     * Used in full view
     */
    public function getCard($card)
    { 
        $fullPath = '@KaikmediaPagesModule/Cards/'. (!empty($card)) ? $card . '.html.twig' : 'default.html.twig' ;
    
        return $fullPath;
    }
    
    /*
     * Used in full view
     */
    public function getPreview($preview_type = 'layout', $preview_name)
    {
        $fullPath = '@KaikmediaPagesModule/'. ucfirst($preview_type) .'s/'. ((!empty($preview_name)) ? $preview_name . '.html.twig' : 'default.html.twig');
        return $fullPath;
    }
}