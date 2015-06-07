<?php
/**
 * Copyright (c) KaikMedia.com 2014
 */
namespace Kaikmedia\PagesModule\Twig;

use Symfony\Component\HttpFoundation\Session\Session;

class PagesExtension extends \Twig_Extension
{
    private $session;

    public function __construct(Session $session = null)
    {
        $this->session = $session;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('statusicon', array(
                $this,
                'statusIcon'
            ))
        );
    }

    public function getFunctions()
    {
        return array(
            'currentuser' => new \Twig_Function_Method($this, 'getCurrentUser', array(
                'is_safe' => array(
                    'html'
                )
            ))
        );
    }

    /**
     * status icon hardcoded
     * 
     * @param
     *            $value
     * @return string
     * @todo
     */
    public function statusIcon($value)
    {
        switch ($value) {
            case 1:
                return 'circle text-primary ';
            case 0:
                return 'archive';
            case 3:
                return 'trash';
        }
    }

    /**
     * 
     * @param array $params            
     * @return string
     */
    public function getCurrentUser(array $params = array())
    {
        $result = \UserUtil::getVar('uname');
        
        return $result;
    }

    public function getName()
    {
        return 'pages_extension';
    }
}