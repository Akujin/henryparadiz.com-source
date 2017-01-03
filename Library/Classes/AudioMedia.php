<?php



 class AudioMedia extends EmergenceMedia
{

	// configurables
	public static $previewExtractCommand = 'ffmpeg -i %1$s -ss %3$u -t %4$u -f mp3 -y %2$s'; // 1=input file, 2=output file, 3=start time, 4=duration
	public static $previewDuration = 30;
	public static $iconPath = '/var/mics/code/Static/img/icons/filetypes/mp3.png';
	
	
	// magic methods
	static public function __classLoaded()
	{
		$className = get_called_class();
		
		EmergenceMedia::$mimeHandlers['audio/mpeg'] = $className;
		
		parent::__classLoaded();
	}
	
		
	function __get($name)
	{
		switch($name)
		{
			case 'JsonTranslation':
				return array_merge(parent::__get($name), array(
				));
			
			case 'ThumbnailMIMEType':
				return 'image/png';
				
			case 'Width':
				return 128;
			
			case 'Height':
				return 128;
				
			case 'Extension':

				switch($this->MIMEType)
				{
					case 'audio/mpeg':
						return 'mp3';
					default:
						throw new Exception('Unable to find audio extension for mime-type: ' . $this->MIMEType);
				}
				
			default:
				return parent::__get($name);
		}
	}
	
	
	// public methods
	static public function getBlankPath($contextClass)
	{
		return static::$iconPath;
	}
	
	public function getImage($sourceFile = null)
	{
		if (!isset($sourceFile))
		{
			$sourceFile = $this->BlankPath;
		}

		return imagecreatefromstring(file_get_contents($sourceFile));
	}
	
	public function createPreview()
	{
		// check if a preview already exists
	
		if(!empty($_REQUEST['startTime']) && is_numeric($_REQUEST['startTime']) && ($_REQUEST['startTime'] >= 0) && ($_REQUEST['startTime'] < $this->Duration))
		{
			$startTime = $_REQUEST['startTime'];
		}
		else
		{
			$startTime = 0;
		}
	
		$previewPath = tempnam('/tmp', 'mediaPreview');
		
		// generate preview
		$cmd = sprintf(static::$previewExtractCommand, $this->FilesystemPath, $previewPath, $startTime, static::$previewDuration);
		shell_exec($cmd);
		
		if(!filesize($previewPath))
		{
			throw new Exception('Preview output is empty');
		}
		
		// create media instance
		$PreviewMedia = Media::createFromFile($previewPath, array(
			'ContextClass' => 'Media'
			,'ContextID' => $this->ID
			,'Caption' => sprintf('%u sec preview (%us-%us)', static::$previewDuration, $startTime, $startTime+static::$previewDuration)
		));

		return $PreviewMedia;
	}
	
	// static methods
	static public function analyzeFile($filename, $mediaInfo = array())
	{
		// Initialize getID3 engine
		$getID3 = new getID3();

		$mediaInfo['id3Info'] = $getID3->analyze($filename);
		
		$mediaInfo['width'] = 0;
		$mediaInfo['height'] = 0;
		$mediaInfo['duration'] = $mediaInfo['id3Info']['playtime_seconds'];
	
		return $mediaInfo;
	}

}