<?php

class Date
{
	static private $aRegMonthEnds = array (0,31,28,31,30,31,30,31,31,30,31,30,31);
	static private $aLeapMonthEnds = array (0,31,29,31,30,31,30,31,31,30,31,30,31);
	static private $aFullMonths = array ('', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	
	public $iYYYY;
	public $iMM;
	public $iDD;
	public $sDate;
	public $aMonthEnds = array();
	public $aErrors = array();
	
	/*************
	 * Constructor
	**************/
	public function __construct($date)	// if not array, format = 'YYYY-mm-dd'
	{
		if (is_array($date))
		{
			$this->iMM = intval( (isset($date['MM']) ? $date['MM'] : date('m') ) );
			$this->iDD = intval( (isset($date['DD']) ? $date['DD'] : date('d') ) );
			$this->iYYYY = intval( (isset($date['YYYY']) ? $date['YYYY'] : date('Y') ) );
		}
		else 
		{
			$this->iYYYY = intval( substr($date, 0, 4) );
			$this->iMM = intval( substr($date, 5, 2) );
			$this->iDD = intval( substr($date, 8, 2) );
		}
		
		if ($this->validateDate())
		{	
			$this->sDate = $this->iYYYY . '-' . str_pad($this->iMM, 2, '0', STR_PAD_LEFT) . '-' . str_pad($this->iDD, 2, '0', STR_PAD_LEFT);
			$this->aMonthEnds = ($this->is_leap() ? static::$aLeapMonthEnds : static::$aRegMonthEnds);
			//echo "@@ Date.class.php:".__line__.": this->oDate = "; print_r($this->oDate); echo "<br />";
		}
	}
	
	
	/********************
	 * Full Literal Month
	*********************/
	public function FullMonth()
	{
		return static::$aFullMonths[$this->iMM];
	}
	
	
	/*******************************
	 * End of Month Date 'YYYY-MM-DD
	********************************/
	public function EndOfMonth()
	{
		return $this->iYYYY . '-' . str_pad($this->iMM, 2, '0', STR_PAD_LEFT) . '-' . 
					($this->is_leap($this->iYYYY) ? static::$aLeapMonthEnds[$this->iMM] : static::$aRegMonthEnds[$this->iMM]);
	}
	
	
	/************************************
	 * End of Last Month Date 'YYYY-MM-DD
	*************************************/
	public function EndOfLastMonth()
	{
		$iLastMonthMM = ($this->iMM == 1 ? 12 : $this->iMM - 1);
		$iLastMonthYYYY = ($this->iMM == 1 ? $this->iYYYY - 1 : $this->iYYYY);
		
		//echo "@@ Date.class.php:".__line__.": iLastMonthMM = $iLastMonthMM ; iLastMonthYYYY = $iLastMonthYYYY \n";
		//die ("@@ Date.class.php:".__line__.": !!! \n"); 
		return $iLastMonthYYYY . '-' . str_pad($iLastMonthMM, 2, '0', STR_PAD_LEFT) . '-' . $this->aMonthEnds[$iLastMonthMM];
	}
	
	
	/************************************
	 * End of Next Month Date 'YYYY-MM-DD
	*************************************/
	public function EndOfNextMonth()
	{
		$iNextMonthMM = ($this->iMM == 12 ? 1 : $this->iMM + 1);
		$iNextMonthYYYY = ($this->iMM == 12 ? $this->iYYYY + 1 : $this->iYYYY);
		
		return $iNextMonthYYYY . '-' . str_pad($iNextMonthMM, 2, '0', STR_PAD_LEFT) . '-' . $this->aMonthEnds[$iNextMonthMM];
	}
	
	
	/*********************************
	 * First of Month Date 'YYYY-MM-DD
	**********************************/
	public function FirstOfMonth()
	{
		return $this->iYYYY . '-' . str_pad($this->iMM, 2, '0', STR_PAD_LEFT) . '-01';
	}
	
	/**************************************
	 * First of Last Month Date 'YYYY-MM-DD
	***************************************/
	public function FirstOfLastMonth()
	{
		$iLastMonthMM = ($this->iMM == 1 ? 12 : $this->iMM - 1);
		$iLastMonthYYYY = ($this->iMM == 1 ? $this->iYYYY - 1 : $this->iYYYY);
			
		return $iLastMonthYYYY . '-' . str_pad($iLastMonthMM, 2, '0', STR_PAD_LEFT) . '-01';
	}
	
	
	/**************************************
	 * First of Next Month Date 'YYYY-MM-DD
	***************************************/
	public function FirstOfNextMonth()
	{
		$iNextMonthMM = ($this->iMM == 12 ? 1 : $this->iMM + 1);
		$iNextMonthYYYY = ($this->iMM == 12 ? $this->iYYYY + 1 : $this->iYYYY);
			
		return $iNextMonthYYYY . '-' . str_pad($iNextMonthMM, 2, '0', STR_PAD_LEFT) . '-01';
	}
	
	
	/***************************
	 * Last Week Date 'YYYY-MM-DD
	****************************/
	public function LastWeek()
	{
		$oLastWeekDate = new datetime($this->sDate);
		$oLastWeekDate->sub(date_interval_create_from_date_string('7 days'));
		
		return $oLastWeekDate->format('Y-m-d');
	}
	
	
	/***************************
	 * Next Day Date 'YYYY-MM-DD
	****************************/
	public function NextDay()
	{
		$oNextDayDate = new datetime($this->sDate);
		$oNextDayDate->add(date_interval_create_from_date_string('1 day'));
		
		return $oNextDayDate->format('Y-m-d');
	}
	
	
	/********************
	 * Check if Leap Year
	*********************/
	public function is_leap($year=0)
	{
		$year = ($year == 0 ? $this->iYYYY : $year);
		return (($year % 4 == 0 AND $year % 100 > 0) OR $year % 400 == 0 ? true : false);	
	}
	
	
	/**********************
	 * Self Test
	***********************/
	public function Test($sBreakChar='<br />')
	{
		echo "@@ CronsRequestHandler:".__line__.": sDate = ", $this->sDate, $sBreakChar;
		echo "@@ CronsRequestHandler:".__line__.": Yesterday = ", $this->Yesterday(), $sBreakChar;
		echo "@@ CronsRequestHandler:".__line__.": NextDay = ", $this->NextDay(), $sBreakChar;
		echo "@@ CronsRequestHandler:".__line__.": FirstOfMonth = ", $this->FirstOfMonth(), $sBreakChar;
		echo "@@ CronsRequestHandler:".__line__.": EndOfMonth = ", $this->EndOfMonth(), $sBreakChar;
		echo "@@ CronsRequestHandler:".__line__.": FirstOfLastMonth = ", $this->FirstOfLastMonth(), $sBreakChar;
		echo "@@ CronsRequestHandler:".__line__.": EndOfLastMonth = ", $this->EndOfLastMonth(), $sBreakChar;
		echo "@@ CronsRequestHandler:".__line__.": FirstOfNextMonth = ", $this->FirstOfNextMonth(), $sBreakChar;
		echo "@@ CronsRequestHandler:".__line__.": EndOfNextMonth = ", $this->EndOfNextMonth(), $sBreakChar;
	}
	
	
	/********************
	 * Today Date
	*********************/
	public function Today()
	{
		return $this->sDate;	
	}
	
	
	/**********************
	 * Validate Date Fields
	***********************/
	public function validateDate()
	{
		if (!is_int($this->iYYYY)  OR  !is_int($this->iMM)  OR  !is_int($this->iDD))
		{
			return false;
		}
		
		if ($this->iYYYY < 1000  OR  $this->iYYYY > 9000)
		{
			return false;
		}
		
		$this->aMonthEnds = ($this->is_leap() ? static::$aLeapMonthEnds : static::$aRegMonthEnds);
		
		if ($this->iMM < 1  OR  $this->iMM > 12)
		{
			return false;
		}
		
		if ($this->iDD < 1  OR  $this->iDD > $this->aMonthEnds[$this->iMM])
		{
			return false;
		}
		
		return true;
	}
	
	
	/****************
	 * Yesterday Date
	*****************/
	public function Yesterday()
	{
		$oYesterdayDate = new datetime($this->sDate);
		$oYesterdayDate->sub(date_interval_create_from_date_string('1 day'));
		
		return $oYesterdayDate->format('Y-m-d');
	}
	
	
}