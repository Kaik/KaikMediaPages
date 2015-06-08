<?php
/**
 * Copyright (c) KaikMedia.com 2014
 */
namespace Kaikmedia\PagesModule\Controller;

use DataUtil;
use ModUtil;
use SecurityUtil;
use Symfony\Component\HttpFoundation\JsonResponse;
use Zikula\Core\Response\Ajax\AjaxResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use System;
use UserUtil;
use ServiceUtil;
use ZLanguage;
use Zikula\Core\Controller\AbstractController;
use Zikula\Core\Event\GenericEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route; // used in annotations - do not remove
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method; // used in annotations - do not remove
use Symfony\Component\Routing\RouterInterface;
use Kaikmedia\PagesModule\Entity\ImageEntity as File;

/**
 * @Route("/ajax/gallery")
 * Class AplicantsAjaxController
 * 
 * @package Kaikmedia\KmdModule\Controller
 */
class GalleryAjaxController extends AbstractController
{
    /**
     * @Route("/modify/{id}", options={"expose"=true})
     * @Method("GET")
     * Modify aplicant information.
     *
     * @param Request $request
     * @param integer $id
     *            Parameters passed via GET:
     *            --------------------------------------------------
     *            string uname The user name of the account for which profile information should be modified; defaults to the uname of the current user.
     *            dynadata array The modified profile information passed into this function in case of an error in the update function.
     * @return RedirectResponse|string The rendered template output.
     * @throws AccessDeniedException on failed permission check
     */
    public function modifyAction(Request $request, $id = null)
    {
    	// Security check
    	if (!UserUtil::isLoggedIn() || !SecurityUtil::checkPermission($this->name.'::', '::', ACCESS_ADMIN)) {
    		throw new AccessDeniedException();
    	}

    	$mode = 'html';
    	
        if($id == null){
	        $file = new File();
	        $file->setPromoted(false);
	        	        
        }else{
	        $file = $this->get('doctrine.entitymanager')->getRepository('Kaikmedia\PagesModule\Entity\ImageEntity')
	                    ->getOneBy(array('id' => $id));    
        }
         
        $options = array();
        $options['isXmlHttpRequest'] = $request->isXmlHttpRequest();
        $options['action'] =  $this->get('router')->generate('kaikmediapagesmodule_galleryajax_modify', array(), RouterInterface::ABSOLUTE_URL);
        $form = $this->createForm('images', $file, $options);

        //$form->bindRequest($request);
        
        if ($request->getMethod() == "POST"){
        	$form->handleRequest($request);
        	if ($form->isValid())
        	{
			    $em = $this->getDoctrine()->getManager();
			    $em->persist($file);
			    $em->flush();              	
			    $template = $file->getName();
			$response = new Response(json_encode(array('template' => $template)));
			$response->headers->set('Content-Type', 'application/json');
			return $response;
	       }else {
	           $errors = array();
	           foreach ($form->getErrors() as $key => $error) {
	               $errors[] = $error->getMessage();
	           }	           
	           $response = new Response(json_encode(array('template' => $errors)));
	           $response->headers->set('Content-Type', 'application/json');
	           return $response;	           
	       }
        }
        
        if($mode == 'html'){
	       $template = $this->renderView('KaikmediaPagesModule:Gallery:new.form.html.twig', array(
            'form' => $form->createView(),
            'file' => $file));
        }else {
        	$template['id'] = $file->getId();
        	$template['name'] = $file->getName();
        }        
         
        
		$response = new Response(json_encode(array('template' => $template)));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
    }
    
    /**
     * @Route("/add/", options={"expose"=true})
     * @Method("POST")
     * Modify aplicant information.
     * 
     * @param Request $request            
     * @param integer $id
     *            Parameters passed via GET:
     *            --------------------------------------------------
     *            string uname The user name of the account for which profile information should be modified; defaults to the uname of the current user.
     *            dynadata array The modified profile information passed into this function in case of an error in the update function.
     * @return RedirectResponse|string The rendered template output.
     * @throws AccessDeniedException on failed permission check
     */
    public function addAction(Request $request)
    {
        // Security check
        if (! SecurityUtil::checkPermission($this->name . '::view', '::', ACCESS_READ)) {
            throw new AccessDeniedException();
        }
        
        $file = new File(); 
        $file->setPromoted(false);       
        $options = array();
        $options['isXmlHttpRequest'] = $request->isXmlHttpRequest();
        $form = $this->createForm('images', $file, $options);

        if ($request->getMethod() == "POST"){
            $a = $form->isValid();
            $form->handleRequest($request);
            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($file);
                $em->flush();
                $data = array();
                $data['file'] = $file->toArray();
                $data['homeurl'] = $this->getRequest()->getScheme().'://'.$this->getRequest()->getHttpHost();                                                              
                $response = new Response(json_encode($data));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }else {          
	           $response = new Response(json_encode(array('template' => $form->getErrorsAsString())));
	           $response->headers->set('Content-Type', 'application/json');
	           return $response;	           
	       }
        }        
               
        $response = new Response(json_encode(array(
            'template' => $a
        )));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    /**
     * @Route("/get/{file}", options={"expose"=true})
     * @Method("GET")
     * Modify aplicant information.
     *
     * @param Request $request
     * @param integer $id
     *            Parameters passed via GET:
     *            --------------------------------------------------
     *            string uname The user name of the account for which profile information should be modified; defaults to the uname of the current user.
     *            dynadata array The modified profile information passed into this function in case of an error in the update function.
     * @return RedirectResponse|string The rendered template output.
     * @throws AccessDeniedException on failed permission check
     */
    public function getAction(Request $request, File $file)
    {
        // Security check
        if (!SecurityUtil::checkPermission($this->name . '::view', '::', ACCESS_READ)) {
            throw new AccessDeniedException();
        }
        
        if (!$file){
            throw new AccessDeniedException();            
        }
        
        $mode = 'html';       
        if($mode == 'html'){
            $template = $this->renderView('KaikmediaPagesModule:Gallery:element.html.twig', array(
                'file' => $file));
        }else {
            $template['id'] = $file->getId();
            $template['name'] = $file->getName();
        }
                
        $response = new Response(json_encode(array('template' => $template)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;        
         
    }
    
    /**
     * Create and configure the view for the controller.
     *
     * NOTE: This is necessary because the Zikula_Controller_AbstractAjax overrides this method located in Zikula_AbstractController.
     */
    protected function configureView()
    {
        $this->setView();
        $this->view->setController($this);
        $this->view->assign('controller', $this);
    }    
}
