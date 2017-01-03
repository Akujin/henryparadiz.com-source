<?php


class PhotoMedia extends EmergenceMedia
{

	// configurables
	public static $jpegCompression = 90;
	
	// magic methods
	static public function __classLoaded()
	{
		$className = get_called_class();
		
		EmergenceMedia::$mimeHandlers['image/gif'] = $className;
		EmergenceMedia::$mimeHandlers['image/jpeg'] = $className;
		EmergenceMedia::$mimeHandlers['image/png'] = $className;
		
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
					case 'image/gif':
						return 'gif';
						
					case 'image/jpeg':
						return 'jpg';
						
					case 'image/png':
						return 'png';
						
					default:
						throw new Exception('Unable to find photo extension for mime-type: ' . $this->MIMEType);
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
			throw new Exception('Failed to read image file information');
		}
		
		// store image data
		$mediaInfo['width'] = $mediaInfo['imageInfo'][0];
		$mediaInfo['height'] = $mediaInfo['imageInfo'][1];
		$mediaInfo['duration'] = 0;
	
		return $mediaInfo;
	}
	

}