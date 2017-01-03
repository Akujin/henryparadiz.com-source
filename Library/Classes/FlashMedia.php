<?php
class FlashMedia extends EmergenceMedia
{
	
	// magic methods
	static public function __classLoaded()
	{
		$className = get_called_class();
		
		EmergenceMedia::$mimeHandlers['application/x-shockwave-flash'] = $className;
		
		parent::__classLoaded();
	}
		
	
	function __get($name)
	{
		switch($name)
		{
			case 'JsonTranslation':
				return array_merge(parent::__get($name), array(
				));
			
				
			case 'Extension':

				switch($this->MIMEType)
				{
					case 'application/x-shockwave-flash':
						return 'swf';
					
					default:
						throw new Exception('Unable to find extension for mime-type: ' . $this->MIMEType);
				}	
				
			default:
				return parent::__get($name);
		}
	}
	
	
	// public methods
		
	
	// static methods
	static public function analyzeFile($filename, $mediaInfo = array())
	{
		if(!$mediaInfo['imageInfo'] = @getimagesize($filename))
		{
			throw new Exception('Failed to read flash file information');
		}
		
		// store image data
		$mediaInfo['width'] = $mediaInfo['imageInfo'][0];
		$mediaInfo['height'] = $mediaInfo['imageInfo'][1];
		$mediaInfo['duration'] = 0;
	
		return $mediaInfo;
	}
}