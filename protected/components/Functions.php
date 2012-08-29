<?php
/**
 * This is the model class for site wide functions
 */
class Functions extends CApplicationComponent
{
	/**
	 * from http://php.net/manual/en/function.strtoupper.php
	 * convert a string to camel or pascal case - pascalcase is uppercase first like class names
	 */
	public function camelize($string, $pascalCase = false) 
	{ 
		$string = str_replace(array('-', '_'), ' ', $string); 
		$string = ucwords($string); 
		$string = str_replace(' ', '', $string);  

		if (!$pascalCase)
		{ 
			return lcfirst($string); 
		}

		return $string; 
	}

	/**
	 * from http://stackoverflow.com/questions/6227061/php-add-underscores-before-capital-letters
	 * convert from camel or pascal case to lowercase with underscore seperators
	 */
	public function uncamelize($string) 
	{ 
		$string = preg_replace('/\B([A-Z])/', '_$1', $string);; 
		$string = strtolower($string);
		
		return $string; 
	}

	/**
	 * convert from camel or pascal case to lowercase with space seperators and capital first letter of each word or just first
	 */
	public function sentencize($string, $firstOnlyToCaptital=TRUE) 
	{ 
		$string = preg_replace('/\B([A-Z])/', ' $1', $string);
		
		if($firstOnlyToCaptital)
		{
			$string = strtolower($string);
			$string = ucfirst($string);
		}
		
		return $string; 
	}

	public function multidimensional_arraySearch(&$array, &$search, $level = 0)
	{
		static $array_keys = array();
		
		// if starting this recursive function
		if(!$level)
		{
			// reset the static variable from the last time this was called
			// could alternatly store the search value and only scan once if the
			// same
			$array_keys = array();
		}

		// loop thru this level
		foreach($array as $key => &$value)
		{
			// if $key is not an array
			if(!is_array($value))
			{
				// do we have a match
				if($search == strval($value))
				{
					$array_keys[$level] = $value;
					break;
				}
			}
			// otherwise key is not int therefore must be array
			else
			{
				// do we have a match
				if($search == strval($key))
				{
					$array_keys[$level] = $key;
					break;
				}
				// otherwise recurse if array
				elseif(is_array($value))
				{
					$this->multidimensional_arraySearch($value, $search, $level + 1);
				}
			}
			// if we have found our answer but havn't yet stored this level
			if(count($array_keys) && !isset($array_keys[$level]))
			{
				// store this level
				$array_keys[$level] = $key;
				break;
			}
		}
		
		// if we are exiting and not recursing
		if(!$level)
		{
			// sort by the arrays ascending so that we know we have the write order in foreach loops
			ksort($array_keys);
		}
		
		// return the array keys array
		return $array_keys;
	} 

}
?>