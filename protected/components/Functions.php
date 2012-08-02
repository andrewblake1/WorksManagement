<?php
/**
 * This is the model class for site wide functions
 */
class Functions extends CApplicationComponent
{
	/**
	* from http://php.net/manual/en/function.strtoupper.php
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
}

?>
