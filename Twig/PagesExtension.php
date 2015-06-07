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
    
    public function getName()
    {
        return 'pages_extension';
    }
}