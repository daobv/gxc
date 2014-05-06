<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Controller extends FeController
{
	public $defaultAction='render';

	public function allowedActions()
	{
	   	return 'render';
	}               
        
	public function actionRender()
	{				
		$slug=isset($_GET['slug'])? plaintext($_GET['slug']) : false;
		if($slug){
			parent::renderPageSlug($slug);  	
		} else {
			throw new CHttpException('404',t('cms','Oops! Page not found!'));
		}
	    
	}		

}