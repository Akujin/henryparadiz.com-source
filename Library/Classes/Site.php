<?php

class Site
{
	// config properties
	static public $debug = true;	
	static public $autoCreateSession = false;
	static public $onInitialized;
	static public $onNotFound;
	static public $onRequestMapped;

	// public properties
	//static public $ID;
	static public $Title;
	static public $rootPath;

	static public $webmasterEmail = 'henry.paradiz@gmail.com';

	static public $requestURI;
	static public $requestPath;
	static public $pathStack;

	static public $config;

	// protected properties
	static protected $_rootCollections;

		
	static public function initialize()
	{
	
		// get site root
		if(empty(static::$rootPath))
		{
			if(!empty($_SERVER['SITE_ROOT']))
				static::$rootPath = $_SERVER['SITE_ROOT'];
			else
				throw new Exception('No Site root detected');
		}
		
		// load config
		if(!(static::$config = apc_fetch($_SERVER['HTTP_HOST'])) || ($_GET['_recache']==static::$controlKey))
		{
            if(is_readable(static::$rootPath.'/site.json'))
            {
			    static::$config = json_decode(file_get_contents(static::$rootPath.'/site.json'), true);
			    apc_store($_SERVER['HTTP_HOST'], static::$config);
            }
            else if(is_readable(static::$rootPath.'/Site.config.php'))
            {
                include(static::$rootPath.'/Site.config.php');
                apc_store($_SERVER['HTTP_HOST'], static::$config);
            }
		}
		
			
		
		// get request URI
		if(empty(static::$requestURI))
			static::$requestURI = parse_url($_SERVER['REQUEST_URI']);
			
		// get path stack
		static::$pathStack = static::$requestPath = static::splitPath(static::$requestURI['path']);
		
		// register class loader
		spl_autoload_register('Site::loadClass');
		
		// set error handle
		set_error_handler('Site::handleError');
		
		// register exception handler
		set_exception_handler('Site::handleException');
		
		// check virtual system for site config
		static::loadConfig(__CLASS__);
		
		
		if(is_callable(static::$onInitialized))
			call_user_func(static::$onInitialized);
	}

	
	
	
	
	static public function handleError($errno, $errstr, $errfile, $errline)
	{
		if(!(error_reporting() & $errno))
			return;
		
		if(substr($errfile, 0, strlen(static::$rootPath)) == static::$rootPath)
		{
			$fileID = substr(strrchr($errfile, '/'), 1);
			$File = SiteFile::getByID($fileID);

			$errfile .= ' ('.$File->Handle.')';
		}
			
		die("<h1>Error</h1><p>$errstr</p><p><b>Source:</b> $errfile<br /><b>Line:</b> $errline<br /><b>Author:</b> {$File->Author->Username}<br /><b>Timestamp:</b> ".date('Y-m-d h:i:s', $File->Timestamp)."</p>");
	}
	
	static public function handleException($e)
	{
		die('<h1>Unhandled Exception</h1><p>'.get_class($e).': '.$e->getMessage().'</p><h1>Backtrace:</h1><pre>'.$e->getTraceAsString().'</pre><h1>Exception Dump</h1><pre>'.print_r($e,true).'</pre>');
	}
	
	static public function respondNotFound($message = 'Page not found')
	{
		if(is_callable(static::$onNotFound))
		{
			call_user_func(static::$onNotFound, $message);
		}
		else
		{
			header('HTTP/1.0 404 Not Found');
			die($message);
		}
	}
	
	static public function respondBadRequest($message = 'Cannot display resource')
	{
		header('HTTP/1.0 400 Bad Request');
		die($message);
	}
	
	static public function respondUnauthorized($message = 'Access denied')
	{
		header('HTTP/1.0 403 Forbidden');
		die($message);
	}
	
	
	static public function getRootCollection($handle)
	{
		if(!empty(static::$_rootCollections[$handle]))
			return static::$_rootCollections[$handle];
				
		return static::$_rootCollections[$handle] = SiteCollection::getOrCreateRootCollection($handle);
	}


	static public function splitPath($path)
	{
		return explode('/', ltrim($path, '/'));
	}
	
	static public function redirect($path, $get = false, $hash = false)
	{
		if(is_array($path)) $path = implode('/', $path);
		
		if(preg_match('/^https?:\/\//i', $path))
			$url = $path;
		else
			$url = ($_SERVER['HTTPS'] ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($path, '/');

		if($get)
		{
			$url .= '?' . (is_array($get) ? http_build_query($get) : $get);
		}
	
		if($hash)
		{
			$url .= '#' . $hash;	
		}
		
		header('Location: ' . $url);
		exit();
	}

	static public function getPath($index = null)
	{
		if($index === null)
			return static::$requestPath;
		else
			return static::$requestPath[$index];
	}

	static public function matchPath($index, $string)
	{
		return 0==strcasecmp(static::getPath($index), $string);
	}
    
    static public function prepareOptions($value, $defaults = array())
    {
        if(is_string($value))
        {
            $value = json_decode($value, true);
        }
        
        return is_array($value) ? array_merge($defaults, $value) : $defaults;
    }
}