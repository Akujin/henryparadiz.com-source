<?php
namespace hpcom\Controllers;

use Divergence\IO\Database\MySQL as DB;

use \hpcom\Controllers\Records\BlogPost as BlogPost;
use \hpcom\App as App;

class API extends \Divergence\Controllers\RequestHandler
{
	
	/*
	 * check if logged in and show login page if not
	 */
	
	public static function handleRequest()
	{	
		switch($action = $action?$action:static::shiftPath())
		{
			case 'blogpost':
				return \hpcom\Controllers\Records\BlogPost::handleRequest();
			
		}
	}	
}