<?php
namespace hpcom;

class App extends \Divergence\App {
	public static function init($Path)
	{
		error_reporting(E_ALL & ~E_NOTICE);
		return parent::init($Path);	
	}
	public static function getLoadTime() {
		return (microtime(true)-DIVERGENCE_START);
	}
}