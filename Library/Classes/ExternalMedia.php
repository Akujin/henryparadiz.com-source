<?php
class ExternalMedia extends EmergenceMedia
{
	
	static public $defaultThumbnailIcon = '/var/www/images/File Media.png';
	
	// magic methods
	static public function __classLoaded()
	{
		$className = get_called_class();
		
		EmergenceMedia::$mimeHandlers['text/plain'] = $className;
		
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
					case 'text/plain':
						return 'txt';
					
					default:
						return 'txt';
						throw new Exception('Unable to find extension for mime-type: ' . $this->MIMEType);
				}	
				
			/*case 'FilesystemPath':

				if($this->ID == false)
				{
					return false;
				}

				return static::$defaultThumbnailIcon;*/
				
			default:
				return parent::__get($name);
		}
	}
	
	
	// public methods
	public function getImage($sourceFile = null)
	{
		return parent::getImage(static::$defaultThumbnailIcon);
	}
	
	public function getConstrainedSize($maxWidth, $maxHeight)
	{
		if(!$mediaInfo['imageInfo'] = @getimagesize(static::$defaultThumbnailIcon))
		{
			throw new Exception('Failed to read flash file information');
		}
		
		// get image sizes
		$width = $mediaInfo['imageInfo'][0];
		$height = $mediaInfo['imageInfo'][1];
		
		
		// calculate scale ratios
		$widthRatio = ($width > $maxWidth) ? ($maxWidth / $width) : 1;
		$heightRatio = ($height > $maxHeight) ? ($maxHeight / $height) : 1;
		
		$ratio = min($widthRatio, $heightRatio);

		return array(
			'width' => round($width * $ratio)
			, 'height' => round($height * $ratio)
		);
	}
	
	public function getThumbnail($maxWidth, $maxHeight, $fillColor = false)
	{
		// init thumbnail path
		$thumbFormat = sprintf('%ux%u', $maxWidth, $maxHeight);
		
		if ($fillColor)
		{
			$thumbFormat .= 'x'.strtoupper($fillColor);
		}
		
		$thumbPath = static::$mediaPath.'/media/'.$thumbFormat.'/'.$this->Filename;
		
		
		if(!$mediaInfo['imageInfo'] = @getimagesize(static::$defaultThumbnailIcon))
		{
			throw new Exception('Failed to read flash file information');
		}
		
		// get image sizes
		$this->Width = $mediaInfo['imageInfo'][0];
		$this->Height = $mediaInfo['imageInfo'][1];
		
		
		// get mime type
		$finfo = finfo_open(FILEINFO_MIME, static::$magicPath);
		
		if(!$finfo || !($mimeInfo = finfo_file($finfo, static::$defaultThumbnailIcon)) )
		{
			throw new Exception('Unable to load media file info');
		}

		finfo_close($finfo);

		// split mime type
		$p = strpos($mimeInfo, ';');
		$this->MIMEType = $p ? substr($mimeInfo, 0, $p) : $mimeInfo;
		
		
		// look for cached thumbnail
		if (!file_exists($thumbPath))
		{
			
			// create new thumbnail
			$thumbnail = $this->createThumbnailImage($maxWidth, $maxHeight, $fillColor);
			
			
			// save thumbnail to cache
			$thumbDir = dirname($thumbPath);
			if (!is_dir($thumbDir))
			{
				mkdir($thumbDir, static::$newDirectoryPermissions, true);
			}
			
			switch($this->MIMEType)
			{
				case 'text/gif':
					imagegif($thumbnail, $thumbPath);
					break;
				
				case 'image/jpeg':
					imagejpeg($thumbnail, $thumbPath, static::$thumbnailJPEGCompression);
					break;
					
				case 'image/png':
					imagepng($thumbnail, $thumbPath, static::$thumbnailPNGCompression);
					break;
					
				default:
					throw new Exception('Unhandled thumbnail format');		
			}
			
			chmod($thumbPath, static::$newFilePermissions);		
		}
		
		
		// return path
		return $thumbPath;
	}
	
	// static methods
	static public function analyzeFile($filename, $mediaInfo = array())
	{
		
		// store image data
		$mediaInfo['width'] = 0;
		$mediaInfo['height'] = 0;
		$mediaInfo['duration'] = 0;
	
		$mediaInfo['className'] = 'ExternalMedia';
	
		return $mediaInfo;
	}
}