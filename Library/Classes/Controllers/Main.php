<?php
namespace Controllers;

class Main extends \RequestHandler
{

	public static function handleRequest()
	{
	
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/site.LOCK'))
        {
            echo file_get_contents($_SERVER['DOCUMENT_ROOT'].'/down.html');
            exit;   
        }

		/* 
		 * This is to make sure any page that loads
		 * through Apache's ErrorDocument returns 200
		 * instead of 404.
		 */ 
		header('HTTP/1.0 200 OK');
		header('X-Powered-By: PHP/' . phpversion() . ' Emergence Framework (http://emr.ge) Henry\'s Revision');
		
		
	    
        /*if(in_array($_SERVER['REMOTE_ADDR'],Site::$doNotTrack))
        {
	    	\php_error\reportErrors(array(
	    		'error_reporting_on'		=>	E_ALL & ~E_NOTICE & ~E_STRICT
	    		,'catch_supressed_errors'	=>	false
	    		,'catch_ajax_errors'		=>	false
	    		,'background_text'			=>	'AEGIS Digital'
	    	));
	    	
	    	if($_GET['dev'])
	    	{
		    	//asjkbfkjbf();
	    	}
        }
        else
        {
            error_reporting(0);
        }*/

		switch($action = $action?$action:static::shiftPath())
		{
		
			/* PHP INFO */
			case 'info':
				phpinfo();
				exit;
			
			case '':
				$action = 'home';

			case 'work':
				$action = 'work'.(static::peekPath()?'/'.static::shiftPath():'');
			
			default:
				if(file_exists(templates_directory.$action.'.tpl'))
				{

					return static::respond(templates_directory.$action.'.tpl');
				}
				return Errors::handleRequest();
		}
	}
}
