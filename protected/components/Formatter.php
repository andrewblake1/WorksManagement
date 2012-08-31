<?php

class Formatter extends CFormatter
{
	public function formatDate($value) {
		return date('d M, Y', strtotime($value));
	}

	public function formatDatetime($value) {
		return date('d M Y, H:i:s', strtotime($value));
	}

	/**
	 * Convert a string to a bool
	 * @param type $value 
	 * @return boolean 0 or 1
	 */
	public function formatToMysqlBool($value)
	{
		if($value == '')
		{
			return null;
		}
		elseif(stripos($value, 'y') !== false)
		{
			return 1;
		}
		elseif(stripos($value, 'n') !== false)
		{
			return 0;
		}
		
		return 2;
	}

	/**
	 * Convert a client entered date to a mysql date string
	 * @param type $value 
	 * @return mysql formatted date
	 */
	function formatToMysqlDate($value)
	{
		if(!empty($value))
		{
			// need to clear oiut ,'s first as seems to muck it up
			return date('Y-m-d', strtotime(str_replace(',', ' ', $value)));
		}

		return '';
	}

	/**
	 * Convert a client entered date to a mysql daterimw string
	 * @param type $value 
	 * @return mysql formatted date
	 */
	function formatToMysqlDateTime($value)
	{
		if(!empty($value))
		{
			$t =  strtotime(str_replace(',', ' ', $value));
			// need to clear oiut ,'s first as seems to muck it up
			return date('Y-m-d H:i:s', strtotime(str_replace(',', ' ', $value)));
		}

		return '';
	}
}

?>