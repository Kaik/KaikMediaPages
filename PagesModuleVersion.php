<?php
/**
 * Copyright (c) KaikMedia.com 2014
 */
namespace Kaikmedia\PagesModule;

use HookUtil;
use ModUtil;
use Zikula\Component\HookDispatcher\SubscriberBundle;

class PagesModuleVersion extends \Zikula_AbstractVersion
{

    public function getMetaData()
    {
        $meta = array();
        $meta['displayname'] = $this->__('Pages');
        $meta['description'] = $this->__('KMPages');
        $meta['url'] = $this->__('kaikmediapages');
        $meta['oldnames'] = array(
            'KMPages'
        );
        $meta['version'] = '2.0.0';
        $meta['core_min'] = '1.4.0';
        $meta['securityschema'] = array(
            'KaikmediaPagesModule::' => '::'
        );
        $meta['capabilities'] = array(
            HookUtil::SUBSCRIBER_CAPABLE => array(
                'enabled' => true
            )
        );
        $meta['securityschema'] = array(
            'KaikmediaPagesModule::' => 'Page name::Page ID'
        );
        // Module depedencies
        $meta['dependencies'] = array(
            array(
                'modname' => 'Scribite',
                'minversion' => '4.3.0',
                'maxversion' => '',
                'status' => ModUtil::DEPENDENCY_RECOMMENDED
            )
        );
        
        return $meta;
    }

    /**
     * Define the hook bundles supported by this module.
     * 
     * @return void
     */
    protected function setupHookBundles()
    {
        $bundle = new SubscriberBundle($this->name, 'subscriber.kaikmediapages.ui_hooks.page', 'ui_hooks', $this->__('KMPages Hooks'));
        $bundle->addEvent('display_view', 'kaikmediapages.ui_hooks.page.display_view');
        $bundle->addEvent('form_edit', 'kaikmediapages.ui_hooks.page.form_edit');
        $bundle->addEvent('form_delete', 'kaikmediapages.ui_hooks.page.form_delete');
        $bundle->addEvent('validate_edit', 'kaikmediapages.ui_hooks.page.validate_edit');
        $bundle->addEvent('validate_delete', 'kaikmediapages.ui_hooks.page.validate_delete');
        $bundle->addEvent('process_edit', 'kaikmediapages.ui_hooks.page.process_edit');
        $bundle->addEvent('process_delete', 'kaikmediapages.ui_hooks.page.process_delete');
        $this->registerHookSubscriberBundle($bundle);
    }
}