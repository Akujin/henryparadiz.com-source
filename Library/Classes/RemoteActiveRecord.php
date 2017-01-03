<?php
class RemoteActiveRecord extends ActiveRecord
{
							/*
						*		*
					*				*
				*						*
			*								*
		*										*
	*												*/

	static public $remoteStore;

	static public $remoteKey;
	
	static public $_record_cache;
	
	static public $_timeout = 0; // See: CURLOPT_CONNECTTIMEOUT @ http://php.net/manual/en/function.curl-setopt.php
	
	/*
	 *	refactor these methods to their own class
	 *	compatible with DB:: but for external HTTP.
	 *
	 *	The $remoteKey / $remoteStory ^^ above ^^
	 *	should actually be in there.
	 */
	
	static public function oneRecord($query, $params)
	{
		//$data = file_get_contents($query);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $query);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		curl_close($ch);
    	
		$Data = json_decode($data,true);
		
		if(!is_array($Data['data']))
		{
			throw new Exception('Invalid return');	
		}
		else
		{
			if($Data['data'][0])
			{
				return $Data['data'][0];
			}
			else
			{
				return false;
			}
		}
	}
	
	
	static public function oneRecordCached($key,$query,$params)
	{
		if(self::$_record_cache[$key])
		{
			return self::$_record_cache[$key];
		}
		else
		{
			
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $query);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, static::$_timeout);
			$data = curl_exec($ch);
			curl_close($ch);
		
			//$data = file_get_contents($query);
    	
			//var_dump($data);
    	
			$Data = json_decode($data,true);
			
			//echo $query;
			
			//var_dump($Data); exit;
	    
			self::$_record_cache[$key] = $Data['data'][0];
	    
			return self::$_record_cache[$key];
		}
	}
	
	/*												*
		*										*
			*								*
				*						*
					*				*
						*		*
							*/
	
    static public function getRecordByField($field, $value, $cacheIndex = false)
    {	
    	$filter = array(array(
    		'property'	=>	$field
    		,'value'	=>	$value
    	));
    	
    	$URL = static::$remoteStore . '?Key=' . static::$remoteKey . '&filter=' . json_encode($filter);
    	
    	if($cacheIndex)
    	{
	    	$key = sprintf('%s', md5($URL));
	    	return static::oneRecordCached($key,$URL,array());
    	}
    	else
    	{
	    	return static::oneRecord($URL, array());
    	}
    }
    
    static public function getRecordByWhere($conditions, $options = array())
    {
    	foreach($conditions as $property=>$value)
    	{
	    	$Filter[] = array(
		    	'property'	=>	$property
	    		,'value'	=>	$value
	    	);
    	}
    
    	$QueryString = http_build_query($options);
    
		$URL = static::$remoteStore . '?Key=' . static::$remoteKey . '&' . $QueryString . '&filter=' . json_encode($Filter);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, static::$_timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		
		if(empty($data))
		{
			throw new Exception('No data has been returned from the remote store. '.$URL);
		}
		
		$Data = json_decode($data,true);
    
		return $Data['data'][0];
    }
    
    static public function getAllRecordsByWhere($conditions = array(), $options = array())
    {
    	$QueryString = http_build_query($options);
    
		$URL = static::$remoteStore . '?Key=' . static::$remoteKey . '&' . $QueryString . '&filter=' . json_encode($conditions);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, static::$_timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		
		if(is_integer(strpos($data,'Fatal error: Allowed memory size')))
		{
			throw new Exception('Remote store ran out of memory. Try imposing a limit.');
		}
		
		if(empty($data))
		{
			throw new Exception('No data has been returned from the remote store. '.$URL);
		}
		
		$Data = json_decode($data,true);
    
		return $Data['data'];
    }
    
    public function save($deep = true)
    {
        // set creator
        if(static::_fieldExists('CreatorID') && !$this->CreatorID && $_SESSION['User'])
        {
            $this->CreatorID = $_SESSION['User']->ID;
        }
        
        // set created
        if(static::_fieldExists('Created') && (!$this->Created || ($this->Created == 'CURRENT_TIMESTAMP')))
        {
            $this->Created = time();
        }
        
        // validate
        if(!$this->validate($deep))
        {
            throw new RecordValidationException($this, 'Cannot save invalid record');
        }
        
        if($this->isDirty)
        {
            // prepare record values
            $recordValues = $this->_prepareRecordValues();
    
            // transform record to set array
            $set = static::_mapValuesToSet($recordValues);
            
            // create new or update existing
            if($this->_isPhantom)
            {
            
                //do create
                $URL = static::$remoteStore . '/create?Key=' . static::$remoteKey;
                
                $Context = stream_context_create(array(
                	'http'	=>	array(
                		'method'	=>	'POST'
                		,'content'	=>	http_build_query($recordValues)
                	)
                ));
                
                $data = file_get_contents($URL,null,$Context);
                
                $Data = json_decode($data,true);
                
                if($Data['success'])
                {
	                $this->_record[static::$primaryKey?static::$primaryKey:'ID'] = $Data['data'][static::$primaryKey?static::$primaryKey:'ID'];
	                $this->_isPhantom = false;
	                $this->_isNew = true;
                }
                else
                {
	                throw new Exception('Saving to remote storage failed.');
                }
            }
            elseif(count($set))
            {
                // do edit
                
                $PKValue = $this->getValue(static::$primaryKey?static::$primaryKey:'ID');
                
                $URL = static::$remoteStore . '/' . $PKValue . '/edit?Key=' . static::$remoteKey;
                
                $Context = stream_context_create(array(
                	'http'	=>	array(
                		'method'	=>	'POST'
                		,'content'	=>	http_build_query($recordValues)
                	)
                ));
                
                $data = file_get_contents($URL,null,$Context);
                
                $Data = json_decode($data,true);
                
                if($Data['success'])
                {
                	$this->_isUpdated = true;
                }
                else
                {
	                throw new Exception('Saving to remote storage failed.');
                }
            }
            
            // update state
            $this->_isDirty = false;
        }
    }
}