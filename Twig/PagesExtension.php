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
            new \Twig_SimpleFilter('yesno', array(
                $this,
                'yesNo'
            )),
            new \Twig_SimpleFilter('statusicon', array(
                $this,
                'statusIcon'
            ))
        );
    }

    public function getFunctions()
    {
        return array(
            'getstatusmsg' => new \Twig_Function_Method($this, 'getStatusMsg', array(
                'is_safe' => array(
                    'html'
                )
            )),
            'currentuser' => new \Twig_Function_Method($this, 'getCurrentUser', array(
                'is_safe' => array(
                    'html'
                )
            ))
        );
    }

    /**
     * convert a string|integer|boolean to "Yes" or "No" string
     * 
     * @param
     *            $value
     * @return string
     */
    public function yesNo($value)
    {
        return (bool) $value ? __("Yes") : __("No");
    }

    /**
     * convert a string|integer|boolean to "Yes" or "No" string
     * 
     * @param
     *            $value
     * @return string
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
     * Display flash messages in twig template.
     * Defaults to bootstrap alert classes.
     * <pre>
     * {{ getstatusmsg() }}
     * {{ getstatusmsg({'class': 'custom-class', 'tag': 'span'}) }}
     * </pre>
     * 
     * @param array $params            
     * @return string
     */
    public function getStatusMsg(array $params = array())
    {
        $result = '';
        
        $total_messages = array();
        
        $messageTypeMap = array(
            \Zikula_Session::MESSAGE_ERROR => 'danger',
            \Zikula_Session::MESSAGE_WARNING => 'warning',
            \Zikula_Session::MESSAGE_STATUS => 'success'
        );
        
        foreach ($messageTypeMap as $messageType => $bootstrapClass) {
            /**
             * Get messages.
             */
            $messages = $this->session->getFlashBag()->get($messageType);
            
            if (count($messages) > 0) {
                /**
                 * Set class for the messages.
                 */
                $class = (! empty($params['class'])) ? $params['class'] : "alert alert-$bootstrapClass";
                
                $total_messages = $total_messages + $messages;
                
                /**
                 * Build output of the messages.
                 */
                if (empty($params['tag']) || ($params['tag'] != 'span')) {
                    $params['tag'] = 'div';
                }
                
                $result .= '<' . $params['tag'] . ' class="' . $class . '"';
                
                if (! empty($params['style'])) {
                    $result .= ' style="' . $params['style'] . '"';
                }
                
                $result .= '>';
                $result .= implode('<hr />', $messages);
                $result .= '</' . $params['tag'] . '>';
            }
        }
        
        if (empty($total_messages)) {
            return "";
        }
        
        return $result;
    }

    /**
     * Display flash messages in twig template.
     * Defaults to bootstrap alert classes.
     * <pre>
     * {{ getstatusmsg() }}
     * {{ getstatusmsg({'class': 'custom-class', 'tag': 'span'}) }}
     * </pre>
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

    /**
     * Add the core config variable settings to twig templates
     * 
     * @return array
     */
    public function getGlobals()
    {
        return array(
            'ZConfig' => \ModUtil::getVar(\ModUtil::CONFIG_MODULE)
        );
    }
}