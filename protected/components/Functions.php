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

}
?>